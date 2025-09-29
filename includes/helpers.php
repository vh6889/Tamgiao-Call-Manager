<?php
/**
 * Author: Tieu_Vu
 * 
 * Name: Create Or Update DB
 * @param string $table_name
 * @param int $id
 * @param array $data
 * 
 */
function createOrUpdate($table_name,$id,$data){
	global $_db;
	$data_value = "";
	foreach($data as $k=>$v){
		$data_value.=",".$k."='".$v."'";
	}
	$data_return = substr($data_value,1);
	if($id!=''){
		$query = $_db->exec_query("UPDATE $table_name SET $data_return WHERE id=$id");		
	}else{		
		$query = $_db->exec_query("INSERT INTO $table_name SET $data_return");		
	}
	return $query;
}

/**
 * Author: Tieu_Vu
 * 
 * Name: Select DB
 * @param string $table_name
 * @param string $select
 * @param array $parameter
 * @param string $order_name
 * @param string $order_value
 * @param integer $limit
 * 
 */
function getData($table_name,$select,$parameter,$sql,$order_name,$order_value,$limit){
	global $_db;	
	if(!empty($parameter)){
		$parameter_value = "";
		foreach($parameter as $k=>$v){
			$parameter_value.=",".$k."='".$v."'";
		}
		$data_parameter = substr($parameter_value,1);
		if($limit!=''){			
			if($order_name!='' && $order_value!=''){
				$query = $_db->query("SELECT $select FROM $table_name WHERE $data_parameter ".$sql." order by $order_name $order_value limit $limit")->fetch_array();
			}else{							
				$query = $_db->query("SELECT $select FROM $table_name WHERE $data_parameter $sql limit $limit")->fetch_array();
			}
		}else{
			if($order_name!='' && $order_value!=''){
				$query = $_db->query("SELECT $select FROM $table_name WHERE $data_parameter ".$sql." order by $order_name $order_value")->fetch_array();
			}else{
				$query = $_db->query("SELECT $select FROM $table_name WHERE $data_parameter")->fetch_array();
			}
		}
	}else{
		if($limit!=''){	
			if($order_name!='' && $order_value!=''){
				$query = $_db->query("SELECT $select FROM $table_name order by $order_name $order_value limit $limit")->fetch_array();
			}else{
				$query = $_db->query("SELECT $select FROM $table_name limit $limit")->fetch_array();
			}	
		}else{
			if($order_name!='' && $order_value!=''){
				$query = $_db->query("SELECT $select FROM $table_name order by $order_name $order_value")->fetch_array();
			}else{
				$query = $_db->query("SELECT $select FROM $table_name")->fetch_array();
			}	
		}
	}
	return $query;
}

/**
 * Author: Tieu_Vu
 * 
 * Name: Get Table By Id
 * @param string $table_name
 * @param string $select
 * @param integer $id
 * 
 */
function getInfoById($table_name,$select,$id){
	global $_db;
	$query = $_db->query("SELECT $select FROM $table_name WHERE id=$id limit 1")->fetch();
	return $query;
}

/**
 * Author: Tieu_Vu
 * 
 * Name: Show Only 1 record
 * @param string $table_name
 * @param string $select
 * 
 */
function getTableByFirst($table_name,$select){
	global $_db;
	$query = $_db->query("SELECT $select FROM $table_name limit order by id DESC 1")->fetch();
	return $query;
}

function LocDau($str)
{
	$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|�� �|ặ|ẳ|ẵ|ắ)/", 'a', $str);
	$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
	$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
	$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
	$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
	$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
	$str = preg_replace("/(đ)/", 'd', $str);
	$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|�� �|Ặ|Ẳ|Ẵ)/", 'A', $str);
	$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
	$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
	$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ợ|Ở|Ớ|Ỡ)/", 'O', $str);
	$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
	$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
	$str = preg_replace("/(Đ)/", 'D', $str);
	$str = preg_replace("/( |'|,|\||\.|\"|\?|\/|\%|–|!|:)/", '-', $str);
	$str = preg_replace("/(\()/", '-', $str);
	$str = preg_replace("/(\))/", '-', $str);
	$str = preg_replace("/(&)/", '-', $str);
	$str = preg_replace("/“/", '', $str);
	$str = preg_replace("/”/", '', $str);
	$str = preg_replace("/;/", '', $str);
	$str = preg_replace("/:/", '', $str);
	return slugify($str);
}
function slugify($text, string $divider = '-')
{
	// replace non letter or digits by divider
	$text = preg_replace('~[^\pL\d]+~u', $divider, $text);

	// transliterate
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);

	// trim
	$text = trim($text, $divider);

	// remove duplicate divider
	$text = preg_replace('~-+~', $divider, $text);

	// lowercase
	$text = strtolower($text);

	if (empty($text)) {
		return 'n-a';
	}

	return $text;
}