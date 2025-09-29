<?php 
$data = json_decode(file_get_contents("php://input"));
$query = 'SELECT * FROM core_offers';
$result = $_db->query($query)->fetch_array();
$response_arr = [];
foreach($result as $row){    
   $response_arr[] = [
    'id' => $row['id'],
    'name' => $row["name"],    
   ];

}
echo json_encode($response_arr);
exit;
?>