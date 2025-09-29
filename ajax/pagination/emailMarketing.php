<?php
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] : date('d/m/Y',strtotime('-6 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] : date('d/m/Y',time());
$sql = "";
$ts1 = date("Y-m-d",strtotime(str_replace('/','-',$ts)));
$te1 = date("Y-m-d",strtotime(str_replace('/','-',$te)));
$sql.=" and email_date >='".$ts1."' and email_date <='".$te1."' ";
if(isset($_GET['category_id'])){
  $category_id = $_GET['category_id'];
}else{
    $category_id = 0;
}
if(isset($_GET['category_id1'])){
    $category_id1 = $_GET['category_id1'];
}else{
    $category_id1 = 0;
}
if(isset($_GET['category_id2'])){
    $category_id2 = $_GET['category_id2'];
}else{
    $category_id2= 0;
}
if(isset($_GET['type'])){
  if($_GET['type']=='accept'){
    $status = 1;
  }elseif($_GET['type']=='refuse'){
    $status = 2;
  }
}else{
  $status = 0;
}
$result = getData('core_email_marketings','id,email_setting_id,email_category_id1,email_category_id2,email_landing_id,name,phone,email,email_date,status',['status'=>$status],$sql,'id','desc',$perPage);

$paginationHtml = '';

foreach ($result as $row) {       
    $email_setting = getInfoById('core_offers','id,name',$row['email_setting_id']);                                      
    $email_category1 = getInfoById('core_comment_categories1','id,category_name',$row['email_category_id1']);                                          
    $email_category2 = getInfoById('core_comment_categories2','id,category_name2',$row['email_category_id2']);        
    $paginationHtml.='<tr data-id='.$row["id"].'>'; 

    if(isset($_GET['type'])) {         

        $paginationHtml.='<td class="text-center" width="100">';  

        if($_GET['type']=='accept'){

            $paginationHtml.='<a href="?route=addEmailMarketing&type=accept&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';

        }else{  

            $paginationHtml.='<a href="?route=addEmailMarketing&type=refuse&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';

        }

        $paginationHtml.='<span role="btn-cancelCommentReply" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelCommentReply"><i class="fas fa-times ml-1"></i></span>';    

        $paginationHtml.='</td>';

    }

    $paginationHtml.='<td class="text-center"><strong>'.$row["id"].'</strong></td>';   

    $paginationHtml.='<td>'.date('d-m-Y',strtotime($row["email_date"])).'</td>';     

    if(!empty($email_setting)){

        $paginationHtml.='<td>'.$email_setting["name"].'</td>';

    }else{

        $paginationHtml.='<td></td>';

    }    

    if(!empty($email_category1)){

        $paginationHtml.='<td><strong class="trigger green lighten-3" style="color:#fff;">'.$email_category1["category_name"].'</strong></td>';

    }else{

        $paginationHtml.='<td></td>';

    }   

    if(!empty($email_category2)){

        $paginationHtml.='<td><strong class="trigger green lighten-3" style="color:#fff;">'.$email_category2["category_name2"].'</strong></td>';

    }else{

        $paginationHtml.='<td></td>';

    }     

    $paginationHtml.='<td class="text-center">'; 

    $paginationHtml.='<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$row["id"].'">Xem chi tiết</button>';

    $paginationHtml.='<div class="modal fade" id="exampleModal'.$row["id"].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';

    $paginationHtml.='<div class="modal-dialog" role="document">';

    $paginationHtml.='<div class="modal-content">';

    $paginationHtml.='<div class="modal-header">';

    $paginationHtml.='<h5 class="modal-title" id="exampleModalLabel">Thông tin</h5>';

    $paginationHtml.='<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

    $paginationHtml.='</div>';

    $paginationHtml.='<div class="modal-body text-left" style="white-space:normal;">';

    $paginationHtml.='<div style="padding-bottom:10px;">';

    $paginationHtml.='<p style="margin-bottom:0;"><strong>Họ tên:</strong> '.$row["name"].'</p>';

    $paginationHtml.='<p style="margin-bottom:0;"><strong>Điện thoại:</strong> '.$row["name"].'</p>';

    $paginationHtml.='<p style="margin-bottom:0;"><strong>Email:</strong> '.$row["email"].'</p>';   

    if(empty($_GET['type'])){

        $paginationHtml.='<p style="margin-bottom:0;"><strong style="display:block;">Trạng thái:</strong>';    

        $paginationHtml.='<div class="status_all" data-status-id='.$row["id"].' data-table="core_email_marketings">';

        $paginationHtml.='<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value="1">&nbsp;&nbsp;Accept</label>&nbsp;&nbsp;';

        $paginationHtml.='<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value = "2">&nbsp;&nbsp;Refuse</label>&nbsp;&nbsp;';

        $paginationHtml.='</div>'; 

        $paginationHtml.='<div style="clear:both;"></div></div>';           

        $paginationHtml.='</p>';

    }

    $paginationHtml.='</div>';

    $paginationHtml.='<div class="modal-footer">';

    $paginationHtml.='<button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>';

    $paginationHtml.='</div>';

    $paginationHtml.='</div>';

    $paginationHtml.='</div>';

    $paginationHtml.='</div>';

    $paginationHtml.='</td>';

	$paginationHtml.='</tr>';  

} 
echo $paginationHtml; 

?>