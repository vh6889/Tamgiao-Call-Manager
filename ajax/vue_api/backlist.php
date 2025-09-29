<?php 
$received_data = json_decode(file_get_contents("php://input"));
$limit = $received_data->limit;
$data = array();
if($received_data->action == 'fetchall'){
    if(isset($_GET['countLength'])){
        $query = 'SELECT * FROM core_backlists ORDER BY id DESC';
    }else{
        $query = 'SELECT * FROM core_backlists ORDER BY id DESC limit '.$limit;
    }
    $result = $_db->query($query)->fetch_array();
    foreach($result as $row){
        $user = getUser($row['user_add']);
        $data[]=[
            'id'    => $row['id'],
            'phone_number'=>'<b class="trigger red lighten-3">'._e($row['phone_number']).'</b>',
            'note'=>nl2br(_e($row['note'])),
            'user_add'=>'<div class="chip align-middle">
            <a target="_blank" href="?route=statistics&user='.$user['id'].'">
              <img src="'.getAvatar($user['id']).'"> '.(!isBanned($user) ? _e($user['name']) : '<strike class="text-dark"><b>'._e($user['name']).'</b></strike>').'
            </a>
            </div>'
        ];
    }
    echo json_encode($data);
}elseif($received_data->action == 'delete'){
    $query = $_db->exec_query("delete from `core_backlists`  where `id`='".$received_data->id."' ");
	$output = array(
		'message' => 'Xóa thành công Backlist'
	);
	echo json_encode($output);
}elseif($received_data->action == 'fetchSingle'){
    $id = $received_data->id;
    $query = 'SELECT * FROM core_backlists WHERE id='.$id.' ORDER BY id DESC LIMIT 1';
    $result = $_db->query($query)->fetch();
    $data['id'] = $result['id'];
    $data['phone_number'] = $result['phone_number'];
    $data['note'] = $result['note'];
    echo json_encode($data);
}elseif($received_data->action == 'update'){                 
    $_db->exec_query("update `core_backlists` set `phone_number`='".$received_data->phone_number."',`note`='".$received_data->note."' where `id`='".$received_data->hiddenId."' ");    
    $output = array(
        'message' => 'Data Updated'
    );
    echo json_encode($output);
}elseif($received_data->action == 'insert'){
    $_db->exec_query("insert into `core_backlists` set `phone_number`='".$received_data->phone_number."',`note`='".$received_data->note."' ");   
    $output = [
        'message' => 'Thêm backlist thành công'
    ];
    echo json_encode($output); 
}
?>