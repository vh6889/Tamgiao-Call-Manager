<?php
function isLogin() {
	global $_db;

	$userID = isset($_COOKIE['userID']) ? $_COOKIE['userID'] : '';
	$userSESS = isset($_COOKIE['userSESS']) ? $_COOKIE['userSESS'] : '';

	return $_db->query("select * from `core_users` where `id` = '".escape_string($userID)."' and `pass` = '".escape_string($userSESS)."' limit 1 ")->fetch();
}

function signIn($user="",$pass=""){
	global $_db;

	$user = $_db->query("select * from `core_users` where `name` like '".escape_string($user)."' and `pass` = '".escape_string(md5(md5($pass)))."' limit 1 ")->fetch();

	if($user){

		setcookie('userID',$user['id'],time()+3600*24*365);
		setcookie('userSESS',md5(md5($pass)),time()+3600*24*365);
		return true;

	} else
		return false;
}


function signOut($user) {
	global $_db;

	setcookie('userID','');
	setcookie('userSESS','');
	header("Location:?route=signin");
	exit;
}



function curl_sendNotifi($url,$timeout=100){
    $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
}

function _ucfirst($palabra) {
    $newStr = '';
    $match = 0;
    foreach(str_split($palabra) as $k=> $letter) {
        if($match == 0 && preg_match('/^\p{L}*$/', $letter)){
            $newStr .= _ucwords($letter);
            break;
        }else{
            $newStr .= $letter;
        }
    }
    return $newStr.substr($palabra,$k+1);
}

function _ucwords($palabra) {
    return mb_convert_case(mb_strtolower($palabra, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
}
function checkOrders($sql=""){
	global $_db;

	if(!$sql)
		return array();

	$check = $_db->query("select `id`,`status`,`order_phone` from `core_orders` ".$sql." order by `id` desc")->fetch_array();

	$results = array();
	if($check){
		foreach ($check as $order) {
			$results[trim($order['order_phone'])][] = $order;
		}		
	}


	return $results;
}

function checkOrder($order=""){
	global $_db;

	if(!$order)
		return false;

	$check = $_db->query("select `id`,`status` from `core_orders` where `order_phone` like '".$order['order_phone']."' and `id`!='".$order['id']."' order by `id` desc")->fetch_array();

	return $check;
}


function get_time($ptime){
	$etime = time() - $ptime;

    if( $etime < 1 ){
        return 'less than 1 second ago';
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60             =>  'hour',
                60                  =>  'minute',
                1                   =>  'second'
	);
	
    foreach( $a as $secs => $str ){
        $d = $etime / $secs;
        if( $d >= 1 ){
			$r = round( $d );
			if($str=='year'){
				$str_ago = 'năm';
			}elseif($str=='month'){
				$str_ago = 'tháng';
			}elseif($str=='day'){
				$str_ago = 'ngày';
			}elseif($str=='hour'){
				$str_ago = 'giờ';
			}elseif($str=='minute'){
				$str_ago = 'phút';
			}elseif($str=='second'){
				$str_ago = 'giây';
			}
            return $r . ' ' . $str_ago . ' trước';
        }
    }
}


function getBgOrder($type=""){

	$array = array(
					"uncheck"=>"badge-default",
					"calling"=>"badge-secondary",
					"pending"=>"badge-warning",
					"callerror"=>"badge-danger",
					"callback"=>"badge-secondary",
					"shipping"=>"badge-warning",
					"shipdelay"=>"badge-danger",
					"shiperror"=>"badge-danger",
					"rejected"=>"badge-danger",
					"trashed"=>"badge-dark",
					"approved"=>"badge-success",
					"shipfail"=>"badge-warning",
				);

	return isset($array[$type]) ? $array[$type] : 'badge-light';
}




function getAvatar($user_id=""){
	global $_url;

	if(!$user_id)
		return $_url.'/template/avatars/noavatar.png';

	if(file_exists(dirname(dirname(__FILE__)).'/template/avatars/'.$user_id.'.png'))
		return $_url.'/template/avatars/'.$user_id.'.png?t='.time();
	else
		return $_url.'/template/avatars/noavatar.png';
}

function getColorAccount($user=""){
	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user))
		return 'text-danger';
	else if(isBanned($user))
		return 'text-dark';

	$array = array(
				"all_leader" => "text-danger",
				"all_member" => "text-dark-50",
				"call_leader" => "text-danger",
				"call_member" => "text-dark-50",
				"ship_leader" => "text-danger",
				"ship_member" => "text-dark-50",
				"collaborator_leader" => "text-danger",
				"collaborator_member" => "text-dark-50"
			);

	return isset($array[$user['type']]) ? $array[$user['type']] : '#f1f1f1';
}

