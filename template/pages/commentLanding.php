<?php
$_count = count(getData('core_comment_landings','id,category_id,category_id1,category_id2,landing','','','id','desc',''));
$perPage = 10;
$_data = getData('core_comment_landings','id,category_id,category_id1,category_id2,landing','','','id','desc',$perPage);
?>
<section class="row">
  <div class="col-md-6">
    <h2 class="section-heading mb-4">Comment Landing Page (<?php echo $_count; ?>)</h2>
  </div>
</section>
<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">    
      <div class="row">
      <div class="col-md-6">
              <span class="custom_select_show">Hiện thị</span>
          <select name="pageSize" id="pageSize" class="custom_select">
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="30">30</option>
              <option value="50">50</option>
              <option value="100">100</option>   
              <option value="250">250</option>     
              <option value="500">500</option>

          </select>

          <span class="custom_select_record">bản ghi</span> 

          <div class="clear-fix"></div>
        </div>
        <div class="col-md-6 text-right">
          <a href="?route=addConmmentLanding" type="button" class="btn btn-primary">Thêm mới <i class="fas fa-plus-square ml-1"></i></a>      
        </div>
      </div>                
      <div class="mb-5">  
      <input type="hidden" id="limit" name="limit" value="10">
    <input type="hidden" id="totalCount" name="limit" value="<?php echo $_count; ?>">
    <div class="postList" style="overflow-x:auto;margin-bottom:15px;">                                          
      <table style="margin-top:10px;" id="dtBasicExample" class="table table-hover table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>            
            <th class="text-center"></th>
            <th class="text-center" data-toggle="tooltip" title="ID Comment"><strong>#ID</strong></th>
            <th class="text-center" data-toggle="tooltip" title="Commtent Category"><strong>Comment Category</strong></th>
            <th class="text-center" data-toggle="tooltip" title="Commtent Category 1"><strong>Comment Category 1</strong></th>
            <th class="text-center" data-toggle="tooltip" title="Commtent Category 2"><strong>Comment Category 2</strong></th>
			<th class="text-center" data-toggle="tooltip" title="Landing"><strong>Landing</strong></th>                
          </tr>
        </thead>
        <tbody id="content">
            <?php 
                foreach ($_data as $row) {       
                  $comment_category = getInfoById('core_offers','id,name',$row['category_id']);
                  $comment_category1 = getInfoById('core_comment_categories1','id,category_name',$row['category_id1']);                  
                  $comment_category2 = getInfoById('core_comment_categories2','id,category_name2',$row['category_id2']);                   
                  echo '<tr data-id='.$row["id"].'>';  
                  echo '<td class="text-center" width="100">';
                  echo '<a href="?route=addConmmentLanding&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';
                  echo '<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder"><i class="fas fa-times ml-1"></i></span>';
                  echo '</td>';
                  echo '<td class="text-center"><strong>'.$row["id"].'</strong></td>';    
                  if(!empty($comment_category)){
                      echo '<td class="text-center"><strong class="trigger green lighten-3">'.$comment_category["name"].'</strong></td>';
                  }else{
                      echo '<td></td>';
                  }
                  if(!empty($comment_category1)){
                      echo '<td class="text-center"><strong class="trigger green lighten-3">'.$comment_category1["category_name"].'</strong></td>';
                  }else{
                      echo '<td></td>';
                  }   
                  if(!empty($comment_category2)){
                      echo '<td class="text-center"><strong class="trigger green lighten-3">'.$comment_category2["category_name2"].'</strong></td>';    
                  }else{
                      echo '<td></td>'; 
                  }
                  echo '<td>'.$row["landing"].'</td>';
                echo '</tr>';  
              } 
            ?>
        </tbody>
      </table>
      <?php 
        if($_count > 10){
        ?> 
        <h3 class="load-more">Xem thêm</h3> 
        <?php 
        }
        ?>
      </div>           
    </div>
    </div>
</section>
<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="cancelOrder" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Xóa</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div data-id="">
          Bạn thực sự muốn xóa Comment Landing Page này không?
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
      <button class="btn btn-outline-danger" type="submit" name="cancelOrder">Xóa</button>
      <button class="btn btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: modalConfirmDelete-->
<div class="loader-overlay">
  <div class="loader-content-container">
    <div class="loader-content">
      <div class="spinner-grow" role="status" style="width: 6rem; height: 6rem;">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){    
  $("#pageSize").on('change',function(){
    var limit_curent = parseInt($(this).val());
    $("#limit").val((limit_curent));    
    limit = parseInt($(this).val());          
    $.ajax({
      cache:false,
      type:"POST",
      data:{limit : limit},            
      url:'<?=$_url;?>/ajax.php?act=pagination-commentLanding',
      success:function(html){  
        $("#content").html(html);  
        loadPagination(limit);                                 											
      }                                                          
    });	          
  }); 
  $(document).on('click', '.load-more', function(){    
      var limit_curent = parseInt($("#limit").val());          
      $("#limit").val((limit_curent + 10));                
      loadPagination(limit_curent + 10);           
  });
    $("body").on('click','[role=btn-cancelOrder]', function(){
        var $tr = $(this).parents('tr');
        $idOrder = [$tr.attr("data-id")];
    });
    $("#cancelOrder").on("click","[name=cancelOrder]",function(){    
        $(".loader-overlay").show();

        $.ajax({
            url: '<?=$_url;?>/ajax.php?act=cancelCommentLanding',
            dataType: 'json',
            data: {id: $idOrder.join(",")},
            type: 'post',
            success: function (response) {
                $(".loader-overlay").hide();
                if(response.status == 200){
                    toastr.success(response.message);
                    setTimeout(function(){
                    location.reload();
                    },500);
                } else{
                setTimeout(function(){
                    //location.reload();
                },500);
                toastr.error(response.message);
                }

            },
            error: function (response) {
                $(".loader-overlay").hide();
                toastr.error('Could not connect to API!');
            }
        });
    });
});
function loadPagination(limit){           
  $(".load-more").text("Load more");                            
  $.ajax({
    cache:false,
    type:"POST",
    data:{limit : limit},
    beforeSend:function(){
        $(".load-more").text("Loading...");
    },
    url:'<?=$_url;?>/ajax.php?act=pagination-commentLanding',
    success:function(html){  
      $("#content").html(html);             
      $(".load-more").text("Load more");
        var limit_check = parseInt($("#limit").val());
        var totalCount = parseInt($("#totalCount").val());    
        if(totalCount <= limit_check){
          $(".load-more").css("display",'none');
        }											
    }                                                          
  });	         
}
</script>