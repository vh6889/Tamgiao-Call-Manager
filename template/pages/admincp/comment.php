<?php




?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>


<h2 class="section-heading mb-4">Comment Settings</h2>


<!-- Modal: newOffer -->
<div class="modal fade" id="newCommentCategory" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">Add Comment Categories</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

      	<form name="addOffer" class="noSubmit">
	      <div class="row">
	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0">
	            <input id="i-name" type="text" class="form-control form-control-sm">
	            <label for="i-name" data-toggle="tooltip" title="Tên sản phẩm">Name</label>
	            <div class="invalid-feedback">Tên Comment Categories không được để trống trống.</div>
	          </div>
	        </div>	        

	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0">
	            <input id="i-key" type="text" class="form-control form-control-sm">
	            <label for="i-key" data-toggle="tooltip" title="Key dùng để so sánh trong api(có thể bỏ trống)">Key Api</label>
                <div class="invalid-feedback">Key Api không được để trống.</div>
	          </div>
	        </div>
	      </div>
      	</form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-dark waves-effect waves-light" type="submit">Add Comment Categories</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: newOffer -->


<!-- Modal: editOffer -->
<div class="modal fade" id="editCommentCategory" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">Edit Comment Category</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

      	<form name="editCommentCategory" class="noSubmit" data-id="">
	      <div class="row">
	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0">
	            <input id="e-name" type="text" class="form-control form-control-sm">
	            <label for="e-name" data-toggle="tooltip" title="Tên sản phẩm">Name</label>
	            <div class="invalid-feedback">Tên Comment Category không được để trống.</div>
	          </div>
	        </div>	        
	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0">
	            <input style="background:#ddd;" id="e-key" type="text" class="form-control form-control-sm" readonly>
	            <label style="padding-bottom:5px;" for="e-key" data-toggle="tooltip" title="Key dùng để so sánh trong api(có thể bỏ trống)">Key Api</label>
                <div class="invalid-feedback">Key Api không được để trống.</div>
	          </div>
	        </div>

	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0" data-toggle="tooltip" title="Trạng thái sản phẩm">
				<select id="e-status" class="mdb-select  md-form">
					<option disabled>Status:</option>
				  <?php

				  foreach (typeCommentCategory() as $k => $v) {
					echo '<option value="'.$k.'">'.$v.'</option>';
				  }

				  ?>
				</select>
	            <div class="invalid-feedback">Vui lòng chọn một trạng thái.</div>
	          </div>
	        </div>

	      </div>
      	</form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-dark waves-effect waves-light" type="submit">Save</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: editOffer -->

<!--Modal: modalConfirmDelete-->
<div class="modal fade" id="deleteCommentCategory" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
    <!--Content-->
    <div class="modal-content text-center">
      <!--Header-->
      <div class="modal-header d-flex justify-content-center">
        <p class="heading">Bạn thực sự muốn xóa?</p>
      </div>

      <!--Body-->
      <div class="modal-body">

        <i class="fas fa-times fa-4x animated rotateIn"></i>
        <div id="deleteBody" data-id="">
        </div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
        <button class="btn btn-outline-danger" type="submit">Xóa</button>
        <button class="btn btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: modalConfirmDelete-->

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
    	<div class="text-right pb-2">
  			<button role="btn-newCommentCategory" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" data-toggle="modal" data-target="#newCommentCategory">New Comment Category<i class="fas fa-plus-square ml-1"></i></button>
  		</div>
      <table id="dtBasicExample" class="table table-bordered table-sm table-hover" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="th-sm text-center" data-toggle="tooltip" title="ID dùng trong api đặt mua">#ID</th>
			<th class="th-sm text-center" data-toggle="tooltip" title="Tình trạng">Status</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tên sản phẩm">Name</th>                        
            <th class="th-sm text-center" data-toggle="tooltip" title="Key dùng trong api đặt mua">KEY Api</th>
            <th class="th-sm text-center"></th>
          </tr>
        </thead>
        <tbody>
          <?php

            $_data = $_db->query("select * from `core_comment_categories` order by `id` DESC ")->fetch_array();

            if($_data){

              foreach ($_data as $arr) {
                $status = $arr['status'] == "1" ? '<span class="text-success">Running</span>' : '<span class="text-danger">Stop</span>';                
                ?>                
                <tr data-id="<?php echo $arr['id'];?>" data-name="<?php echo _e($arr['name']);?>" data-status="<?php echo $arr['status'];?>" data-key="<?php echo _e($arr['key_api']);?>">
                    <td class="text-center"><?php echo $arr['id'];?></td>
                    <td class="text-center"><?php echo $status; ?></td>
                    <td class="text-center"><b style="color:#fff;" class="trigger <?php if($arr['status'] == '1'){ echo 'green';}else{ echo 'red';} ?>"><?php echo $arr["name"]; ?></b></td>
                    <td class="text-center"><b class="number"><?php echo $arr["key_api"]; ?></b></td>
                    <td class="text-center">
                        <button role="btn-editCommentCategory" type="button" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light"  data-toggle="modal" data-target="#editCommentCategory"> Edit <i class="fas fa-pen-square ml-1"></i></button>
                        <button role="btn-deleteCommentCategory" type="button" class="btn btn-danger btn-sm btn-rounded buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#deleteCommentCategory"> Delete <i class="fas fa-times ml-1"></i></button>
                    </td>
                <tr>
                <?php
              }

            }

          ?>
        </tbody>
      </table>
    </div>

