<?php




?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>

<h2 class="section-heading mb-4">Group Settings</h2>

<!-- Modal: newGroup -->
<div class="modal fade" id="newGroup" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">New Group</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

      	<form name="addOffer" class="noSubmit">
	      <div class="row">
	        <div class="col-md-6">
	          <div class="md-form form-sm mb-0">
	            <input id="i-name" type="text" class="form-control form-control-sm">
	            <label for="i-name" data-toggle="tooltip" title="Tên nhóm">Name</label>
	            <div class="invalid-feedback">Tên nhóm không được bỏ trống.</div>
	          </div>
	        </div>
	        <div class="col-md-6">
	          <div class="md-form form-sm mb-0" data-toggle="tooltip" title="Phân loại nhóm">
      				<select id="i-type" class="mdb-select  md-form">
      					<option disabled selected>Choose your Type</option>
      				  <?php

      				  foreach (typeGroup() as $k => $v) {
      					echo '<option value="'.$k.'">'._e($v).'</option>';
      				  }

      				  ?>
      				</select>
	            <div class="invalid-feedback">Vui lòng chọn một loại nhóm.</div>
	          </div>
	        </div>

	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0" data-toggle="tooltip" title="Loại sản phẩm cho phép">
      				<select id="i-offers" class="mdb-select  md-form" multiple>
      					<option disabled selected>Choose your offers</option>
      				  <?php

      				  foreach (getOffer() as $o) {
      					echo '<option value="'.$o['id'].'">'._e($o['name']).'</option>';
      				  }

      				  ?>
      				</select>
	            <div class="invalid-feedback">Vui lòng chọn một offer.</div>
	          </div>
	        </div>

	        <div class="col-md-6">
	          <div class="md-form form-sm payout-type-wrapper">
	            <label for="i-payout" data-toggle="tooltip" title="Tiền trả cho mỗi đơn hàng giao thành công">Payout:</label>
	            <div class="invalid-feedback">Payout phải lớn hơn hoặc bằng 0.</div>
                <div class="input-group">
                    <input id="i-payout" type="text" class="form-control form-control-sm">
                    <select id="i-payout_type" class="mdb-select md-form">
                        <?php
                            foreach (getPayoutTypes() as $key=>$val) {
                                echo '<option value="'.$key.'">'._e($val).'</option>';
                            }
                        ?>
                    </select>
                </div>
                
	          </div>
	        </div>

	        <div class="col-md-6">
	          <div class="md-form form-sm mb-0">
	            <input id="i-deduct" type="text" class="form-control form-control-sm">
	            <label for="i-deduct" data-toggle="tooltip" title="Tiền khấu trừ mỗi đơn hàng trả lại (đơn vị k)">Deduct: </label>
	            <div class="invalid-feedback">Khấu trừ phải lớn hơn hoặc bằng 0.</div>
	          </div>
	        </div>

	      </div>
      	</form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-dark waves-effect waves-light" type="submit">Add Group</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: newGroup -->


