<?php
session_start();
if (extension_loaded('zlib')) {
    @ini_set('zlib_output_compression','On');
    @ini_set('zlib.output_compression_level', 3);
    @ini_set('output_buffering','On');
    ob_start('ob_gzhandler');
} else {
    ob_start();
}
include __DIR__.'/config.php';
include __DIR__.'/functions.php';
include __DIR__.'/helpers.php';
include __DIR__.'/language/vi.php';
$_statusmessage = array();
$_user = isLogin();
if(!$_user && $_route != 'signin'){
	header('Location: ?route=signin');
}
elseif(!$_user['ukey']){
    global $_db;
    /* Render user's randomkey */
    $_user['ukey'] = generateRandomUserKey();
    $_db->query("update `core_users` set `ukey`='".$_user['ukey']."' where `id`='".$_user['id']."' ");
}
include __DIR__.'/controllers/data.php';
include __DIR__.'/controllers/actions.php';
?>