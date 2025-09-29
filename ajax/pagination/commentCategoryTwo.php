<?php
if(isset($_POST['limit'])){
    $perPage = $_POST['limit'];
}else{
    $perPage = 10;
} 
$result = getData('core_comment_categories2','id,category_id,category_id1,category_name2','','','id','desc',$perPage);
$paginationHtml = '';
foreach ($result as $row) {       
    $comment_category = getInfoById('core_offers','id,name',$row['category_id']);
    $comment_category1 = getInfoById('core_comment_categories1','id,category_name',$row['category_id1']); 
    $paginationHtml.='<tr data-id='.$row["id"].'>';  
    $paginationHtml.='<td class="text-center" width="100">';
    $paginationHtml.='<a href="?route=editCommtentCategoryTwo&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';
    $paginationHtml.='<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder"><i class="fas fa-times ml-1"></i></span>';
    $paginationHtml.='</td>';
    $paginationHtml.='<td class="text-center"><strong>'.$row["id"].'</strong></td>';    
    if(!empty($comment_category)){
        $paginationHtml.='<td><strong class="trigger green lighten-3">'.$comment_category["name"].'</strong></td>';
    }else{
        $paginationHtml.='<td></td>';
    }
    if(!empty($comment_category1)){
        $paginationHtml.='<td><strong class="trigger green lighten-3">'.$comment_category1["category_name"].'</strong></td>';
    }else{
        $paginationHtml.='<td></td>';
    }    
    $paginationHtml.='<td>'.$row["category_name2"].'</td>';
	$paginationHtml.='</tr>';  
} 
echo $paginationHtml; 
?>