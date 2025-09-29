<?php 
$received_data = json_decode(file_get_contents("php://input"));
$data = array();
if($received_data->action == 'fetchall'){     
    $result = getData('core_offers','*','','','id','desc','');                       
    foreach($result as $row){
        $ads = getInfoById('core_ads','id,ads',$row['type_ads']);   
        $payer = getUser($row['payer']);
        $status = $row['status'] == "run" ? '<span class="text-success">Running</span>' : '<span class="text-danger">Stop</span>';   
        if(!empty($ads)){
            $type_ads = '<strong class="trigger green lighten-2">'.$ads['ads'].'</strong>';    
        }else{
            $type_ads = '';
        }
        $label_color = $row['status'] == 'run' ? 'green' :'red';
        $prices_label = '';
        if($row['price']) {
            foreach(explode('|',$row['price']) as $_price) {
                $prices_label .= '<strong class="trigger '.$label_color.' lighten-2 text-white">'.number_format($_price, 0, ',', '.').'K</strong></br>';
            }
        }
        $_offer_name = strlen($row['name'])>20 ? mb_substr($row['name'],0,20,'utf-8').'...' : $row['name'];
        $_offet_key  = ($row['offer_link']) ? '<a href="'.$row['offer_link'].'" target="__blank">'.$row['key'].'<i class="fas fa-external-link-alt ml-1 text-primary"></i></a>' : $row['key'];
        $data[]=[
            'id'    => $row['id'],
            'key'   =>  $_offet_key,
            'status'=>  $status,
            'name'  => '<strong class="trigger '.$label_color.' lighten-2">'._e($_offer_name).'</strong>',
            'cost'  => '<strong class="trigger '.$label_color.' lighten-2 text-white">'._e($row['cost']).'%</strong>',
            'price' => $prices_label,
            'price_deduct' => '<strong class="trigger '.$label_color.' lighten-2 text-white">'.number_format($row['price_deduct'], 0, ',', '.').'K</strong>',
            'price_ship' => $row['price_ship'],
            'type_ads' => $type_ads,
            'tracking_token' => $row['tracking_token'],
            's2s_postback_url' => $row['s2s_postback_url'],            
        ];
    }
    echo json_encode($data);
}elseif($received_data->action == 'delete'){
    $query = $_db->exec_query("delete from `core_offers`  where `id`='".$received_data->id."' ");
	$output = array(
		'message' => 'Xóa thành công Offers'
	);
    echo json_encode($output);
}elseif($received_data->action == 'insert'){
    $insert_str .= '`name`="'.$received_data->name.'"';
    $insert_str .= ',`cost`="'.$received_data->cost.'"';
    $insert_str .= ',`payout`="'.$received_data->payout.'"';
    $insert_str .= ',`payout_type`="'.$received_data->payout_type.'"';
    $insert_str .= ',`price`="'.$received_data->price.'"';
    $insert_str .= ',`price_deduct`="'.$received_data->price_deduct.'"';
    $insert_str .= ',`price_ship`="'.$received_data->price_ship.'"';
    $insert_str .= ',`key`="'.$received_data->key.'"';
    $insert_str .= ',`type_ads`="'.$received_data->type_ads.'"';
    $insert_str .= ',`tracking_token`="'.$received_data->tracking_token.'"';
    $insert_str .= ',`s2s_postback_url`="'.$received_data->s2s_postback_url.'"';

    $_db->exec_query('insert into `core_offers` set '.$insert_str);
    $output = [
        'message' => 'Thêm Offers thành công'
    ];
    echo json_encode($output);
}elseif($received_data->action == 'fetchSingle'){
    $id = $received_data->id;
    $query = 'SELECT * FROM core_offers WHERE id='.$id.' ORDER BY id DESC LIMIT 1';
    $result = $_db->query($query)->fetch();
    $data = [
        'id'    =>  $result['id'],
        'name'  => $result['name'],
        'cost'  => $result['cost'],
        'payout'  => $result['payout'],
        'payout_type'  => $result['payout_type'],
        'price' => $result['price'],
        'price_bonus' => $result['price_bonus'],
        'price_deduct' => $result['price_deduct'],
        'price_ship'    => $result['price_ship'],
        'key'   =>  $result['key'],
        'type_ads' => $result['type_ads'],
        'tracking_token' => $result['tracking_token'],
        's2s_postback_url' => $result['s2s_postback_url'],
        'status'    =>  $result['status']
    ];    
    echo json_encode($data);
}elseif($received_data->action == 'update'){
    $update_str .= '`name`="'.$received_data->name.'"';
    $update_str .= ',`cost`="'.$received_data->cost.'"';
    $update_str .= ',`payout`="'.$received_data->payout.'"';
    $update_str .= ',`payout_type`="'.$received_data->payout_type.'"';
    $update_str .= ',`price`="'.$received_data->price.'"';
    $update_str .= ',`price_bonus`="'.$received_data->price_bonus.'"';
    $update_str .= ',`price_deduct`="'.$received_data->price_deduct.'"';
    $update_str .= ',`price_ship`="'.$received_data->price_ship.'"';
    $update_str .= ',`key`="'.$received_data->key.'"';
    $update_str .= ',`type_ads`="'.$received_data->type_ads.'"';
    $update_str .= ',`tracking_token`="'.$received_data->tracking_token.'"';
    $update_str .= ',`s2s_postback_url`="'.$received_data->s2s_postback_url.'"';
    $update_str .= ',`status`="'.$received_data->status.'"';

    $_db->exec_query("update `core_offers` set $update_str where `id`='".$received_data->hiddenId."' ");       
    $output = array(
        'message' => 'Cập nhật thành công'
    );
    echo json_encode($output);
}
?>