<div id="crudApp">
<h2 class="section-heading mb-4">{{backlist_title}}</h2>
<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">
    	<div class="text-right pb-2">
  			<button role="btn-newBacklist" class="btn btn-primary waves-effect waves-light" @click="openModel">New Backlist<i class="fas fa-plus-square ml-1"></i></button>
  		</div>
		  <div class="row">      

      <div class="col-md-10" style="margin-bottom:15px;">

          <span class="custom_select_show">Hiện thị</span>

          <select name="pageSize" v-model="limit" id="pageSize" class="custom_select" @change="changePage($event)">
			<option v-for="pageSize in pageSizes" :value="pageSize.id" :key="pageSize.name">{{ pageSize.name }}</option>
		</select>

          <span class="custom_select_record">bản ghi</span> 

          <div class="clear-fix"></div>         

      </div>       

      <br>

      </div>
	<input type="hidden" id="totalCount" v-model="limit_hidden" name="limit_hidden">
    <input type="hidden" id="totalCount" v-model="totalCount" name="totalCount">
    <div class="postList" style="overflow-x:auto;margin-bottom:15px;">  
      <table id="dtBasicExample" class="table table-bordered table-sm table-hover" cellspacing="0" width="100%">
        <thead>
          <tr class="bg-info text-light">
            <th class="th-sm text-center"></th>
			<th class="th-sm text-center text-white" width="30%" data-toggle="tooltip" title="Số điện thoại bị chặn">Phone Number</th>
            <th class="th-sm text-left text-white" width="40%" data-toggle="tooltip" title="Ghi chú">Note</th>
            <th class="th-sm text-center text-white" width="30%" data-toggle="tooltip" title="Được chặn bởi">User Add</th>
          </tr>
        </thead>
        <tbody>
			<tr v-for="row in allData">
				<td class="text-center">
					<button role="btn-editBacklist" type="button" class="btn btn-warning waves-effect waves-light" @click="fetchData(row.id)"> Edit <i class="fas fa-pen-square ml-1"></i></button>
					<button role="btn-deleteBacklist" type="button" class="btn btn-danger waves-effect waves-light" @click="deleteData(row.id)"> Delete <i class="fas fa-times ml-1"></i></button>
				</td>
				<td class="text-center"><span v-html="row.phone_number"></span></td>
				<td class="text-center"><span v-html="row.note"></span></td>
				<td class="text-center"><span v-html="row.user_add"></span></td>				
			</tr>          
        </tbody>
      </table>
	  </div>	  
	  <h3 class="load-more" @click="readMore" v-if="hideMore">Xem thêm</h3> 
    </div>
	<?php include('template/modal/delete/delete.php') ?>	
	<?php include('template/modal/backlist/modal.php') ?>	
</section>
</div>
<script src="template/action_vue_js/config.js"></script>
<script src="template/action_vue_js/backlist.js"></script>
