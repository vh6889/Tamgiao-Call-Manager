<?php 
$received_data = json_decode(file_get_contents("php://input"));
$data = array();
if($received_data->action == 'fetchall'){     
    $result = getData('core_ads','*','','','id','desc','');                       
    foreach($result as $row){       
        $data[]=[
            'id'    => $row['id'],
            'ads'   =>  $row['ads'],                     
        ];
    }
    echo json_encode($data);
}elseif($received_data->action == 'delete'){
    $query = $_db->exec_query("delete from `core_ads`  where `id`='".$received_data->id."' ");
	$output = array(
		'message' => 'Xóa thành công Ads'
	);
	echo json_encode($output);
}elseif($received_data->action == 'insert'){
    $_db->exec_query("insert into `core_ads` set `ads`='".$received_data->ads."' ");   
    $output = [
        'message' => 'Thêm Ads thành công'
    ];
    echo json_encode($output); 
}elseif($received_data->action == 'fetchSingle'){
    $id = $received_data->id;
    $query = 'SELECT * FROM core_ads WHERE id='.$id.' ORDER BY id DESC LIMIT 1';
    $result = $_db->query($query)->fetch();
    $data = [
        'id'    =>  $result['id'],
        'ads'   =>  $result['ads']
    ];    
    echo json_encode($data);
}elseif($received_data->action == 'update'){                 
    $_db->exec_query("update `core_ads` set `ads`='".$received_data->ads."' where `id`='".$received_data->hiddenId."' ");    
    $output = array(
        'message' => 'Cập nhật thành công'
    );
    echo json_encode($output);
}
?>