</section>

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

		$("body").on('click','button[role=btn-newCommentCategory]', function(){

          	var name = $("#i-name"),              	
              	key = $("#i-key");

	        name.siblings(".invalid-feedback").hide();	        
	        key.siblings(".invalid-feedback").hide();
          	name.val('');          	
          	key.val('');
		});
        $("#newCommentCategory").on("click","button[type=submit]",function(){            
        	var validate_number = /^\d+$/;

          	var name = $("#i-name"),              	
              	key = $("#i-key");
	          name.siblings(".invalid-feedback").hide();	          
              key.siblings(".invalid-feedback").hide();  
	        if(!name.val().trim()){
	            name.siblings(".invalid-feedback").show();
	        } else if(!key.val().trim()){
                key.siblings(".invalid-feedback").show();
            }
            else {

	            $(".loader-overlay").show();

	            $.ajax({
	                url: '<?=$_url;?>/ajax.php?act=admincp-newCommentCategory',
	                dataType: 'json',
	                data: {name: name.val(), key: key.val()},
	                type: 'post',
	                success: function (response) {
	                	$(".loader-overlay").hide();
	               		if(response.status == 200){
	                    	toastr.success(response.message);
	                    	$('#newCommentCategory').modal('hide');
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


		var $idOffer;
		$("body").on('click','button[role=btn-editCommentCategory]', function(){


          	var name = $("#e-name"),
          		status = $("#e-status"),              	
              	key = $("#e-key");

            var $tr = $(this).parents('tr');
	        name.siblings(".invalid-feedback").hide();
	        status.parent().siblings(".invalid-feedback").hide();	        
	        status.val($tr.attr("data-status").trim()).trigger("change");
          	name.val($tr.attr('data-name').trim()).trigger("change");          	
          	key.val($tr.attr('data-key').trim()).trigger("change");

          	$idOffer = $tr.attr("data-id").trim();

		});

        $("#editCommentCategory").on("click","button[type=submit]",function(){    
               
        	var validate_number = /^\d+$/;

          	var name = $("#e-name"),
          		status = $("#e-status"),              	
              	key = $("#e-key");

	          name.siblings(".invalid-feedback").hide();
	          status.parent().siblings(".invalid-feedback").hide();
	          key.siblings(".invalid-feedback").hide();	          

	        if(!name.val().trim()){
	            name.siblings(".invalid-feedback").show();
	        } else if(!key.val().trim()){
                key.siblings(".invalid-feedback").show();
            }else if(!status.val()){
	        	status.parent().siblings(".invalid-feedback").show();
	        } else {
	            $(".loader-overlay").show();

	            $.ajax({
	                url: '<?=$_url;?>/ajax.php?act=admincp-editCommentCategory',
	                dataType: 'json',
	                data: {id : $idOffer, name : name.val(), status : status.val(), key : key.val()},
	                type: 'post',
	                success: function (response) {
	                	$(".loader-overlay").hide();
	               		if(response.status == 200){
	                    	toastr.success(response.message);
	                    	$('#editCommentCategory').modal('hide');
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

		$("body").on('click','button[role=btn-deleteCommentCategory]', function(){

            var $tr = $(this).parents('tr');
            $("#deleteBody").html('<b>Comment category:</b> '+$tr.attr("data-name"));
            $idOffer = $tr.attr("data-id").trim();

		});	
        $("#deleteCommentCategory").on("click","button[type=submit]",function(){

            $(".loader-overlay").show();

            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=admincp-deleteCommentCategory',
                dataType: 'json',
                data: {id: $idOffer},
                type: 'post',
                success: function (response) {
                	$(".loader-overlay").hide();
               		if(response.status == 200){
                    	toastr.success(response.message);
                    	$('#deleteCommentCategory').modal('hide');
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

        });

	    $('#dtBasicExample').DataTable({
	    	columnDefs: [{ targets: [5,6], orderable: false }],
        	language: {emptyTable: "Offer not found."},
	      info: false,
	      paging: false,
	      scrollX: true,
	      scrollY: true,
	      searching: false,
	      order: [ 1, 'asc' ],
	      responsive: false
	    });
	    $('.dataTables_length').addClass('bs-select');
	});
</script>