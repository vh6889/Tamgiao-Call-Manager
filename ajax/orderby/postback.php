<?php
if(isset($_POST['column']) && isset($_POST['sortOrder'])){
    $columnName = strip_tags(str_replace("","_",strtolower($_POST['column'])));
    $sortOrder  = $_POST['sortOrder'];
}
if(isset($_POST['limit'])){
    $perPage = $_POST['limit'];
}else{
    $perPage = 10;
}
if(isset($_GET['type'])){
    if($_GET['type']=='success'){
        $response_code = 200;
    }elseif($_GET['type']=='fail'){
        $response_code = 400; 
    }
}else{
    $response_code = 200;
}
$sql = "";
$ts = date("Y-m-d",strtotime(str_replace('/','-',$_GET['ts'])));
$te = date("Y-m-d",strtotime(str_replace('/','-',$_GET['te'])));
$sql.=" and DATE_FORMAT(created,'%Y-%m-%d') >='".$ts."' and DATE_FORMAT(created,'%Y-%m-%d') <='".$te."' ";
$offer_id = $_GET['offer_id'];
if($offer_id > 0){
    $sql.=" OR offer_id='".$offer_id."' ";
}
$type_ads_id = $_GET['type_ads_id'];
if($type_ads_id > 0){    
    $sql.=" OR type_ads_id='".$type_ads_id."' ";
}
$result = getData('core_s2s_postback','id,offer_id,type_ads_id,landing_page,state,request_url,response_code,created',['response_code'=>$response_code],'',$columnName,$sortOrder,$perPage);
$paginationHtml = '';

foreach ($result as $row) {  
    $postback_offers_p = getInfoById('core_offers','id,name',$row['offer_id']);
    $postback_ads_p = getInfoById('core_ads','id,ads',$row['type_ads_id']);
    $paginationHtml.= '<tr data-id='.$row["id"].'>'; 
    $paginationHtml.= '<td class="text-center" width="100">';                                    
    $paginationHtml.= '<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder"><i class="fas fa-times ml-1"></i></span>';
    $paginationHtml.= '</td>';
    $paginationHtml.= '<td class="text-center">Postback #<strong>'.$row["id"].'</strong></td>';
    if(!empty($postback_offers_p)){
        $paginationHtml.= '<td class="text-center"><strong class="trigger green lighten-3">'.$postback_offers_p["name"].'</strong></td>';
    }else{
        $paginationHtml.= '<td></td>';
    }
    $paginationHtml.= '<td>'.$row["landing_page"].'</td>';
    $paginationHtml.= '<td>'.date("d-m-Y H:i:s",strtotime($row["created"])).'</td>';
    if(!empty($postback_ads_p)){
        $paginationHtml.= '<td class="text-center"><strong class="trigger green lighten-3">'.$postback_ads_p["ads"].'</strong></td>';
    }else{
        $paginationHtml.= '<td></td>';
    }
    $paginationHtml.= '<td>'.$row["state"].'</td>';
    $paginationHtml.= '<td>'.$row["request_url"].'</td>';
    $paginationHtml.= '<td>'.$row["response_code"].'</td>';
    $paginationHtml.= '</tr>';
} 

echo $paginationHtml;

?>