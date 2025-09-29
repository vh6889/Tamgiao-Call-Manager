<?php

if(isset($_FILES['file'])){

	if($_FILES['file']['error'] > 0)
		exit('{"status":403,"message":"Không thể tải lên hình ảnh!"}');

	$mimeType = array(
					'image/png',
					'image/jpeg',
					'image/gif',
					'image/jpg'
				);

	if(!in_array($_FILES['file']['type'], $mimeType))
		exit('{"status":403,"message":"Định dạng hình ảnh không chính xác!"}');

    if(move_uploaded_file($_FILES['file']['tmp_name'], dirname(dirname(dirname(__FILE__))).'/template/avatars/'.$_user['id'].'.png'))
    	exit('{"status":200,"message":"Avatar thay đổi thành công!","url":"'.$_url.'/template/avatars/'.$_user['id'].'.png?t='.time().'"}');
    else
    	exit('{"status":403,"message":"Có lỗi xảy ra vui lòng thử lại sau."}');

} else 
	exit('{"status":403,"message":"Không thể tải lên hình ảnh!"}');

?>