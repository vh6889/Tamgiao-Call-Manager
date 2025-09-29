<?php

$debug = 0;

error_reporting($debug ? E_ALL & ~E_NOTICE : 0);
ini_set('display_errors', $debug);

$homePath = __DIR__;

include $homePath.'/includes/config.php';


$keyword  = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';


$search = $_db->query("select * from `core_orders` where `order_name` like '%".escape_string($keyword)."%' or `order_phone` like '%".escape_string($keyword)."%'")->fetch_array();

if($search){
    foreach ($search as $arr) {
        echo '<div class="search_result"><b>'.$arr['type'].': </b><a href="'.$_url.'/?route=edit&id='.$arr['id'].'&nav='.$arr['type'].'">đơn hàng #'.$arr['id'].'</a></div>';
    }
} else {
    echo '<div class="search_not_forund">No result is found</div>';
}


?>