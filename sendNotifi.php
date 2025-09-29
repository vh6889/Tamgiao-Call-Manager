<?php

$debug = 0;

error_reporting($debug ? E_ALL & ~E_NOTICE : 0);
ini_set('display_errors', $debug);

$homePath = __DIR__;

include $homePath.'/includes/config.php';

$id  = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$act  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';




switch ($act) {

	case 'uncheck':

		$notifiSet = $_db->query("select `mail` from `core_users` where `notifi`='1' and ( `type` in ('call_leader','call_member','all_leader','all_member') or `adm`='1' ) ")->fetch_fetch();
		$mail_to = array();
		foreach ($notifiSet as $user)
			$mail_to[] = $user['mail'];

		$mail_from = array("inuhaha006@gmail.com"=>"Uncheck Notifi");

		$mail_subject = 'Đơn hàng chờ xử lí #'.$id;
		$mail_body = 'Vừa có 1 đơn hàng đang chờ xử lí!<br><br><a href="'.$_url.'/?route=edit&id='.$id.'&nav='.$act.'">Xem chi tiết đơn hàng #'.$id.'</a>';
		if($_notifi->send($mail_to, $mail_from, $mail_subject, $mail_body))
			echo 'success';
		else
			echo 'failed';
	break;

	case 'pending':

		$notifiSet = $_db->query("select `mail` from `core_users` where `notifi`='1' and ( `type` in ('ship_member','ship_leader','call_leader','call_member','all_leader','all_member') or `adm`='1' ) ")->fetch_fetch();
		$mail_to = array();
		foreach ($notifiSet as $user)
			$mail_to[] = $user['mail'];

		$mail_from = array("inuhaha006@gmail.com"=>"Pending Notifi");

		$mail_subject = 'Đơn hàng chờ ship #'.$id;
		$mail_body = 'Vừa có 1 đơn hàng đang chờ vận chuyển!<br><br><a href="'.$_url.'/?route=edit&id='.$id.'&nav='.$act.'">Xem chi tiết đơn hàng #'.$id.'</a>';
		if($_notifi->send($mail_to, $mail_from, $mail_subject, $mail_body))
			echo 'success';
		else
			echo 'failed';
	break;

	case 'shipping':

		$notifiSet = $_db->query("select `mail` from `core_users` where `notifi`='1' and `adm`='1' ) ")->fetch_fetch();
		$mail_to = array();
		foreach ($notifiSet as $user)
			$mail_to[] = $user['mail'];

		$mail_from = array("inuhaha006@gmail.com"=>"Shipping Notifi");

		$mail_subject = 'Đơn hàng vận chuyển #'.$id;
		$mail_body = 'Vừa có 1 đơn hàng được vận chuyển!<br><br><a href="'.$_url.'/?route=edit&id='.$id.'&nav='.$act.'">Xem chi tiết đơn hàng #'.$id.'</a>';
		if($_notifi->send($mail_to, $mail_from, $mail_subject, $mail_body))
			echo 'success';
		else
			echo 'failed';

	break;
}


?>