<?php

include('class/class.mysqli.php');
                   
$_url = 'https://footec.work/';
$database_config = array('host' => 'localhost',
						 'port' => '3306',
						 'name' => 'footec_work_cb81',
						 'user' => 'footecworkcb81',
                         'pass' => 'bc3c159cbdcd3f8e');
                         
$_db = new DB_mysqli($database_config);

?>