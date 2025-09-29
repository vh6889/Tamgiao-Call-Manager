<?php

$_title = '';


  if(!isAller()){

	$_g = getGroup($_user['group']);
	$_o = getIdsOffer($_g['offers']);
	$_sql_offer = " and `offer` in ('".implode("','",$_o)."') ";

    if(isCaller() || isColler()){
		$_count_uncheck = $_db->query("select `id` from `core_orders` where `status` = 'uncheck' and `user_call`=''".$_sql_offer)->num_rows();
		$_count_calling = $_db->query("select `id` from `core_orders` where `status` = 'calling' and `user_call`='".$_user['id']."'".$_sql_offer)->num_rows();
		$_count_allcalling = $_db->query("select `id` from `core_orders` where `group`='".$_user['id']."' and `status` = 'calling' and `user_call`!=''  and `user_call`!='".$_user['id']."' ")->num_rows();


		//$_count_callback = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'callback' ")->num_rows();
		$_count_callerror = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'callerror' ")->num_rows();
		$_count_pending = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'pending' ")->num_rows();
		$_count_shipping = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'shipping' ")->num_rows();	
		$_count_shipping_fail = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'shipfail' ")->num_rows();		
		$_count_shipdelay = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'shipdelay' ")->num_rows();
		$_count_shiperror = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'shiperror' ")->num_rows();
		$_count_rejected = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'rejected' ")->num_rows();
		$_count_trashed = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'trashed' ")->num_rows();
		$_count_approved = $_db->query("select `id` from `core_orders` where `user_call`='".$_user['id']."' and `status` = 'approved' ")->num_rows();

    } elseif (isPublisher()) {
        $_count_postback_success = $_db->query("SELECT id FROM core_s2s_postback WHERE `response_code`=200 and `ukey`='".$_user['ukey']."'")->num_rows();
        $_count_postback_fail = $_db->query("SELECT id FROM core_s2s_postback WHERE `response_code`!=200 and `ukey`='".$_user['ukey']."'")->num_rows();

		$_count_callerror = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'callerror' ")->num_rows();
		$_count_pending = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'pending' ")->num_rows();
		$_count_shipping = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'shipping' ")->num_rows();	
		$_count_shipping_fail = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'shipfail' ")->num_rows();		
		$_count_shipdelay = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'shipdelay' ")->num_rows();
		$_count_shiperror = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'shiperror' ")->num_rows();
		$_count_rejected = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'rejected' ")->num_rows();
		$_count_trashed = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'trashed' ")->num_rows();
		$_count_approved = $_db->query("select `id` from `core_orders` where `ukey`='".$_user['ukey']."' and `status` = 'approved' ")->num_rows();

    } else {

		$_count_newpending = $_db->query("select `id` from `core_orders` where `status` = 'pending' and `user_ship`='' ".$_sql_offer)->num_rows();
		$_count_newshipdelay = $_db->query("select `id` from `core_orders` where `status` = 'shipdelay' and `user_ship`='' ".$_sql_offer)->num_rows();

		$_count_pending = $_db->query("select `id` from `core_orders` where `user_ship`='".$_user['id']."' and `status` = 'pending' ")->num_rows();
		$_count_shipping = $_db->query("select `id` from `core_orders` where `user_ship`='".$_user['id']."' and `status` = 'shipping' ")->num_rows();	
		$_count_shipping_fail = $_db->query("select `id` from `core_orders` where `user_ship`='".$_user['id']."' and `status` = 'shipfail' ")->num_rows();	
		$_count_shipdelay = $_db->query("select `id` from `core_orders` where `user_ship`='".$_user['id']."' and `status` = 'shipdelay' ")->num_rows();
		$_count_shiperror = $_db->query("select `id` from `core_orders` where `user_ship`='".$_user['id']."' and `status` = 'shiperror' ")->num_rows();
		$_count_approved = $_db->query("select `id` from `core_orders` where `user_ship`='".$_user['id']."' and `status` = 'approved' ")->num_rows();
    }


  }
  else {
	//by tieu_vu
	$_count_postback_success = $_db->query("SELECT id FROM core_s2s_postback WHERE 	response_code=200")->num_rows();
	$_count_postback_fail = $_db->query("SELECT id FROM core_s2s_postback WHERE response_code!=200")->num_rows();
	$_count_comments = $_db->query("SELECT id FROM core_comments WHERE status='0'")->num_rows();
	$_count_comment_accept = $_db->query("SELECT id FROM core_comments WHERE status='1'")->num_rows();
	$_count_comment_noaccept = $_db->query("SELECT id FROM core_comments WHERE status='2'")->num_rows();
	$_count_comment_reply = $_db->query("SELECT id FROM core_comment_replys WHERE status='0'")->num_rows();
	$_count_comment_reply1 = $_db->query("SELECT id FROM core_comment_replys WHERE status='1'")->num_rows();
	$_count_comment_reply2 = $_db->query("SELECT id FROM core_comment_replys WHERE status='2'")->num_rows();
	$_count_comment_bad = $_db->query("SELECT id FROM core_comment_bads")->num_rows();
	$_count_email_marketing = $_db->query("SELECT id FROM core_email_marketings")->num_rows();
	$_count_category_1 = $_db->query("SELECT id FROM core_comment_categories1")->num_rows();
	$_count_category_2 = $_db->query("SELECT id FROM core_comment_categories2")->num_rows();
	$_count_comment_landing = $_db->query("SELECT id FROM core_comment_landings")->num_rows();
	$_count_email_new = $_db->query("SELECT id FROM core_email_marketings WHERE status='0'")->num_rows();
	$_count_email_new1 = $_db->query("SELECT id FROM core_email_marketings WHERE status='1'")->num_rows();
	$_count_email_new2 = $_db->query("SELECT id FROM core_email_marketings WHERE status='2'")->num_rows();
	//end by tieu_vu

	$_count_uncheck = $_db->query("select `id` from `core_orders` where `status` = 'uncheck' and `user_call`=''")->num_rows();
	$_count_calling = $_db->query("select `id` from `core_orders` where `status` = 'calling' and `user_call`='".$_user['id']."'")->num_rows();
	$_count_allcalling = $_db->query("select `id` from `core_orders` where `status` = 'calling' and `user_call`!='' and `user_call`!='".$_user['id']."' ")->num_rows();
	$_count_newpending = $_db->query("select `id` from `core_orders` where `status` = 'pending' and `user_ship`=''  ")->num_rows();
	$_count_newshipdelay = $_db->query("select `id` from `core_orders` where `status` = 'shipdelay' and `user_ship`='' ")->num_rows();

	//$_count_callback = $_db->query("select `id` from `core_orders` where `status` = 'callback' ")->num_rows();
	$_count_callerror = $_db->query("select `id` from `core_orders` where `status` = 'callerror' ")->num_rows();
	$_count_pending = $_db->query("select `id` from `core_orders` where `status` = 'pending' and `user_ship`!='' ")->num_rows();
	$_count_shipping = $_db->query("select `id` from `core_orders` where `status` = 'shipping' ")->num_rows();
	$_count_shipping_fail = $_db->query("select `id` from `core_orders` where `status` = 'shipfail' ")->num_rows();
	$_count_shipdelay = $_db->query("select `id` from `core_orders` where `status` = 'shipdelay' and `user_ship`!='' ")->num_rows();
	$_count_shiperror = $_db->query("select `id` from `core_orders` where `status` = 'shiperror' ")->num_rows();
	$_count_rejected = $_db->query("select `id` from `core_orders` where `status` = 'rejected' ")->num_rows();
	$_count_trashed = $_db->query("select `id` from `core_orders` where `status` = 'trashed' ")->num_rows();
	$_count_approved = $_db->query("select `id` from `core_orders` where `status` = 'approved' ")->num_rows();
  }