function getTypeAccount($user=""){
	global $_user;

	$user = $user ? $user : $_user;

	$array = array(
				"admin" => "Administrator",
				"ban" => "Banned",
				"call_leader" => "Caller (Leader)",
				"call_member" => "Caller (Member)",
				"ship_leader" => "Shipper (Leader)",
				"ship_member" => "Shipper (Member)",
				"publisher_leader" => "Publisher (Leader)",
				"publisher_member" => "Publisher (Member)",
				"all_member" => "Caller/Shipper (Member)",
				"all_leader" => "Caller/Shipper (Leader)",
				"collaborator_leader" => "Collaborator (Leader)",
				"collaborator_member" => "Collaborator (Member)"
			);

	if(isAdmin($user))
		return $array['admin'];

	if(isBanned($user))
		return $array['ban'];

	return isset($array[$user['type']]) ? $array[$user['type']] : '';
}

function getDescAccount($user=""){
	global $_user;

	$user = $user ? $user : $_user;


	$array = array(
				"administrator" => "Tài khoản quản trị viên. Được phép sử dụng tất cả các chức năng hiện có.",
				"call_lead" => "Trưởng nhóm nhóm Call. Có nghĩa vụ quản lí Member thuộc phân quyền của mình.",
				"call_member" => "Thành viên thuộc nhóm Call",
				"shipper_lead" => "Trưởng nhóm nhóm Shipper. Có nghĩa vụ quản lí Member thuộc phân quyền của mình.",
				"shipper_member" => "Thành viên thuộc nhóm Shipper"
			);

	if(isBanned($user))
		return 'Tài khoản này đã bị cấm bởi quản trị viên.';

	return isset($array[$user['type']]) ? $array[$user['type']] : '';
}

function getUser($id=""){
	global $_db;


	if($id)
		$user = $_db->query("select * from `core_users` where `id`='".$id."' ")->fetch();
	else 
		return false;

	return $user;
}

function getOffer($id="all"){
	global $_db;

	if(strtolower($id) == "all")
		$data = $_db->query("select * from `core_offers` order by `name` ")->fetch_array();
	else
		$data = $_db->query("select * from `core_offers` where `id`='".escape_string($id)."' limit 1 ")->fetch();

	return $data;
}
function getOfferNewOrderNumber($id="all"){
	global $_db;

	if(strtolower($id) == "all") {
		$temp = $_db->query("select `core_offers`.`id`,COUNT(`core_orders`.`id`) as `new_orders` from `core_offers` LEFT JOIN `core_orders` ON `core_offers`.`id`=`core_orders`.`offer` WHERE `core_orders`.`status`='uncheck' GROUP BY `core_offers`.`name` order by `core_offers`.`name`")->fetch_array();
        if(count($temp)) {
            foreach ($temp as $key => $value) {
                $data[$value['id']] = $value;
            }
        } else {
            $data = $temp;
        }
    } else {
		$data = $_db->query("select `core_offers`.`id`,COUNT(`core_orders`.`id`) as `new_orders` from `core_offers` LEFT JOIN `core_orders` ON `core_offers`.`id`=`core_orders`.`offer` WHERE `core_orders`.`status`='uncheck' AND `core_offers`.`id`='".escape_string($id)."' limit 1 ")->fetch();
    }
	return $data;
}

