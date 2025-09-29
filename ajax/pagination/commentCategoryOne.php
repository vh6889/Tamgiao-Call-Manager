<?php
if(isset($_POST['limit'])){
    $perPage = $_POST['limit'];
}else{
    $perPage = 10;
}
$result = getData('core_comment_categories1','id,category_id,category_name','','','id','desc',$perPage);
$paginationHtml = '';
foreach ($result as $row) {       
    $comment_category = getInfoById('core_offers','id,name',$row['category_id']);  
    $paginationHtml.='<tr data-id='.$row["id"].'>';  
    $paginationHtml.='<td class="text-center" width="100">';
    $paginationHtml.='<a href="?route=editCommtentCategoryOne&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';
    $paginationHtml.='<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder"><i class="fas fa-times ml-1"></i></span>';
    $paginationHtml.='</td>';
    $paginationHtml.='<td class="text-center"><strong>'.$row["id"].'</strong></td>';    
    if(!empty($comment_category)){
        $paginationHtml.='<td><strong class="trigger green lighten-3" style="color:#fff;">'.$comment_category["name"].'</strong></td>';
    }else{
        $paginationHtml.='<td></td>';
    }
    $paginationHtml.='<td>'.$row["category_name"].'</td>';
	$paginationHtml.='</tr>';  
} 
echo $paginationHtml; 
?>