$_notifications = $_db->query("select * from `core_notifications` where `status` = '1' and `user_to` = '".$_user['id']."' order by id DESC ")->fetch_array();

//by tieu_vu
if($_route == 'commentCategoryOne'){
	$_title = 'Comment Category Sub 1';
	$_nav   = 'category';
}

if($_route == 'statistics-postback'){
	$_title = 'Statistics Postback';
	$_nav   = 'statistics';
}

if($_route == 'statistics-province'){
	$_title = 'Statistics Province';
	$_nav   = 'statistics';
}

if($_route == 'importOrder'){
	$_title = 'Import Order';
	$_nav   = 'order';
}

if($_route == 'postback'){
	$_title = 'S2S Postback';
	$_nav   = 'postback';
}

if($_route == 'commentLanding'){
	$_title = 'Comment Landing Page';
	$_nav   = 'category';
}

if($_route == 'addConmmentLanding'){
	$_title = 'Cập nhật Comment Landing Page';
	$_nav   = 'category';
}

if($_route == 'editCommtentCategoryOne'){
	$_title = 'Edit Comment Category Sub 1';
	$_nav   = 'category';
}

if($_route == 'commentCategoryTwo'){
	$_title = 'Comment Category sub 2';
	$_nav   = 'category';	
}

if($_route == 'editCommtentCategoryTwo'){
	$_title = 'Update Comment Category sub 2';
	$_nav   = 'category';	
}

if($_route == 'comment'){

	$_title = 'Danh sách Comment';
	$_nav   = 'category';
	
}

if($_route == 'addCommentReply')
{
	$_title = 'Cập nhật Comment Reply';
	$_nav   = 'category';	
}

if($_route == 'commentBad')
{
	$_title = 'Danh sách báo xấu';
	$_nav   = 'category';	
}

if($_route == 'addCommentBad')
{
	$_title = 'Cập nhật báo xấu';
	$_nav   = 'category';		
}

if($_route == 'commentReply'){

	$_title = 'Danh sách Trả lời Comment';
	$_nav   = 'category';
	
}

