<div id="crudApp">
	<h2 class="section-heading mb-4" v-html="ads_title"></h2>
	<section class="row mb-5 pb-3">
		<div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
			<div class="text-right pb-2">
				<button role="btn-newBacklist" class="btn btn-primary waves-effect waves-light" @click="openModel">New Ads<i class="fas fa-plus-square ml-1"></i></button>
			</div>							
			<div class="postList" style="overflow-x:auto;margin-bottom:15px">  
				<table class="table table-bordered table-sm table-hover" cellspacing="0" width="100%">
					<thead>
					<tr class="bg-info text-light">
						<th class="th-sm text-center"></th>
						<th class="th-sm text-center text-white" width="30%" data-toggle="tooltip" title="Số điện thoại bị chặn">Id</th>
						<th class="th-sm text-left text-white" width="40%" data-toggle="tooltip" title="Ghi chú">Ads</th>            
					</tr>
					</thead>
					<tbody>
						<tr v-for="row in allData">
							<td class="text-center">
								<button role="btn-editAds" type="button" class="btn btn-warning" @click="fetchData(row.id)"> Edit <i class="fas fa-pen-square ml-1"></i></button>
								<button role="btn-deleteAds" type="button" class="btn btn-danger" @click="deleteData(row.id)"> Delete <i class="fas fa-times ml-1"></i></button>						
							</td>	
							<td class="text-center">Ads #<span v-html="row.id"></span></td>
							<td class="text-left"><span v-html="row.ads"></span></td> 
						</tr>			
					</tbody>
				</table>
			</div>				
		</div>			
	</section>
<?php include('template/modal/delete/delete.php'); ?>			
		<?php include('template/modal/ads/modal.php'); ?>
<div>
<script src="template/action_vue_js/config.js"></script>
<script src="template/action_vue_js/ads.js"></script>