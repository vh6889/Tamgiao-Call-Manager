<?php

	//header('Content-type: application/json; charset=utf-8');

    $debug = 0;
	$homePath = __DIR__;

    error_reporting($debug ? E_ALL & ~E_NOTICE : 0);
    ini_set('display_errors', $debug);

	include __DIR__.'/includes/config.php';
	include __DIR__.'/includes/functions.php';
	include __DIR__.'/includes/helpers.php';

	$_user = isLogin();

	if(!$_user)
		exit('{"status":403,"message":"Truy cập bị từ chối"}');

	preg_match("#^(.*?)-(.*?)$#si",$_act,$exp);

	$folder = isset($exp[1])?$exp[1]:'';
	$file   = isset($exp[2])?$exp[2]:'';

	if(file_exists(__DIR__.'/ajax/'.$folder.'/'.$file.'.php'))
		include __DIR__.'/ajax/'.$folder.'/'.$file.'.php';
	else {
		if(file_exists(__DIR__.'/ajax/'.$_act.'.php'))
			include __DIR__.'/ajax/'.$_act.'.php';
		else
			exit('{"status":403,"message":"Lỗi request API"}');
	}

?>