if($_route == 'editComment'){

	$_title = 'Edit Comment';
	$_nav   = 'category';
	
}

if($_route == 'addComment'){

	$_title = 'Thêm mới Comment';
	$_nav   = 'category';
	
}

if($_route == 'emailMarketing'){

	$_title = 'Danh sách Email';
	$_nav   = 'category';
	
}

if($_route == 'addEmailMarketing'){

	$_title = 'Cập nhật Email';
	$_nav   = 'category';
	
}

//end by tieu_vu

if($_route == 'signin'){

	$_title = 'SignIn - Call Center Manager';


	$user = isset($_POST['username']) ? trim($_POST['username']) : '';
	$pass = isset($_POST['password']) ? trim($_POST['password']) : '';
}



if($_route == 'statistics'){

	$_title = 'Statistics - Call Center Manager';
	$_nav   = 'statistics';
	
}


if($_route == 'newOrder'){

	$_title = 'All New Order - Call Center Manager';
	$_nav   = 'newOrder';
	
}


if($_route == 'addOrder'){

	$_title = 'Add Order - Call Center Manager';
	$_nav   = 'addOrder';
	
}


if($_route == 'newPending'){

	$_title = 'All Pending Order - Call Center Manager';
	$_nav   = 'newPending';
	
}

if($_route == 'landing'){

	$_title = 'Landing Statistics - Call Center Manager';
	$_nav   = 'landing';
	
}

if($_route == 'calling'){

	$_title = 'Order Calling - Call Center Manager';
	$_nav   = 'calling';
	
}

if($_route == 'allCalling'){

	$_title = 'Member\'s Call - Call Center Manager';
	$_nav   = 'allCalling';
	
}

if($_route == 'newDelay'){

	$_title = 'All Delay Shipping Orders - Call Center Manager';
	$_nav   = 'calling';
	
}


if($_route == 'groupPayment'){

	$_title = 'Group payment history - Call Center Manager';
	$_nav   = 'group';
	$_nav_li = 'payment';
	
}


if($_route == 'groupStats'){

	$_title = 'Group statistics - Call Center Manager';
	$_nav   = 'group';
	$_nav_li = 'groupStats';
	
}

if($_route == 'group'){

	$_title = 'Group members - Call Center Manager';
	$_nav   = 'group';
	$_nav_li = 'members';
	
}

if($_route == 'editNote'){

	$_title = 'Edit Note Group - Call Center Manager';
	$_nav   = 'group';
	
}

if($_route == 'editOrder'){

	$_title = 'Edit Order #'.$_id.' - Call Center Manager';
	$_nav   = '';
	
}

if($_route == 'profile'){

	$_title = 'Account Settings - Call Center Manager';
	$_nav   = 'profile';
	
}

if($_route == 'payments'){

	$_title = 'Payments History - Call Center Manager';
	$_nav   = 'payments';
	
}


if($_route == 'order'){

	$_nav   = 'order';
	$_nav_li = strtolower($_type);

	switch ($_type) {

		case 'calling':
			$_title = 'All Calling Order - Call Center Manager';
			$_nav   = 'calling';
			$_nav_li = '';
		break;

		case 'callError':
			$_title = 'Order Call Error - Call Center Manager';
		break;

		case 'pending':
			$_title = 'Order Pending - Call Center Manager';
		break;

		case 'callBack':
			$_title = 'Call Back - Call Center Manager';
		break;

		case 'shipping':
			$_title = 'Order Shipping - Call Center Manager';
		break;

		case 'shipDelay':
			$_title = 'Ship Delay - Call Center Manager';
		break;

		case 'shipError':
			$_title = 'Order Shipping Error - Call Center Manager';
		break;

		case 'reject':
			$_title = 'Order Rejected - Call Center Manager';
		break;

		case 'trash':
			$_title = 'Order Trashes - Call Center Manager';
		break;

		case 'approve':
			$_title = 'Order Approved - Call Center Manager';
		break;

		default:
			$_title = 'Order Management - Call Center Manager';
		break;
	}
}

if($_route == 'settings'){

	$_nav   = 'settings';
	$_nav_li = $_type;

	switch ($_type) {
		case 'backlist':
			$_title = 'Backlist Management | Admin Settings - Call Center Manager';
		break;

		case 'offers':
			$_title = 'Offers Management | Admin Settings - Call Center Manager';
		break;

		case 'groups':
			$_title = 'Groups Management | Admin Settings - Call Center Manager';
		break;

		case 'payment':
			$_title = 'Payment | Admin Settings - Call Center Manager';
		break;

		case 'viewPayment':
			$_title = 'Invoice #'.$_id.' | Payment - Call Center Manager';
			$_nav_li = 'payment';
		break;

		default:
			$_title = 'Admin Settings - Call Center Manager';
		break;
	}
}


if($_route == 'notifications'){
	$_title = 'Notifications - Call Center Manager';
}


if(!$_nav)
	$_nav = $_route;
if(!$_nav_li)
	$_nav_li = $_route;


?>