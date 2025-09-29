<?php
if(isset($_POST['limit'])){
    $perPage = $_POST['limit'];
}else{
    $perPage = 10;
}
$result = getData('core_comment_bads','id,comment_id,name,email,content,bad_date','','','id','desc',$perPage);
$paginationHtml = '';
foreach ($result as $row) {       
    $comment_category = getInfoById('core_comments','id,name',$row['comment_id']);     
    $paginationHtml.='<tr data-id='.$row["id"].'>';  
    $paginationHtml.='<td class="text-center" width="100">';
    $paginationHtml.='<a href="?route=addCommentBad&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';
    $paginationHtml.='<span role="btn-cancelCommentReply" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelCommentReply"><i class="fas fa-times ml-1"></i></span>';
    $paginationHtml.='</td>';
    $paginationHtml.='<td class="text-center"><strong>'.$row["id"].'</strong></td>';   
    $paginationHtml.='<td>'.date('d-m-Y',strtotime($row["bad_date"])).'</td>'; 
    if(!empty($comment_category)){
        $paginationHtml.='<td><strong class="trigger green lighten-3" style="color:#fff;">'.$comment_category["name"].'</strong></td>';
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
    $paginationHtml.='<p style="margin-bottom:0;"><strong>Email:</strong> '.$row["email"].'</p>';    
    $paginationHtml.='<div style="clear:both;"></div></div>';    
    $paginationHtml.='<p style="margin-bottom:0;"><strong style="display:block;">Nội dung:</strong> '.$row["content"].'</p>';    
    $paginationHtml.='</p>';
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