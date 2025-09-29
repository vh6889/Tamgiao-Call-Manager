<?php 
$data = json_decode(file_get_contents("php://input"));
$row = $data->row;
$rowperpage = $data->rowperpage;
if(isset($_GET['countpost'])){
    $query = 'SELECT * FROM core_notifications WHERE `user_to` = '.$_user['id'].' and `status`=1';
}else{
    $query = 'SELECT * FROM core_notifications WHERE `user_to` ='.$_user['id'].' ORDER BY id DESC LIMIT '.$row.','.$rowperpage;
}
$result = $_db->query($query)->fetch_array();
$response_arr = array();
foreach($result as $row){  
    $time = '
        '.($row['status'] == 1 ? '<span class="btn purple-gradient waves-effect waves-light">New</span>' : '').'
        '.get_time($row['time']).'
    ';
    $from = getUser($row['user_from']); 
    $form_to='<div class="chip align-middle">
        <a target="_blank" href="?route=statistics&user='.$from['id'].'">
        <img src="'.getAvatar($from['id']).'"> '.(!isBanned($from) ? _e($from['name']) : '<strike class="text-dark"><b>'._e($from['name']).'</b></strike>').'
        </a>
    </div>
    <p class="'.getColorAccount($from).'">'.getTypeAccount($from).'</p>';
  $text = '<div class="d-flex justify-content-between mb-1">
    <hp class="mb-1">
        '.$row['title'].'
    </hp>
    </div>
    <p class="text-truncate">
    '.$row['text'].'
    </p>';
   $response_arr[] = array(
    'time' => $time,
    'user_from' => $form_to,    
    'text' => $text,    
   );

}
echo json_encode($response_arr);
exit;
?>