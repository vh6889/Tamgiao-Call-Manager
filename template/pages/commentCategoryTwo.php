<?php
$_count = count(getData('core_comment_categories2','id,category_id,category_id1,category_name2','','','id','desc',''));
$perPage = 15;
$_data = getData('core_comment_categories2','id,category_id,category_id1,category_name2','','','id','desc',$perPage);
$comment_category = getData('core_offers','id,name','','','id','desc','');
$comment_category1 = getData('core_comment_categories1','id,category_name','','','id','desc','');
?>
<section class="row">
  <div class="col-md-6">
    <h2 class="section-heading mb-4">Comment Category Sub 2 (<?php echo $_count; ?>)</h2>
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
        <button role="btn-newCommentCategory1" class="btn btn-primary" data-toggle="modal" data-target="#newCommentCategory1">Thêm mới <i class="fas fa-plus-square ml-1"></i></button>      
        </div>
      </div>               
      <div class="mb-5">  
      <input type="hidden" id="limit" name="limit" value="10">
    <input type="hidden" id="totalCount" name="limit" value="<?php echo $_count; ?>">
    <div class="postList" style="overflow-x:auto;margin-bottom:15px;">                                   
      <table style="margin-top:10px;" class="table table-hover table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>            
            <th class="text-center"></th>
            <th class="text-center" data-toggle="tooltip" title="ID Comment"><strong>#ID</strong></th>
            <th class="text-center" data-toggle="tooltip" title="Commtent Category"><strong>Comment Category</strong></th>
            <th class="text-center" data-toggle="tooltip" title="Commtent Category"><strong>Comment Category 1</strong></th>
			      <th class="text-center" data-toggle="tooltip" title="Trạng thái"><strong>Name</strong></th>                
          </tr>
        </thead>
        <tbody id="content">  
            <?php 
                foreach ($_data as $row) {       
                  $comment_category = getInfoById('core_offers','id,name',$row['category_id']);
                  $comment_category1 = getInfoById('core_comment_categories1','id,category_name',$row['category_id1']);
                  echo '<tr data-id='.$row["id"].'>';  
                  echo '<td class="text-center" width="100">';
                  echo '<a href="?route=editCommtentCategoryTwo&id='.$row["id"].'"><span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span></a>';
                  echo '<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder"><i class="fas fa-times ml-1"></i></span>';
                  echo '</td>';
                  echo '<td class="text-center"><strong>'.$row["id"].'</strong></td>';    
                  if(!empty($comment_category)){
                      echo '<td><strong class="trigger green lighten-3">'.$comment_category["name"].'</strong></td>';
                  }else{
                      echo '<td></td>';
                  }
                  if(!empty($comment_category1)){
                      echo '<td><strong class="trigger green lighten-3">'.$comment_category1["category_name"].'</strong></td>';
                  }else{
                      echo '<td></td>';
                  }    
                  echo '<td>'.$row["category_name2"].'</td>';
                echo '</tr>';  
              } 
            ?>   
		    </tbody>
      </table>
      </div>  
      <?php 
        if($_count > 10){
        ?> 
        <h3 class="load-more">Xem thêm</h3> 
        <?php 
        }
      ?>                
    </div>
    </div>
</section>
<!-- Modal: newOffer -->
<div class="modal fade" id="newCommentCategory1" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">Thêm mới</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

      	<form name="addOffer" class="noSubmit">
	      <div class="row">
	        <div class="col-md-12">
	          <div class="form-group">	            
                <select id="category_id" name="category_id" style="display:block;" class="form-control">
                  <option value="-1">Comment Category</option>  
                    <?php 
                        foreach($comment_category as $item_sub){
                        ?>
                        <option value="<?php echo $item_sub['id']; ?>"><?php echo $item_sub['name']; ?></option>
                        <?php
                        }
                    ?>                 	  
                </select>	            
	            <div class="invalid-feedback">Comment Categories chưa được chọn.</div>
	          </div>
	        </div>	        
          <div class="col-md-12">
	          <div class="form-group">	            
                <select id="category_id1" name="category_id1" style="display:block;" class="form-control select-item">
                  <option value="-1">Comment Category 1</option>  
                    <?php 
                        foreach($comment_category1 as $item_sub1){
                        ?>
                        <option value="<?php echo $item_sub1['id']; ?>"><?php echo $item_sub1['category_name']; ?></option>
                        <?php
                        }
                    ?>                 	  
                </select>	            
	            <div class="invalid-feedback">Comment Categories chưa được chọn.</div>
	          </div>
	        </div>	        
	        <div class="col-md-12">
	          <div class="form-group">
	            <input id="category_name" type="text" class="form-control form-control-sm" placeholder="Category Name">	            
                <div class="invalid-feedback">Category Name không được để trống.</div>
	          </div>
	        </div>
	      </div>
      	</form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Thêm mới</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: newOffer -->
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
            Bạn thực sự muốn xóa không?
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
      url:'<?=$_url;?>/ajax.php?act=pagination-commentCategoryTwo',
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
  $('#category_id').change(function() {
      giatri = this.value;
      $('#category_id1').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect&id=' + giatri);                    
  });
    $("body").on('click','button[role=btn-newCommentCategory1]', function(){
        var category_id = $("#category_id"), 
            category_id1 = $("#category_id1"),
            category_name = $("#category_name");

        category_id.siblings(".invalid-feedback").hide();	        
        category_id1.siblings(".invalid-feedback").hide();	   
        category_name.siblings(".invalid-feedback").hide();
        category_id.val('-1');          	
        category_id1.val('-1');   
        category_name.val('');
    });
    
    $("#newCommentCategory1").on("click","button[type=submit]",function(){                
          	var category_id = $("#category_id"),   
                category_id1 = $("#category_id1"),
                category_name = $("#category_name");
            category_id.siblings(".invalid-feedback").hide();	          
            category_name.siblings(".invalid-feedback").hide();  
            if(category_id.val()=="-1"){
                alert("Bạn chưa chọn Comment Category");
            }else if(!category_name.val().trim()){
                category_name.siblings(".invalid-feedback").show();
            }
            else {

	            $(".loader-overlay").show();

	            $.ajax({
	                url: '<?=$_url;?>/ajax.php?act=admincp-newCommentCategory2',
	                dataType: 'json',
	                data: {category_id: category_id.val(),category_id1 : category_id1.val(), category_name: category_name.val()},
	                type: 'post',
	                success: function (response) {
	                	$(".loader-overlay").hide();
	               		if(response.status == 200){
	                    	toastr.success(response.message);
	                    	$('#newCommentCategory1').modal('hide');
	                    	setTimeout(function(){
	                    		location.reload();
	                    	},500);
	                	} else
	                		toastr.error(response.message);
	                },
	                error: function (response) {
	                	$(".loader-overlay").hide();
	                	toastr.error('Could not connect to API!');
	                }
	            });
	        }
        });

    $("body").on('click','[role=btn-cancelOrder]', function(){
        var $tr = $(this).parents('tr');
        $idOrder = [$tr.attr("data-id")];
    });
    $("#cancelOrder").on("click","[name=cancelOrder]",function(){    
        $(".loader-overlay").show();

        $.ajax({
            url: '<?=$_url;?>/ajax.php?act=cancelCommentCategoryTwo',
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
    url:'<?=$_url;?>/ajax.php?act=pagination-commentCategoryTwo',
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