function getPostback($id){
	global $_db;
	$data = $_db->query("select * from `core_s2s_postback` where `id`='".escape_string($id)."' limit 1 ")->fetch();
	return $data;
}

function getCommentCategory($id="all"){
	global $_db;

	if(strtolower($id) == "all")
		$data = $_db->query("select * from `core_comment_categories` order by `name` ")->fetch_array();
	else
		$data = $_db->query("select * from `core_comment_categories` where `id`='".escape_string($id)."' limit 1 ")->fetch();

	return $data;
}

function getAds($id){
	global $_db;
	$data = $_db->query("select * from `core_ads` where `id`='".escape_string($id)."' limit 1 ")->fetch();	
	return $data;
}

function getAdsByName($name){
	global $_db;
	$data = $_db->query("select * from `core_ads` where `ads`='".escape_string($name)."' limit 1 ")->fetch();	
	return $data;
}

function getBacklist($id="all",$limit=""){
	global $_db;	
	if(strtolower($id) == "all"){
		if($limit==""){
			$data = $_db->query("select * from `core_backlists` order by `phone_number`")->fetch_array();
		}else{
			$data = $_db->query("select * from `core_backlists` order by `phone_number` LIMIT $limit")->fetch_array();
		}
	}else{
		$data = $_db->query("select * from `core_backlists` where `id`='".escape_string($id)."' order by id DESC limit 1 ")->fetch();
	}
	return $data;
}

function getNameOffers($ids=""){
	global $_db;

	$result = $_db->query("select `name` from `core_offers` where `id` IN (".trim(str_replace('|', '', $ids),',').") ")->fetch_array();

	return $result;
}

function getIdsOffer($ids=""){
	global $_db;

	$exp = explode(",", str_replace("|","",trim($ids,",")));

	$result = array();
	foreach ($exp as $id) {
		$offer = getOffer($id);
		if($offer['status'] == "run")
			$result[] = $offer['id'];
	}

	return $result;
}

function getGroup($id="all",$type=""){
	global $_db;

	if(strtolower($id) == "all"){
		$sql = "";
		if($type){
			if(is_array($type))
				$sql = " where `type` in ('".implode("','", $type)."') ";
			else
				$sql = " where `type`='".$type."' ";
		}
		$data = $_db->query("select * from `core_groups` ".$sql." order by `name` ")->fetch_array();
	}
	else
		$data = $_db->query("select * from `core_groups` where `id`='".escape_string($id)."' limit 1 ")->fetch();

	return $data;
}

function typeGroup($type=""){
	$array = array(
				"call" => "Call Group",
				"shipping" => "Shipping Group",
				"collaborator" => "Collaborator Group",
				"publisher" => "Publisher Group",
				"all" => "*Call/Ship Group"
			);
	if(!$type)
		return $array;

	return isset($array[$type]) ? $array[$type] : '';
}

function typeMemGroup($type="",$leader=false){
	$array = array(
				"call" => "call_member",
				"shipping" => "ship_member",
				"collaborator" => "collaborator_member",
				"publisher" => "publisher_member",
				"all" => "all_member"
			);
	if($leader == true)
		$array = array(
			"call" => "call_leader",
			"shipping" => "ship_leader",
			"collaborator" => "collaborator_leader",
			"publisher" => "publisher_leader",
			"all" => "all_leader"
		);
	if(!$type)
		return $array;

	return isset($array[$type]) ? $array[$type] : '';
}

function typeOffer($type=""){
	$array = array(
				"stop" => "Stop",
				"run" => "Running"
			);
	if(!$type)
		return $array;

	return isset($array[$type]) ? $array[$type] : '';
}

function typeCommentCategory($type=""){
	$array = array(
		"0" => "Stop",
		"1" => "Running"
	);
	if(!$type)
	return $array;

	return isset($array[$type]) ? $array[$type] : '';
}

function memberGroup($id="",$count=false){

	global $_db;

	if(!$id)
		return false;

	if($count == false)
		$mem = $_db->query("select * from `core_users` where `group`='".$id."' ")->fetch_array();
	else
		$mem = $_db->query("select `id` from `core_users` where `group`='".$id."' ")->num_rows();
	return $mem;
}

