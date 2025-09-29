<?php
$rootPath = dirname(__DIR__, 2);

include $rootPath . '/includes/config.php';                  // loading api config data
$vnpostCookie = __DIR__ . '/vnpost_cookie.txt';             // VNPost cookie file
$crawlUrl = "http://www.vnpost.vn/vi-vn/dinh-vi/buu-pham";  // VNPost tracking link
$tableCols = [ // columns of table on VNPost page
    'parcel_code',
    'create_date',
    'status',
    'ship_date',
    'post_office',
    'payout_type',
    'payout_status'
];
$orderStatus = [ // order's statuses array for matched status
    'chưa có thông tin phát' => 'shipping',
    'phát thành công'        => 'approved',
    'phát không thành công'  => 'shipfail',
    'phát hoàn thành công'   => 'shiperror',
];

$_startTime = strtotime(date('Y-m-d 0:0:0') . " -45 days"); // will be checking order in -45days ago.

$orders = $_db->query("SELECT * FROM `core_orders` WHERE `parcel_code`!='' AND `ship_time`>='$_startTime' AND `status` IN ('shipfail','shipping')")->fetch_array();
if ($orders) {
    $key[0] = "EH000000001VN";
    $resultRequest = [];
    $_orders = [];
    // sorted orders data for below step
    foreach ($orders as $value) {
        $key[$value['id']] = trim($value['parcel_code']);
        $_orders[trim($value['parcel_code'])] = $value;
    }

    foreach (array_chunk($key, 5, true) as $val) {
        $vnpHtml = loadingVnpostData($crawlUrl, $vnpostCookie, implode(',', $val)); // loading data from VNPost using cURL
        $vnpHtml = mb_convert_encoding($vnpHtml, 'HTML-ENTITIES', "UTF-8");

        /* Parsing response data */
        $dom = new domDocument;
        @$dom->loadHTML($vnpHtml);
        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $row) {
            foreach ($row->getElementsByTagName('tr') as $tr) {
                $tdList = $tr->getElementsByTagName('td');
                if ($tdList->length) {
                    $tdCode = trim($tdList->item(0)->nodeValue); // get `parcel_code` in a row, 0 is default index of this column.
                    $tdVals = [];
                    foreach ($tableCols as $k => $val) {
                        $tdVals[$val] = trim($tdList->item($k)->nodeValue);
                    }
                    if ($tdCode && $tdVals['status'] && key_exists(mb_strtolower($tdVals['status'], 'UTF-8'), $orderStatus)) {
                        $tdVals['status'] = $orderStatus[mb_strtolower($tdVals['status'], 'UTF-8')];
                        $resultRequest[$tdCode] = $tdVals;
                    }
                }
            }
        }
    }

    if (count($resultRequest)) {
        $updateOrder = [];
        $skipOrder = [];
        foreach ($resultRequest as $parcelCode => $od) {
            if($parcelCode==$key[0]) continue;
            if (isset($_orders[$parcelCode]) && $od['status'] != $_orders[$parcelCode]['status']) {
                $_orders[$parcelCode]['new_status'] = $od['status'];
                $updateOrder[$_orders[$parcelCode]['id']] = $_orders[$parcelCode];
            }else{
                $skipOrder[] = '#'.$_orders[$parcelCode]['id'].', code: '.$parcelCode.', order status: '.$_orders[$parcelCode]['status'].', VNPOST status: '.$od['status'];
            }
        }
        
        $fp = fopen(__DIR__ . '/order_cannot_update_status.txt', 'w') or die("Can't create file");
        fwrite($fp,"ID, Parcel Code, Current Status, VNPOST Status\n");
        fwrite($fp, implode("\n",$skipOrder));
        fclose($fp);

        if (count($updateOrder)) {
            updateOrderStatus($updateOrder);
            @file_put_contents(__DIR__ . '/log/' . date('d-m-Y', time()) . '.txt', "[".date('d-m-Y H:i:s', time())."]: Update ". count($updateOrder)." order successfully!!!\n", FILE_APPEND);
            echo 'Update '. count($updateOrder).' order successfully!!!';
        }
    }
}



function loadingVnpostData($url, $cookieFile, $key)
{
    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, $url);
    curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch1, CURLOPT_COOKIEJAR, $cookieFile); // Get VNPOST cookie for pass captcha in next step
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch1);
    curl_close($ch1);

    $url .= "?key=$key";
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $url);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookieFile); // Read VNPOST cookie pass captcha
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch2);
    curl_close($ch2);

    return $response;
}

