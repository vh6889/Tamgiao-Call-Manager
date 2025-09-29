<?php
$result = [];
$ts = isset($_REQUEST['ts']) ? strtotime(str_replace("/", "-", trim($_REQUEST['ts'])) . " GMT+7 00:00:00") : strtotime(date('Y-m-d 0:0:0') . " -1 month");
$te = isset($_REQUEST['te']) ?  strtotime(str_replace("/", "-", trim($_REQUEST['te'])) . " GMT+7 23:59:59") : strtotime(date('Y-m-d 23:59:59'));

$crawlUrl = "http://www.vnpost.vn/vi-vn/dinh-vi/buu-pham";  // VNPost tracking link
$vnpostCookie = $homePath . '/tools/vnpost/vnpost_cookie.txt';   // VNPost cookie file
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


$orders = $_db->query("SELECT * FROM `core_orders` WHERE `parcel_code`!='' AND `ship_time`>='$ts' AND `ship_time`<='$te' AND `status` IN ('shipfail','shipping')")->fetch_array();
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
        $updateStatus = [];
        foreach ($resultRequest as $parcelCode => $od) {
            if (isset($_orders[$parcelCode]) && $od['status'] != $_orders[$parcelCode]['status']) {
                $_orders[$parcelCode]['new_status'] = $od['status'];
                $updateOrder[] = $_orders[$parcelCode];
                $updateStatus[$_orders[$parcelCode]['id']] = $_orders[$parcelCode];
            }
        }
        if (count($updateOrder)) {
            $result['html'] = renderCrawlTableData($updateOrder);
            $result['status'] = $updateStatus;
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

function renderCrawlTableData($orders)
{
    $html = '';

    foreach ($orders as $order) {
        $html .= '<tr id="order-' . $order['id'] . '">';
        $html .= '<td class="text-center"><input type="checkbox" class="form-check-input" id="order_' . $order['id'] . '" value="' . $order['id'] . '"><label class="form-check-label px-2" for="order_' . $order['id'] . '"></label><span onclick="updateOrder(' . $order['id'] . ')" title="Update order" class="ml-2 btn btn-info btn-sm waves-effect waves-light"><i class="fas fa-sync ml-1"></i></span><a href="?route=editOrder&id=' . $order['id'] . '"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a></td>';
        $html .= '<td class="text-center">Đơn hàng #<strong>' . $order['id'] . '</strong></td>';
        $html .= '<td class="text-center">' . $order['parcel_code'] . '</td>';
        $html .= '<td class="text-center">' . $order['post_office'] . '</td>';
        $html .= '<td class="text-center">' . $order['offer_name'] . '</td>';
        $html .= '<td class="text-center">' . date('Y/m/d H:i', $order['time']) . '</td>';
        $html .= '<td class="text-center">' . date('Y/m/d H:i', $order['ship_time']) . '</td>';
        $html .= '<td class="text-center">' . $order['status'] . '</td>';
        $html .= '<td class="text-center">' . $order['new_status'] . '</td>';
        $html .= '<td class="text-center">' . _ucwords($order['order_name']) . '</td>';
        $html .= '<td class="text-center">' . $order['order_phone'] . '</td>';
        $html .= '<td class="text-center">' . nl2br(_ucwords($order['note'])) . '</td>';
        $html .= '<td class="text-center">' . nl2br(_ucwords($order['order_info'])) . '</td>';
        $html .= '</tr>';
    }

    return $html;
}

print_r(json_encode($result));
