<?php

if (isset($_REQUEST['orders']) && count($_REQUEST['orders'])) {
    $orders = $_REQUEST['orders'];
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
        $_db->exec_query("UPDATE `core_users` SET `revenue_pending`=`revenue_pending`+(".$value['pending']."), `revenue_approve`=`revenue_approve`+(".$value['approve']."), `revenue_deduct`=`revenue_deduct`+(".$value['deduct'].") WHERE `id`=$key");
    }
    foreach ($values_groups as $key => $value) {
        $_db->exec_query("UPDATE `core_groups` SET `revenue_pending`=`revenue_pending`+(".$value['pending']."), `revenue_approve`=`revenue_approve`+(".$value['approve']."), `revenue_deduct`=`revenue_deduct`+(".$value['deduct'].") WHERE `id`=$key");
    }
    exit('{"message": "Update order success!!!"}');
} else {
    exit('{"message": "Empty order info!!!"}');
}