<!-- Modal: editGroup -->
<div class="modal fade" id="editGroup" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">Edit Offer</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">

      	<form name="addOffer" class="noSubmit">
	      <div class="row">
	        <div class="col-md-6">
	          <div class="md-form form-sm mb-0">
	            <input id="e-name" type="text" class="form-control form-control-sm">
	            <label for="e-name" data-toggle="tooltip" title="Tên nhóm">Tên nhóm</label>
	            <div class="invalid-feedback">Tên nhóm không được bỏ trống.</div>
	          </div>
	        </div>

	        <div class="col-md-6">
	          <div class="md-form form-sm mb-0" data-toggle="tooltip" title="Phân loại nhóm">
      				<select id="e-type" class="mdb-select md-form">
      					<option disabled selected>Choose your Type</option>
      				  <?php

      				  foreach (typeGroup() as $k => $v) {
      					echo '<option value="'.$k.'">'._e($v).'</option>';
      				  }

      				  ?>
      				</select>
	            <div class="invalid-feedback">Vui lòng chọn một loại nhóm.</div>
	          </div>
	        </div>


	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0" data-toggle="tooltip" title="Loại sản phẩm cho phép">
      				<select id="e-offers" class="mdb-select  md-form" multiple>
      					<option disabled selected>Choose your offers</option>
      				  <?php

      				  foreach (getOffer() as $o) {
      					echo '<option value="'.$o['id'].'">'._e($o['name']).'</option>';
      				  }

      				  ?>
      				</select>
	            <div class="invalid-feedback">Vui lòng chọn một offer.</div>
	          </div>
	        </div>

	        <div class="col-md-12">
	          <div class="md-form form-sm mb-0" data-toggle="tooltip" title="Trưởng nhóm">
      				<select id="e-leader" class="mdb-select  md-form">
      					<option disabled>Choose your leader</option>
      				</select>
	          </div>
	        </div>

	        <div class="col-md-6">
	          <div class="md-form form-sm payout-type-wrapper">
	            <label for="e-payout" data-toggle="tooltip" title="Tiền trả cho mỗi đơn hàng giao thành công">Leader Payout:</label>
	            <div class="invalid-feedback">Payout phải lớn hơn hoặc bằng 0.</div>
                <div class="input-group">
                    <input id="e-payout" type="text" class="form-control form-control-sm">
                    <select id="e-payout_type" class="mdb-select md-form">
                        <?php
                            foreach (getPayoutTypes() as $key=>$val) {
                                $selected = '';
                                echo '<option value="'.$key.'" '.$selected.'>'._e($val).'</option>';
                            }
                        ?>
                    </select>
                </div>
	          </div>
	        </div>

	        <div class="col-md-6">
	          <div class="md-form form-sm mb-0">
	            <input id="e-deduct" type="text" class="form-control form-control-sm">
	            <label for="e-deduct" data-toggle="tooltip" title="Tiền khấu trừ mỗi đơn hàng trả lại (đơn vị k)">Leader Deduct:</label>
	            <div class="invalid-feedback">Khấu trừ phải lớn hơn hoặc bằng 0.</div>
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
<!-- Modal: editGroup -->

<!--Modal: deleteGroup-->
<div class="modal fade" id="deleteGroup" tabindex="-1" role="dialog"
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
        <div id="deleteBody" data-id=""></div>
      </div>

      <!--Footer-->
      <div class="modal-footer flex-center">
        <button class="btn  btn-outline-danger" type="submit">Xóa</button>
        <button class="btn  btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
      </div>
    </div>
    <!--/.Content-->
  </div>
</div>
<!--Modal: deleteGroup-->


<!-- Modal: addMem -->
<div class="modal fade" id="addMem" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title">Add Member</h4>
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
              <label for="a-group"> Add to:</label>
              <input id="a-group" type="text" class="form-control form-control-sm" disabled="">
            </div>
          </div>

          <div class="col-md-12">
            <div class="md-form form-sm mb-0">
              <select id="a-members" class="mdb-select md-form" multiple>
                <option disabled selected>Choose a members</option>
              </select>
              <div class="invalid-feedback">Vui lòng chọn ít nhất một ai đó.</div>
            </div>
            <i class="text-danger">Chỉ có thể thêm những thành viên hiện tại không thuộc nhóm nào.</i>
          </div>

        </div>
        </form>

      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
        <button class="btn btn-dark waves-effect waves-light" type="submit">Add To Group</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: addMem -->