function updateOrderStatus($orders)
{
    global $_db;

    $values_orders = array();
    $values_users = array();
    $values_groups = array();

    foreach ($orders as $oid => $_od) {
        $status = $_od['new_status'];

        /* Tính payout cho user call và group tương ứng */
        $_group_payout_amount = $_od['payout_leader'];
        $_user_payout_amount  = ($_group_payout_amount < $_od['payout_member']) ? $_group_payout_amount : $_od['payout_member'];
        if ($_od['payout_type'] == 'percent') {
            /* percent type */
            $_order_amount  = $_od['price'] * $_od['number'];
            $payout         = $_order_amount * $_user_payout_amount / 100;
            $payout_group   = $_order_amount * $_group_payout_amount / 100;
        } else {
            /* fixed type */
            $payout         = $_od['number'] * $_user_payout_amount;
            $payout_group   = $_od['number'] * $_group_payout_amount;
        }
        $pendingUserAmount  = 0;
        $pendingGroupAmount = 0;
        $approveUserAmount  = 0;
        $approveGroupAmount = 0;
        $deductUserAmount   = 0;
        $deductGroupAmount  = 0;

        /* Calculate Hold amount */
        if ($_od['r_hold'] <= 0 && in_array($status, ['pending', 'shipping', 'shipdelay'])) {
            $pendingUserAmount += $payout;
            $pendingGroupAmount += $payout_group;
        } elseif ($_od['r_hold'] > 0) {
            $pendingUserAmount -= $payout;
            $pendingGroupAmount -= $payout_group;
        }

        /* Calculate Approve amount */
        if ($_od['r_approve'] <= 0 && $status == "approved") {
            /* Update `price_bonus` for order `ukey` */
            $offer_bonus = $_db->query('select `price_bonus` from `core_offers` where `id`=' . $_od['offer'])->fetch();
            $price_bonus = $offer_bonus['price_bonus'] *  $_od['number'];
            if ($price_bonus) {
                $_db->exec_query("update `core_users` set `revenue_approve`=`revenue_approve`+$price_bonus where `ukey`='" . $_od['ukey'] . "'");
            }
            $approveUserAmount += $payout;
            $approveGroupAmount += $payout_group;
        } elseif ($_od['r_approve'] > 0) {
            $approveUserAmount -= $payout;
            $approveGroupAmount -= $payout_group;
        }

        /* Calculate Deduct amount */
        if ($_od['r_deduct'] <= 0 && $status == "shiperror") {
            $deductUserAmount += $_od['deduct_member'];
            $deductGroupAmount += $_od['deduct_member'];
        } elseif ($_od['r_deduct'] > 0) {
            $deductUserAmount -= $_od['deduct_member'];
            $deductGroupAmount -= $_od['deduct_member'];
        }

        $values_users[$_od['user_call']]['pending'] = (isset($values_users[$_od['user_call']]['pending'])) ? $values_users[$_od['user_call']]['pending'] + $pendingUserAmount : $pendingUserAmount;
        $values_groups[$_od['group']]['pending'] = (isset($values_groups[$_od['group']]['pending'])) ? $values_groups[$_od['group']]['pending'] + $pendingGroupAmount : $pendingGroupAmount;
        $values_users[$_od['user_call']]['approve'] = (isset($values_users[$_od['user_call']]['approve'])) ? $values_users[$_od['user_call']]['approve'] + $approveUserAmount : $approveUserAmount;
        $values_groups[$_od['group']]['approve'] = (isset($values_groups[$_od['group']]['approve'])) ? $values_groups[$_od['group']]['approve'] + $approveGroupAmount : $approveGroupAmount;
        $values_users[$_od['user_call']]['deduct'] = (isset($values_users[$_od['user_call']]['deduct'])) ? $values_users[$_od['user_call']]['deduct'] + $deductUserAmount : $deductUserAmount;
        $values_groups[$_od['group']]['deduct'] = (isset($values_groups[$_od['group']]['deduct'])) ? $values_groups[$_od['group']]['deduct'] + $deductGroupAmount : $deductGroupAmount;

        $rHold    = in_array($status, ['pending', 'shipping', 'shipdelay']) ? 1 : 0;
        $rApprove = $status == "approved" ? 1 : 0;
        $rDeduct  = $status == "shiperror" ? 1 : 0;
        $values_orders[$_od['id']] =  " ('" . $_od['id'] . "','$status',$rHold,$rApprove,$rDeduct) ";
    }
    $_db->exec_query("INSERT INTO `core_orders` (id,status,r_hold,r_approve,r_deduct) VALUES " . implode(",", $values_orders) . " ON DUPLICATE KEY UPDATE status = VALUES(status),r_hold = VALUES(r_hold), r_approve = VALUES(r_approve),r_deduct = VALUES(r_deduct)");
    foreach ($values_users as $key => $value) {
        $_db->exec_query("UPDATE `core_users` SET `revenue_pending`=`revenue_pending`+(" . $value['pending'] . "), `revenue_approve`=`revenue_approve`+(" . $value['approve'] . "), `revenue_deduct`=`revenue_deduct`+(" . $value['deduct'] . ") WHERE `id`=$key");
    }
    foreach ($values_groups as $key => $value) {
        $_db->exec_query("UPDATE `core_groups` SET `revenue_pending`=`revenue_pending`+(" . $value['pending'] . "), `revenue_approve`=`revenue_approve`+(" . $value['approve'] . "), `revenue_deduct`=`revenue_deduct`+(" . $value['deduct'] . ") WHERE `id`=$key");
    }
}
