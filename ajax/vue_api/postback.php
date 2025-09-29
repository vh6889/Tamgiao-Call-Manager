<?php 
$received_data = json_decode(file_get_contents("php://input"));
$limit = $received_data->limit;
$data = array();
$sql = "";
$ts = date("Y-m-d",strtotime(str_replace('/','-',$received_data->ts)));
$te = date("Y-m-d",strtotime(str_replace('/','-',$received_data->te)));
$sql.=" and DATE_FORMAT(created,'%Y-%m-%d') >='".$ts."' and DATE_FORMAT(created,'%Y-%m-%d') <='".$te."' ";
$offer_id = $received_data->offer_id;
if($offer_id > 0){
    $sql.=" and offer_id='".$offer_id."' ";
}
$type_ads_id = $received_data->types;
if($type_ads_id > 0){    
    $sql.=" and type_ads_id='".$type_ads_id."' ";
}

if(!isAdmin() && isPublisher()) {
    $sql .= " and `ukey`='".$_user['ukey']."' ";
}

$response_code = ($_GET['response_code']==200) ? "`response_code`='200'" : "`response_code`!='200'";
if($received_data->action == 'fetchall'){
    if(isset($_GET['countLength'])){
        $query = 'SELECT * FROM core_s2s_postback WHERE '.$response_code.' '.$sql.' ORDER BY id DESC';
    }else{
        $query = 'SELECT * FROM core_s2s_postback WHERE '.$response_code.' '.$sql.' ORDER BY id DESC limit '.$limit;
    }
    $result = $_db->query($query)->fetch_array();
    foreach($result as $row){
        $postback_offers_p = getInfoById('core_offers','id,name',$row['offer_id']);
        $postback_ads_p = getInfoById('core_ads','id,ads',$row['type_ads_id']);  
        $data[] = array(
            'id' => $row['id'],
            'offer_id' => $postback_offers_p["name"],
            'type_ads_id' => $postback_ads_p["ads"],
            'landing_page' => $row['landing_page'],
            'state' => $row['state'],
            'request_url' => $row['request_url'],
            'response_code' => $row['response_code'],
            'created' => date("d-m-Y H:i:s",strtotime($row["created"]))
        );     
    }
    echo json_encode($data);
}elseif($received_data->action == 'delete'){
    $query = $_db->exec_query("delete from `core_s2s_postback` where `id`='".$received_data->id."' ");    
	$output = array(
		'message' => 'Xóa thành công Postback'
	);
	echo json_encode($output);
}
?>