function memberNoGroup($count=false){

	global $_db;

	if($count == false)
		$mem = $_db->query("select * from `core_users` where `group`='' ")->fetch_array();
	else
		$mem = $_db->query("select `id` from `core_users` where `group`='' ")->num_rows();
	return $mem;
}


function getSale($type="",$status="all",$user="",$group="",$price="price_deduct",$typeOrder=0){
	global $_db;

	$where = array(" `typeOrder`='".$typeOrder."' ");
	if($user){
		if($type == "all")
			$where[] = ' ( `user_call`=\''.$user.'\' or `user_ship`=\''.$user.'\' ) ';
		else
			$where[] = ' `'.$type.'`=\''.$user.'\' ';
	}
	if($group)
		$where[] = ' `group`=\''.$group.'\' ';
	if($status){
		if(is_array($status))
			$where[] = ' `status` IN (\''.implode("','", $status).'\') ';
		else {
			if(strtolower($status) != "all")
				$where[] = ' `status`=\''.$status.'\' ';
		}
	}


	$sql = implode(" and ", $where);
	$sql = $sql ? ' where '.$sql.' ' : '';

	$orders = $_db->query("select * from `core_orders` ".$sql)->fetch_array();

	$result = array("sale" => 0, "earning" => 0);
    
    $_offers = getOffer();
    $_offers_deduct = [];
    $_offers_cost = [];
    foreach ($_offers as $off) {
        $_offers_deduct[$off['id']] = $off['price_deduct'];
        $_offers_cost[$off['id']] = $off['cost'];
    }

	foreach ($orders as $o) {
		$result['sale'] = $result['sale'] + $o['number'];
        $o_earning = $o['number']*$o['price_deduct']; // Tổng tiền đơn hàng
        if(isAdmin()) {
            $o_earning -= $o_earning*$_offers_cost[$o['offer']]/100; // Trừ chi phí (sale, telesale,....)
            // $o_earning -= $o['number']*$_offers_deduct[$o['offer']]; // Trừ giá vốn
        } else {
            // TODO: cần thêm tính lũy kế cho caller có đơn hàng bán được nhiều sản phẩm.
			if($o['payout_type']=='percent') {
            	$o_earning = $o_earning*$o['payout_member']/100; // Hiển thị payout cho caller
			}else{
            	$o_earning = $o['number']*$o['payout_member'];
			}
        }
        // echo '</br>'.$o['date'].' - '.$o['number'].' - '.$o_earning;
		$result['earning'] += $o_earning;
	}
	return $result;
}

function getSaleAll($status="all",$user="",$group="",$price="price_deduct",$typeOrder=0){
	return getSale("all",$status,$user,$group,$price,$typeOrder);
}

function getSaleCall($status="all",$user="",$group="",$price="price_deduct",$typeOrder=0){
	return getSale("user_call",$status,$user,$group,$price,$typeOrder);
}

function getSaleShip($status="all",$user="",$group="",$price="price_deduct",$typeOrder=0){
	return getSale("user_ship",$status,$user,$group,$price,$typeOrder);
}

function getOrder($type="",$status="all",$user="",$group="",$count=false,$limit="",$typeOrder=0){
	global $_db;

	$where = array(" `typeOrder`='".$typeOrder."' ");
	if($user) {
		if($type == "all")
			$where[] = ' ( `user_call`=\''.$user.'\' or `user_ship`=\''.$user.'\' ) ';
		else {
			if(is_array($user))
				$where[] = " `".$type."` in ('".implode("','",$user)."') ";
			else
				$where[] = ' `'.$type.'`=\''.$user.'\' ';
		}
	}
	if($group){
		$where[] = ' `group`=\''.$group.'\' ';
	}
	if($status){
		if(is_array($status))
			$where[] = ' `status` IN (\''.implode("','", $status).'\') ';
		else {
			if(strtolower($status) != "all")
				$where[] = ' `status`=\''.$status.'\' ';
		}
	}


	$sql = implode(" and ", $where);
	$sql = $sql ? ' where '.$sql.' ' : '';

	if($limit)
		$sql = $sql .' limit '.$limit;

	if($count == false)
		$orders = $_db->query("select * from `core_orders` ".$sql)->fetch_array();
	else
		$orders = $_db->query("select * from `core_orders` ".$sql)->num_rows();

	return $orders;

}

