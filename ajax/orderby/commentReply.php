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
$startFrom = ($page-1) * $perPage;  
if(isset($_GET['type'])){
    if($_GET['type']=='uncheck'){
        $status = 0;
    }elseif($_GET['type']=='accept'){
        $status = 1;
    }elseif($_GET['type']=='refuse'){
        $status = 2;
    }
}else{
    $status = 0;
}

$result = $_db->query("SELECT * FROM core_comment_replys WHERE status='".$status."' order by $columnName $sortOrder LIMIT $perPage")->fetch_array();
$paginationHtml = '';
foreach ($result as $row) {       
    $comment_category = $_db->query("select * from `core_comments` where id='".$row['comment_id']."' order by `id` desc limit 1 ")->fetch();   
    $paginationHtml.='<tr data-id='.$row["id"].'>';
    if(isset($_GET['type'])) {
        if($_GET['type']=='uncheck'){
            
        }else{            
            $paginationHtml.='<td class="text-center" width="100">';
            $paginationHtml.='<a href="?route=addCommentReply&type='.$_GET['type'].'&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';
            $paginationHtml.='<span role="btn-cancelCommentReply" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelCommentReply"><i class="fas fa-times ml-1"></i></span>';
            $paginationHtml.='</td>';
        }
    }    
    $paginationHtml.='<td class="text-center">Trả lời Comment #<strong>'.$row["id"].'</strong></td>';   
    $paginationHtml.='<td>'.date('d-m-Y',strtotime($row["created"])).'</td>'; 
    if(!empty($comment_category)){
        $paginationHtml.='<td class="text-left">';		
		$paginationHtml.= str_replace('.','<br>',$comment_category["comment"]);
		
		$paginationHtml.='</td>';
    }else{
        $paginationHtml.='<td></td>';
    }    
    $paginationHtml.='<td class="text-center">'; 
    $paginationHtml.='<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$row["id"].'">Chi tiết</button>';
    $paginationHtml.='<div class="modal fade" id="exampleModal'.$row["id"].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
    $paginationHtml.='<div class="modal-dialog" role="document">';
    $paginationHtml.='<div class="modal-content">';
    $paginationHtml.='<div class="modal-header">';
    $paginationHtml.='<h5 class="modal-title" id="exampleModalLabel">Thông tin</h5>';
    $paginationHtml.='<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    $paginationHtml.='</div>';
    $paginationHtml.='<div class="modal-body text-left" style="white-space:normal;"><img style="float:left;margin-right:10px;" src='.$row["avatar"].' width="100">';
    $paginationHtml.='<div style="padding-bottom:10px;">';
    $paginationHtml.='<p style="margin-bottom:0;"><strong>Họ tên:</strong> '.$row["name"].'</p>';
    $paginationHtml.='<p style="margin-bottom:0;"><strong>Email:</strong> '.$row["email"].'</p>';    
    if(isset($_GET['type'])){  
        if($_GET['type']=='uncheck'){
            $paginationHtml.='<p style="margin-bottom:0;"><strong style="display:block;">Trạng thái:</strong>';    
            $paginationHtml.='<div class="status_all" data-status-id='.$row["id"].' data-table="core_comment_replys">';
            $paginationHtml.='<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value="1">&nbsp;&nbsp;Accept</label>&nbsp;&nbsp;';
            $paginationHtml.='<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value = "2">&nbsp;&nbsp;Refuse</label>&nbsp;&nbsp;';
            $paginationHtml.='</div>';
        }
    }
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