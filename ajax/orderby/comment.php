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

    if($_GET['type']=='accept'){

        $status = 1;

    }elseif($_GET['type']=='refuse'){

        $status = 2; 

    }

}else{

    $status = 0;

}


$sql = "";

$ts = date("Y-m-d",strtotime(str_replace('/','-',$_GET['ts'])));

$te = date("Y-m-d",strtotime(str_replace('/','-',$_GET['te'])));

$sql.=" and date >='".$ts."' and date <='".$te."' ";

$category_id = $_GET['category_id'];

if($category_id > 0){    

    $sql.=" OR comment_category_id='".$category_id."' ";

}

$category_id1 = $_GET['category_id1'];

if($category_id1 > 0){

    $sql.=" OR comment_category_id1='".$category_id1."' ";

}

$category_id2 = $_GET['category_id2'];

if($category_id2 > 0){

    $sql.=" OR comment_category_id2='".$category_id2."' ";

}
$result = getData('core_comments','*',['status'=>$status],$sql,$columnName,$sortOrder,$perPage);

$paginationHtml = '';

foreach ($result as $row) {  

    $comment_category = $_db->query("select * from `core_offers` where id='".$row['comment_category_id']."' order by `id` desc limit 1 ")->fetch();

    $comment_category1 = $_db->query("select * from `core_comment_categories1` where id='".$row['comment_category_id1']."' order by `id` desc limit 1 ")->fetch();

    $comment_category2 = $_db->query("select * from `core_comment_categories2` where id='".$row['comment_category_id2']."' order by `id` desc limit 1 ")->fetch();

    $comment_landing = $_db->query("select * from `core_comment_landings` where id='".$row['comment_landing_id']."' order by `id` desc limit 1 ")->fetch();

    $paginationHtml.='<tr data-id='.$row["id"].'>';  

    if(isset($_GET['type'])){

        if($_GET['type']=='accept' || $_GET['type']=='refuse'){

            $paginationHtml.='<td class="text-center" width="100">';

            $paginationHtml.='<a href="?route=editComment&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';

            $paginationHtml.='<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder"><i class="fas fa-times ml-1"></i></span>';

            $paginationHtml.='</td>';

        }

    }else{

        $paginationHtml.='<td></td>';

    }

    $paginationHtml.='<td class="text-center">Comment #<strong>'.$row["id"].'</strong></td>';
    $paginationHtml.='<td class="text-center">'.date("d-m-Y",strtotime($row["date"])).'</td>'; 	

    if(!empty($comment_category)){

        $paginationHtml.='<td class="text-center"><strong class="trigger green lighten-3">'.$comment_category["name"].'</strong></td>';

    }else{

        $paginationHtml.='<td></td>';

    }  

    if(!empty($comment_category1)){

        $paginationHtml.='<td class="text-center"><strong class="trigger green lighten-3">'.$comment_category1["category_name"].'</strong></td>';

    }else{

        $paginationHtml.='<td></td>';

    }  

    if(!empty($comment_category2)){

        $paginationHtml.='<td class="text-center"><strong class="trigger green lighten-3">'.$comment_category2["category_name2"].'</strong></td>';

    }else{

        $paginationHtml.='<td></td>';

    }  

    if(!empty($comment_landing)){

        $paginationHtml.='<td class="text-center"><strong class="trigger green lighten-3">'.$comment_landing["landing"].'</strong></td>';

    }else{

        $paginationHtml.='<td></td>';

    }            

    $paginationHtml.='<td class="text-center">'; 

    $paginationHtml.='<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$row["id"].'">Xem chi tiết</button>';

    $paginationHtml.='<div class="modal fade" id="exampleModal'.$row["id"].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';

    $paginationHtml.='<div class="modal-dialog" role="document">';

    $paginationHtml.='<div class="modal-content">';

    $paginationHtml.='<div class="modal-header">';

    $paginationHtml.='<h5 class="modal-title" id="exampleModalLabel">Thông tin Comment</h5>';

    $paginationHtml.='<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

    $paginationHtml.='</div>';

    $paginationHtml.='<div class="modal-body text-left" style="white-space:normal;">';

    $paginationHtml.='<div style="padding-bottom:10px;">';

    $paginationHtml.='<p style="margin-bottom:0;"><strong>Họ tên:</strong> '.$row["name"].'</p>';

    $paginationHtml.='<p style="margin-bottom:0;"><strong>Email:</strong> '.$row["email"].'</p>';

    if(isset($_GET['type'])){       

    }else{

        $paginationHtml.='<p style="margin-bottom:0;"><strong style="display:block;">Trạng thái:</strong>';    

        $paginationHtml.='<div class="status_all" data-status-id='.$row["id"].' data-table="core_comments">';

        $paginationHtml.='<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value="1">&nbsp;&nbsp;Accept</label>&nbsp;&nbsp;';

        $paginationHtml.='<label class="radio-inline"><input style="opacity:1;position:static;" type="radio" name="status" value = "2">&nbsp;&nbsp;Refuse</label>&nbsp;&nbsp;';

        $paginationHtml.='</div>';

    }

    $paginationHtml.='<div style="clear:both;"></div></div>';    

    $paginationHtml.='<p style="margin-bottom:0;"><strong style="display:block;">Nội dung:</strong> '.$row["comment"].'</p>';    

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