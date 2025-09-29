<?php

$_id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';

$districts = $_db->query("select * from `core_districts` where `provinceid`='".escape_string($_id)."' order by `name` ")->fetch_array();


if($districts){
	$data = array();
	foreach ($districts as $arr) {
		$data[] = $arr['name'];
	}
	echo json_encode($data);
}
else
	echo json_encode([]);
exit();

?>