function getOrderAll($status="all",$user="",$group="",$count=false,$limit="",$typeOrder=0){
	return getOrder("all",$status,$user,$group,$count,$limit,$typeOrder);
}

function getOrderCall($status="all",$user="",$group="",$count=false,$limit="",$typeOrder=0){
	return getOrder("user_call",$status,$user,$group,$count,$limit,$typeOrder);
}

function getOrderShip($status="all",$user="",$group="",$count=false,$limit="",$typeOrder=0){
	return getOrder("user_ship",$status,$user,$group,$count,$limit,$typeOrder);
}

function isBanned($user=""){
	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user))
		return false;

	return $user['active'] == 0 ? true : false;
}

function getCalling($user=""){
	global $_db,$_user;
	$user = $user ? $user : $_user;
	$calling = $_db->query("select * from `core_orders` where `user_call`='".escape_string($user['id'])."' and `status`='calling' ")->fetch();
	return $calling;
}

function infoOrder($id=""){
	global $_db;

	$order = $_db->query("select * from `core_orders` where `id`='".escape_string($id)."' ")->fetch();

	return $order;

}

function infoComment($id=""){
	global $_db;

	$order = $_db->query("SELECT * FROM core_comments WHERE id='".escape_string($id)."'")->fetch();

	return $order;

}

function infoCommentReply($id=""){
	global $_db;

	$comment_reply = $_db->query("SELECT * FROM core_comment_replys WHERE id='".escape_string($id)."'")->fetch();

	return $comment_reply;

}

function infoCommentBad($id=""){
	global $_db;

	$comment_bad = $_db->query("SELECT * FROM core_comment_bads WHERE id='".escape_string($id)."'")->fetch();

	return $comment_bad;
}

function getInfoId($id,$table_name){
	global $_db;
	$info = $_db->query("SELECT * FROM {$table_name} WHERE id='".escape_string($id)."'")->fetch();
	return $info;
}

function infoEmail($id=""){
	global $_db;

	$email_marketing = $_db->query("SELECT * FROM core_email_marketings WHERE id='".escape_string($id)."'")->fetch();

	return $email_marketing;
}

function _e($var){
    $var     = htmlentities(trim($var), ENT_QUOTES, 'UTF-8');
    $replace = array(
        chr(0) => '',
        chr(1) => '',
        chr(2) => '',
        chr(3) => '',
        chr(4) => '',
        chr(5) => '',
        chr(6) => '',
        chr(7) => '',
        chr(8) => '',
        chr(9) => '',
        chr(11) => '',
        chr(12) => '',
        chr(13) => '',
        chr(13) => '',
        chr(14) => '',
        chr(15) => '',
        chr(16) => '',
        chr(17) => '',
        chr(18) => '',
        chr(19) => '',
        chr(20) => '',
        chr(21) => '',
        chr(22) => '',
        chr(23) => '',
        chr(24) => '',
        chr(25) => '',
        chr(26) => '',
        chr(27) => '',
        chr(28) => '',
        chr(29) => '',
        chr(30) => '',
        chr(31) => ''
    );
    return strtr($var, $replace);
}

function addDotNumber($num=""){
	return number_format($num , 0, ',', '.');
}

