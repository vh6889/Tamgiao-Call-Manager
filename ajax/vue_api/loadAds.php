<?php 
$data = json_decode(file_get_contents("php://input"));
$query = 'SELECT * FROM core_ads';
$result = $_db->query($query)->fetch_array();
$response_arr = [];
foreach($result as $row){    
   $response_arr[] = [
    'id' => $row['id'],
    'ads' => $row["ads"],    
   ];

}
echo json_encode($response_arr);
exit;
?>