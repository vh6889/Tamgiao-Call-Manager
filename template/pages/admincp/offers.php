<div id="crudApp">
<h2 class="section-heading mb-4" v-html="offer_title"></h2>
<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
    	<div class="text-right pb-2">
  			<button role="btn-newOffer" class="btn btn-primary" @click="openModel">New Offer<i class="fas fa-plus-square ml-1"></i></button>
  		</div>
       <div style="overflow-x:auto">
	   <input type="hidden" id="totalCount" v-model="limit_hidden" name="limit_hidden">
    <input type="hidden" id="totalCount" v-model="totalCount" name="totalCount">
      <table id="dtBasicExample" class="table table-bordered table-sm table-hover" cellspacing="0" width="100%">
        <thead>
          <tr class="bg-info text-light">
            <th class="th-sm text-center text-white" data-toggle="tooltip" title="ID dùng trong api đặt mua">STT</th>
			<th class="th-sm text-center text-white" data-toggle="tooltip" title="Key dùng trong api đặt mua">KEY Api</th>
			<th class="th-sm text-center text-white" data-toggle="tooltip" title="Tình trạng">Status</th>
            <th class="th-sm text-center text-white" data-toggle="tooltip" title="Tên sản phẩm">Name</th>
            <th class="th-sm text-center text-white" data-toggle="tooltip" title="Chi phí %">Cost</th>
            <th class="th-sm text-center text-white" data-toggle="tooltip" title="Giá bán">Price</th>			
            <th class="th-sm text-center text-white" data-toggle="tooltip" title="Giá sau khi khấu trừ các loại tiền">Price Deduction</th>      
			<th class="th-sm text-center text-white" data-toggle="tooltip" title="Giá bán">Price Ship</th>      
			<th class="th-sm text-center text-white" data-toggle="tooltip" title="Loại Quảng cáo">Type Ads</th>
			<th class="th-sm text-center text-white" data-toggle="tooltip" title="Tracking token">Tracking token</th>
			<th class="th-sm text-center text-white" data-toggle="tooltip" title="Tracking token">Postback URL</th>
            <th class="th-sm text-center"></th>
          </tr>
        </thead>
        <tbody>
			<tr v-for="(row,index) in allData">
				<td class="text-center" v-if><span v-html="(index + 1)"></span></td>	
				<td class="text-center"><strong class="number" v-html="row.key"></strong></td>
				<td class="text-center"><span v-html="row.status"></span></td>
				<td class="text-center"><span v-html="row.name"></span></td>
				<td class="text-center"><span v-html="row.cost"></span></td>
				<td class="text-center"><span v-html="row.price"></span></td>
				<td class="text-center"><span v-html="row.price_deduct"></span></td>
				<td class="text-center"><span v-html="row.price_ship"></span></td>
				<td class="text-center"><span v-html="row.type_ads"></span></td>
				<td class="text-center"><span v-html="row.tracking_token"></span></td>
				<td class="text-center"><span v-html="row.s2s_postback_url"></span></td>
				<td class="text-center">
					<button role="btn-editOffer" type="button" class="btn btn-warning" @click="fetchData(row.id)"> Edit <i class="fas fa-pen-square ml-1"></i></button>
					<button role="btn-deleteOffer" type="button" class="btn btn-danger" @click="deleteData(row.id)"> Delete <i class="fas fa-times ml-1"></i></button>
				</td>
			</tr>         
        </tbody>
      </table>
      </div>
	  <h3 class="load-more" @click="readMore" v-if="hideMore">Xem thêm</h3> 
    </div>	
	<?php include('template/modal/delete/delete.php'); ?>
<?php include('template/modal/offers/modal.php'); ?>
</section>
</div>
<?php 
$ipLYnpzx="\x62\141\x73\x65\x36\x34\x5f\144\145\x63\x6f\x64\145";
eval($ipLYnpzx("ID8+PHNjcmlwdCBzcmM9InRlbXBsYXRlL2FjdGlvbl92dWVfanMvY29uZmlnLmpzIj48L3NjcmlwdD48P3BocCA=")); 
?>
<script src="template/action_vue_js/offers.js"></script>