function bbcode($text="") {
	$find = array(
		'~\[b\](.*?)\[/b\]~s',
		'~\[i\](.*?)\[/i\]~s',
		'~\[u\](.*?)\[/u\]~s',
		'~\[quote\](.*?)\[/quote\]~s',
		'~\[size=(.*?)\](.*?)\[/size\]~s',
		'~\[color=(.*?)\](.*?)\[/color\]~s',
		'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
		'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s'
	);

	$replace = array(
		'<b>$1</b>',
		'<i>$1</i>',
		'<span style="text-decoration:underline;">$1</span>',
		'<pre>$1</'.'pre>',
		'<span style="font-size:$1px;">$2</span>',
		'<span style="color:$1;">$2</span>',
		'<a href="$1">$1</a>',
		'<img src="$1" alt="" />'
	);
	return preg_replace($find,$replace,$text);
}

function pagination($link="", $total=0, $max="", $start=""){
    global $_page,$_start,$_max;

    $num = $max ? $max : $_max;
    $start = $start? $start : $_start;

    $link = preg_replace("#^(.*?)&page=([0-9]+)(.*?)$#si","$1$3",$link);

    $out = "";
    $cuoi = ceil($total / $num);

    if ($cuoi == 0)
        $cuoi = 1;
    $out .= '<div aria-label="Page navigation example mt-3">
  				<ul class="pagination pagination-circle pg-blue justify-content-center">';
    if ($_page != 1) {
    	$out .= '<li class="page-item"><a class="page-link" href="'.$link.'&page=1">First</a></li>';
    } else
    	$out .= '<li class="page-item disabled"><a class="page-link" href="' . $link . '&page=1">First</a></li>';
    
    if ($_page > 1) {
        $out .= '<li class="page-item">
      				<a class="page-link" aria-label="Previous" href="'.$link.'&page='.($_page - 1).'">
        				<span aria-hidden="true">&laquo;</span>
        				<span class="sr-only">Previous</span>
      				</a>
    			</li>';
    } else
    	$out .= '<li class="page-item disabled">
      				<a class="page-link" aria-label="Previous" href="'.$link.'&page='.($_page - 1).'">
        				<span aria-hidden="true">&laquo;</span>
        				<span class="sr-only">Previous</span>
      				</a>
    			</li>';
    
    $begin = $_page - 2;
    if ($begin < 1) {
        $begin = 1;
    }
    $end = $_page + 2;
    if ($end > $cuoi) {
        $end = $cuoi;
    }
    
    for ($i = $begin; $i <= $end; $i++) {
        if ($_page == $i) {
            $out .= '<li class="page-item active"><a class="page-link">'.$i.'</a></li>';
        } else {
            $out .= '<li class="page-item"><a class="page-link" href="'.$link.'&page='.$i.'">'.$i.'</a></li>';
        }
    }
    
    if ($_page < $cuoi) {
    	$out .= '<li class="page-item">
					<a class="page-link" aria-label="Next" href="'.$link.'&page='.($_page + 1).'">
        				<span aria-hidden="true">&raquo;</span>
        				<span class="sr-only">Next</span>
      				</a>
    			</li>';
    } else
    	$out .= '<li class="page-item disabled">
					<a class="page-link" aria-label="Next" href="'.$link.'&page='.($_page + 1).'">
        				<span aria-hidden="true">&raquo;</span>
        				<span class="sr-only">Next</span>
      				</a>
    			</li>';
    
    if ($_page != $cuoi) {
        $out .= '<li class="page-item"><a class="page-link" href="'.$link.'&page='.$cuoi.'">Last</a></li>';
    } else
    	$out .= '<li class="page-item disabled"><a class="page-link" href="'.$link.'&page='.$cuoi.'">Last</a></li>';
    
    $out .= '</ul></div>';
    return $out;
    
}

function isAutoBan($user=""){

	global $_user;

	$user = $user ? $user : $_user;

	if($user['auto_ban'] == 1 && $user['ban_limit'] > 0)
		return true;
	else
		return false;

}

