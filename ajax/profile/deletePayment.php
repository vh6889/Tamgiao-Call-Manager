<?php


if(empty($_user['payment_name']) && empty($_user['payment_bank']) && empty($_user['payment_branch']) && empty($_user['payment_number']))
	exit('{"status":200,"message":"Xóa địa chỉ thanh toán thành công!"}');

if($_db->exec_query("update `core_users` set `payment_name`='',`payment_bank`='',`payment_number`='',`payment_branch`='' where `id`='".$_user['id']."' "))
	exit('{"status":200,"message":"Xóa địa chỉ thanh toán thành công!"}');
else
	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');


?>