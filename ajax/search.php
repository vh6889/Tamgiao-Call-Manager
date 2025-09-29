<?php


$keyword  = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';


if(!preg_match("#^\#([0-9]+)#si", $keyword))
	$search = $_db->query("select * from `core_orders` where `order_name` like '%".escape_string($keyword)."%' or `order_phone` like '%".escape_string($keyword)."%'")->fetch_array();
else {
	$search = $_db->query("select * from `core_orders` where `id` like '".escape_string(str_replace("#","",$keyword))."%' ")->fetch_array();
}

if($search){
    foreach ($search as $arr) {
        echo '<div class="search_result"><a target="_blank" href="'.$_url.'/?route=editOrder&id='.$arr['id'].'">Đơn hàng #'.$arr['id'].'</a> <b>('.$arr['status'].')</b></div>';
    }
} else {
    echo '<div class="search_not_forund">No result is found</div>';
}


?>