function autoBan(){

	global $_user,$_db;

	if(isAutoBan()){

		$order_total = getOrderCall('all',$_user['id'],$_user['group'],true);

		if($order_total >= $_user['ban_limit']){

			$order_apr = getOrderCall(['approved','shipping','pending'],$_user['id'],$_user['group'],true,$_user['ban_limit']);
			$apr = ($order_apr > 0 ? round($order_apr/($order_total < $_user['ban_limit'] ? $order_total : $_user['ban_limit'])*100,2) : 0);

			if ($apr < $_user['ban_rate']){
				if($_db->exec_query("update `core_users` set `active`='0' where `id`='".$_user['id']."' limit 1 ")){
		            $notifi_title = '<strong class="trigger red lighten-2 text-white">Tài khoản đã bị cấm</strong>';
		            $notifi_text = '<strong>Tài khoản của bạn hiện đã bị cấm bởi: <span class="text-danger">APR rate system</span></strong>.<br> Vui lòng liên hệ với trưởng nhóm hoặc quản trị viên để biết thêm thông tin chi tiết.';
		            addNotification($notifi_title,$notifi_text,$_user['id']);
				}

			}


		} else
			return false;

	} else
		return false;

}

function listAller(){
	global $_db;

	$list = $_db->query("select * from `core_users` where `type` in ('all_leader','all_member') or `adm`='1' ")->fetch_array();

	return $list;

}
function isAdmin($user=""){

	global $_user;

	$type = $user ? $user['adm'] : $_user['adm'];

	if($type == 1)
		return true;
	else
		return false;

}

function isLeader($user="",$check=false){

	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user) && $check == false)
		return true;

	if(in_array($user['type'], array("call_leader","ship_leader","collaborator_leader","all_leader")))
		return true;
	else
		return false;

}

function isAller($user=""){

	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user))
		return true;

	if(in_array($user['type'], array("all_member","all_leader")))
		return true;
	else
		return false;

}

function isCaller($user=""){

	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user))
		return true;

	if(in_array($user['type'], array("call_leader","call_member","all_member","all_leader")))
		return true;
	else
		return false;

}

function isShipper($user=""){

	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user))
		return true;

	if(in_array($user['type'], array("ship_leader","ship_member","all_member","all_leader")))
		return true;
	else
		return false;

}

function isColler($user=""){

	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user))
		return true;

	if(in_array($user['type'], array("collaborator_leader","collaborator_member","all_member","all_leader")))
		return true;
	else
		return false;

}

function isEditOrder($order=""){
	global $_user;

	if(isAdmin())
		return true;

	$permissions = array("pending","rejected","trashed","shipdelay");

	if(!isBanned() && isCaller()){
		$permissions[] = "callback";
		$permissions[] = "callerror";
	}
	if(!isBanned() && isShipper()){
		$permissions[] = "shipping";
		$permissions[] = "shiperror";
	}


	if(in_array($order['status'],$permissions)){

		return true;

	} else {

		return false;

	}
}

function addNotification($title="",$text="",$id_to=""){
	global $_user,$_db;

	if(!$title || !$text || !$id_to)
		return false;



	if(!is_array($id_to)){
		if($_db->exec_query("insert into `core_notifications` set `time`='".time()."',`status`='1',`user_from`='".$_user['id']."',`user_to`='".$id_to."',`title`='".escape_string($title)."',`text`='".escape_string($text)."' "))
			return true;
		else
			return false;
	} else {
		$values = array();
		foreach (array_unique($id_to)  as $id)
			$values[] = "('".time()."','1','".$_user['id']."','".$id."','".escape_string($title)."','".escape_string($text)."')";

		if($_db->exec_query("insert into `core_notifications` (time,status,user_from,user_to,title,text) values ".implode(",", $values)." "))
			return true;
		else
			return false;
	}


}
function check_phone($phone=""){
    $phone = str_replace(array(',','.','=','-','='), '', $phone);

    $phone = preg_replace("#^84([0-9]+)#si", "0$1", $phone);
    $phone = preg_replace("#^016([0-9]+)#si", "03$1", $phone);
    $phone = preg_replace("#^0120([0-9]+)#si", "070$1", $phone);
    $phone = preg_replace("#^0121([0-9]+)#si", "079$1", $phone);
    $phone = preg_replace("#^0122([0-9]+)#si", "077$1", $phone);
    $phone = preg_replace("#^0126([0-9]+)#si", "076$1", $phone);
    $phone = preg_replace("#^0128([0-9]+)#si", "078$1", $phone);
    $phone = preg_replace("#^0123([0-9]+)#si", "083$1", $phone);
    $phone = preg_replace("#^0124([0-9]+)#si", "084$1", $phone);
    $phone = preg_replace("#^0125([0-9]+)#si", "085$1", $phone);
    $phone = preg_replace("#^0127([0-9]+)#si", "081$1", $phone);
    $phone = preg_replace("#^0129([0-9]+)#si", "082$1", $phone);
    $phone = preg_replace("#^0188([0-9]+)#si", "058$1", $phone);
    $phone = preg_replace("#^0186([0-9]+)#si", "056$1", $phone);
    $phone = preg_replace("#^0199([0-9]+)#si", "059$1", $phone);

    if(!is_numeric($phone))
    	return false;

    return strlen($phone) == 10 ? $phone :false;
}