<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
    	<div class="text-right pb-2">
  			<button role="btn-newGroup" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" data-toggle="modal" data-target="#newGroup">New Group<i class="fas fa-plus-square ml-1"></i></button>
  		</div>
      <table id="dtBasicExample" class="table table-bordered table-sm table-hover" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="th-sm text-center">#ID</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tên nhóm">Name</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Kiểu nhóm">Type</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm có thể chạy" style="max-width:500px">Offers</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Trưởng nhóm">Leader</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tiền trả cho mỗi đơn hàng giao thành công">Leader Payout</th>
            <th class="th-sm text-center" data-toggle="tooltip" title="Tiền khấu trừ cho mỗi đơn hàng bị trả lại (đơn vị k)">Leader Deduct</th>
            <th class="th-sm text-center"></th>
          </tr>
        </thead>
        <tbody>
          <?php

            $_data = $_db->query("select * from `core_groups` order by `type` ")->fetch_array();

            if($_data){

              foreach ($_data as $arr) {
                $leader = getUser($arr['leader']);

                $leader = $arr['leader'] ? '<div class="chip align-middle"><a target="_blank" href="?route=statistics&user='.$leader['id'].'"><img src="'.getAvatar($leader['id']).'"> '.(!isBanned($leader) ? _e($leader['name']) : '<strike class="text-dark"><b>'._e($leader['name']).'</b></strike>').'</a></div>': ' Not set';

                $offers = getNameOffers($arr['offers']);
                $html_offer = "";
                foreach ($offers as $of)
                	$html_offer .= '<a class="trigger teal lighten-4 mx-1 mt-1 mb-1">'._e($of['name']).'</a>';

                $payout_type = $arr['payout_type']=='percent' ? '%' : 'K';

                echo '<tr class="'.($arr['id'] == $_user['group'] ? 'brown lighten-5 odd':'').'" data-id="'.$arr['id'].'" data-name="'._e($arr['name']).'" data-type="'._e($arr['type']).'" data-offers="'._e(trim(str_replace("|","",$arr['offers']),',')).'" data-payout="'._e($arr['payout']).'" data-payout_type="'._e($arr['payout_type']).'" data-deduct="'._e($arr['deduct']).'" data-leader="'._e($arr['leader']).'">
                        <td class="text-center"><i>'.$arr['id'].'</i></td>
                        <td>
                        	<a href="?route=group&id='.$arr['id'].'">
                        		<b class="number"> <i class="fas fa-external-link-alt ml-1"></i> '._e($arr['name']).'</b>
                        	</a>
                        </td>
                        <td><i>'.typeGroup($arr['type']).'</i></td>
                        <td style="max-width:500px"><div class="table-td-wrapper">'.$html_offer.'</div></td>
                        <td>'.$leader.'</td>
                        <td  class="text-center">'.($arr['payout'] > 0 ? '<b class="text-success number">'.number_format($arr['payout'], 0, ',', '.').'</b>':'0').$payout_type.'</td>
                        <td  class="text-center">'.($arr['deduct'] > 0 ? '<b class="text-danger number">'.number_format($arr['deduct'], 0, ',', '.').'</b>':'0').'</td>
                        <td>
                          <a target="_blank" href="?route=statistics&nav=groupStats&group='.$arr['id'].'">
                            <span class="btn btn-dark btn-sm waves-effect waves-light"><i class="w-fa fas fa-chart-line ml-1"></i></span>
                          </a>
                        	<button role="btn-editGroup" type="button" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light"  data-toggle="modal" data-target="#editGroup"> Edit <i class="fas fa-pen-square ml-1"></i></button>
                          <button role="btn-addMem" type="button" class="btn btn-dark btn-sm btn-rounded waves-effect waves-light"  data-toggle="modal" data-target="#addMem"> add Mem <i class="fas fa-users ml-1"></i></button>
                        	<button role="btn-deleteGroup" type="button" class="btn btn-danger btn-sm btn-rounded buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#deleteGroup"> Delete <i class="fas fa-times ml-1"></i></button>
                        </td>
                      </tr>';
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

		var $idGroup;

		$("body").on('click','button[role=btn-newGroup]', function(){

          	var name = $("#i-name"),
              	type = $("#i-type"),
              	offers = $("#i-offers"),
              	payout = $("#i-payout"),
              	payout_type = $("#i-payout_type"),
              	deduct = $("#i-deduct");

	        name.siblings(".invalid-feedback").hide();
	        type.parent().siblings(".invalid-feedback").hide();
	        offers.parent().siblings(".invalid-feedback").hide();
	        payout.siblings(".invalid-feedback").hide();
	        deduct.siblings(".invalid-feedback").hide();
          	name.val('');
          	offers.val('').trigger("change");
          	payout.val('0').trigger("change");
            payout_type.val('').trigger("change");
          	deduct.val('0').trigger("change");
		});
        $("#newGroup").on("click","button[type=submit]",function(){

        	var validate_number = /^\d+$/;

          	var name = $("#i-name"),
              	type = $("#i-type"),
              	offers = $("#i-offers"),
              	payout = $("#i-payout"),
              	payout_type = $("#i-payout_type"),
              	deduct = $("#i-deduct");

	          name.siblings(".invalid-feedback").hide();
	          type.parent().siblings(".invalid-feedback").hide();
	          offers.parent().siblings(".invalid-feedback").hide();
	          payout.siblings(".invalid-feedback").hide();
	          deduct.siblings(".invalid-feedback").hide();

	        if(!name.val().trim()){
	            name.siblings(".invalid-feedback").show();
	        } else if(!type.val()){
	        	type.parent().siblings(".invalid-feedback").show();
	        } else if(!payout.val().trim() || !validate_number.test(payout.val().trim()) || payout.val().trim() < 0){
	            payout.siblings(".invalid-feedback").show();
	        } else if(!deduct.val().trim() || !validate_number.test(deduct.val().trim()) || deduct.val().trim() < 0){
	            deduct.siblings(".invalid-feedback").show();
	        } else {

	            $(".loader-overlay").show();

	            $.ajax({
	                url: '<?=$_url;?>/ajax.php?act=admincp-newGroup',
	                dataType: 'json',
	                data: {name: name.val(), type: type.val(), payout: payout.val(), payout_type: payout_type.val(), deduct: deduct.val(), offers: offers.val().join(',')},
	                type: 'post',
	                success: function (response) {
	                	$(".loader-overlay").hide();
	               		if(response.status == 200){
	                    	toastr.success(response.message);
	                    	$('#newGroup').modal('hide');
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


    $("body").on('click','button[role=btn-addMem]', function(){

            var $tr = $(this).parents('tr');

            var member = $("#a-members");

            member.parent().siblings(".invalid-feedback").hide();

            member.val('').trigger("change");

            $("#a-group").val($tr.attr("data-name").trim()).trigger("change");

            $idGroup = $tr.attr("data-id").trim();

            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=admincp-memNoGroup',
                dataType: 'json',
                type: 'post',
                success: function (response) {

                  if(response.status == 200){
                    var html = '<option disabled selected>Choose a members</option>';
                      try {
                        for(var i in response.members)
                          html += '<option value="'+response.members[i].id+'" data-icon="'+response.members[i].avatar+'" class="rounded-circle">'+response.members[i].name+'</option>';

                        member.html(html);
                      } catch(error) {

                      }
                    member.materialSelect();
                  };
                },
                error: function (response) {
                }
            });
    });


        $("#addMem").on("click","button[type=submit]",function(){


            var member = $("#a-members");

            member.parent().siblings(".invalid-feedback").hide();

          if(!member.val() || member.val().length <= 0){
            member.parent().siblings(".invalid-feedback").show();
          } else {

              $(".loader-overlay").show();

              $.ajax({
                  url: '<?=$_url;?>/ajax.php?act=admincp-addMemToGroup',
                  dataType: 'json',
                  data: {id: $idGroup, member: member.val().join(',')},
                  type: 'post',
                  success: function (response) {
                    $(".loader-overlay").hide();
                    if(response.status == 200){
                        toastr.success(response.message);
                        $('#addMem').modal('hide');
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



		$("body").on('click','button[role=btn-editGroup]', function(){

            var $tr = $(this).parents('tr');

          	var name = $("#e-name"),
              	type = $("#e-type"),
              	offers = $("#e-offers"),
              	leader = $("#e-leader"),
              	payout = $("#e-payout"),
              	payout_type = $("#e-payout_type"),
              	deduct = $("#e-deduct");

	        name.siblings(".invalid-feedback").hide();
	        type.parent().siblings(".invalid-feedback").hide();
	        offers.parent().siblings(".invalid-feedback").hide();
	        payout.siblings(".invalid-feedback").hide();
	        deduct.siblings(".invalid-feedback").hide();
          	name.val($tr.attr('data-name').trim()).trigger("change");
          	type.val($tr.attr("data-type").trim()).trigger("change");
          	offers.val($tr.attr("data-offers").trim().split(",")).trigger("change");
          	payout.val($tr.attr('data-payout').trim()).trigger("change");
          	payout_type.val($tr.attr('data-payout_type').trim()).trigger("change");
          	deduct.val($tr.attr('data-deduct').trim()).trigger("change");


          	$idGroup = $tr.attr("data-id").trim();
            
            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=admincp-memGroup',
                dataType: 'json',
                data: {id: $idGroup},
                type: 'post',
                success: function (response) {

               		if(response.status == 200){
               			var html = '<option disabled selected>Choose your leader</option>';
                    	try {
                    		for(var i in response.members)
                    			html += '<option value="'+response.members[i].id+'" data-icon="'+response.members[i].avatar+'" class="rounded-circle">'+response.members[i].name+'</option>';

                    		$("#e-leader").html(html);
                    	} catch(error) {

                    	}
						$('#e-leader').materialSelect();
						leader.val($tr.attr("data-leader").trim()).trigger("change");
                	};
                },
                error: function (response) {
                }
            });
		});


        $("#editGroup").on("click","button[type=submit]",function(){

        	var validate_number = /^\d+$/;

          	var name = $("#e-name"),
              	type = $("#e-type"),
              	offers = $("#e-offers"),
              	payout = $("#e-payout"),
              	payout_type = $("#e-payout_type"),
              	leader = $("#e-leader"),
              	deduct = $("#e-deduct");

	        name.siblings(".invalid-feedback").hide();
	        type.parent().siblings(".invalid-feedback").hide();
	        offers.parent().siblings(".invalid-feedback").hide();
	        payout.siblings(".invalid-feedback").hide();
	        deduct.siblings(".invalid-feedback").hide();

	        if(!name.val().trim()){
	            name.siblings(".invalid-feedback").show();
	        } else if(!type.val()){
	        	type.parent().siblings(".invalid-feedback").show();
	        } else if(!offers.val() || offers.val().length <= 0){
	        	offers.parent().siblings(".invalid-feedback").show();
	        } else if(!payout.val().trim() || !validate_number.test(payout.val().trim()) || payout.val().trim() < 0){
	            payout.siblings(".invalid-feedback").show();
	        } else if(!deduct.val().trim() || !validate_number.test(deduct.val().trim()) || deduct.val().trim() < 0){
	            deduct.siblings(".invalid-feedback").show();
	        } else {

	            $(".loader-overlay").show();

	            $.ajax({
	                url: '<?=$_url;?>/ajax.php?act=admincp-editGroup',
	                dataType: 'json',
	                data: {id: $idGroup, name: name.val(), type: type.val(), offers: offers.val().join(','), payout: payout.val(), payout_type: payout_type.val(), deduct: deduct.val(), leader: leader.val()},
	                type: 'post',
	                success: function (response) {
	                	$(".loader-overlay").hide();
	               		if(response.status == 200){
	                    	toastr.success(response.message);
	                    	$('#editGroup').modal('hide');
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

		$("body").on('click','button[role=btn-deleteGroup]', function(){

            var $tr = $(this).parents('tr');
            $("#deleteBody").html('<p><b>Offer:</b> '+$tr.attr("data-name")+'</p><p>Mọi thành viên trong nhóm sẽ trở về trạng thái chưa kích hoạt và không thể nhận đơn mới!</p>');
            $idGroup = $tr.attr("data-id").trim();

		});	
        $("#deleteGroup").on("click","button[type=submit]",function(){

            $(".loader-overlay").show();

            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=admincp-deleteGroup',
                dataType: 'json',
                data: {id: $idGroup},
                type: 'post',
                success: function (response) {
                	$(".loader-overlay").hide();
               		if(response.status == 200){
                    	toastr.success(response.message);
                    	$('#deleteGroup').modal('hide');
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
        language: {emptyTable: "Group not found"},
	      info: false,
	      paging: false,
	      scrollX: true,
	      scrollY: true,
	      searching: false,
	      order: [ 2, 'asc' ],
	      responsive: false
	    });
	    $('.dataTables_length').addClass('bs-select');
	});
</script>