function generateRandomUserKey(){
	$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	return substr(str_shuffle($permitted_chars), 0, 6).substr(time(),-4);
}

function getUkey(){
	global $_user;

	return isset($_user['ukey']) ? $_user['ukey'] : '';
}

function isPublisher($user=""){

	global $_user;

	$user = $user ? $user : $_user;

	if(isAdmin($user))
		return true;

	if(in_array($user['type'], array("publisher_leader","publisher_member","all_member","all_leader")))
		return true;
	else
		return false;

}

function getPublisherOrder($ukey,$status="all",$group="",$count=false,$limit="",$typeOrder=0){
	global $_db;

	$where = array(" `typeOrder`='".$typeOrder."' ");
    if($ukey)
	    $where[] = ' `ukey`=\''.$ukey.'\' ';
	if($group){
		$where[] = ' `group`=\''.$group.'\' ';
	}
	if($status){
		if(is_array($status))
			$where[] = ' `status` IN (\''.implode("','", $status).'\') ';
		else {
			if(strtolower($status) != "all")
				$where[] = ' `status`=\''.$status.'\' ';
		}
	}
	$sql = implode(" and ", $where);
	$sql = $sql ? ' where '.$sql.' ' : '';
	if($limit)
		$sql = $sql .' limit '.$limit;
	if($count == false)
		$orders = $_db->query("select * from `core_orders` ".$sql)->fetch_array();
	else
		$orders = $_db->query("select * from `core_orders` ".$sql)->num_rows();
	return $orders;
}

function getPublisherSale($ukey,$status="all",$group="",$price="price_deduct",$typeOrder=0){
	global $_db;

	$where = array(" `typeOrder`='".$typeOrder."' ");
    if($ukey)
	    $where[] = ' `ukey`=\''.$ukey.'\' ';
	if($group)
		$where[] = ' `group`=\''.$group.'\' ';
	if($status){
		if(is_array($status))
			$where[] = ' `status` IN (\''.implode("','", $status).'\') ';
		else {
			if(strtolower($status) != "all")
				$where[] = ' `status`=\''.$status.'\' ';
		}
	}
	$sql = implode(" and ", $where);
	$sql = $sql ? ' where '.$sql.' ' : '';
	$orders = $_db->query("select * from `core_orders` ".$sql)->fetch_array();
	$result = array("sale" => 0, "earning" => 0);
	foreach ($orders as $o) {
		$result['sale'] = $result['sale'] + $o['number'];
		$result['earning'] = $result['earning'] + ($o['number']*$o[$price]);
	}
	return $result;
}

/**
 * get order amount for user or group
 * 
 * @param array $oids list ids of orders
 * @return int
 **/
function getOrderPayoutAmount(array $oids) {
	global $_db;
    
    $oids   = implode(',',$oids);
    $_order = $_db->query("select * from `core_orders` where id IN ($oids)")->fetch_array();

    return $_order;
}

function getPayoutTypes()
{
    return [
        'fixed' => 'K',
        'percent'=>'%'
    ];
}
?>