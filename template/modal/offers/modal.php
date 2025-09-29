<div v-if="myModel">
	<transition name="model">
		<div class="modal-mask">
			<div class="modal-wrapper">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">{{ dynamicTitle }}</h4>
							<button type="button" class="close" @click="myModel=false"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<p v-if="errors.length">
								<b>Please correct the following error(s):</b>
							<ul class="text-danger" style="padding-left:17px;">
								<li v-for="error in errors"><span class="text-danger" v-html="error"></span></li>
							</ul>
							</p>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Name</label>
										<input type="text" class="form-control" v-model="name" placeholder="Name" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Cost % (Sale,marketing...)</label>
										<input type="number" class="form-control" v-model="cost" placeholder="Cost" />
									</div>
								</div>
								<div class="col-md-4">
									<label for="e-payout" data-toggle="tooltip" title="Tiền trả cho mỗi đơn hàng giao thành công. Nếu không nhập thì sẽ sử dụng payout được set theo nhóm của user.">Payout:</label>
									<div class="input-group">
										<input id="payout" v-model="payout" type="text" class="form-control form-control-sm">
										<div class="input-group-append">
											<select id="payout_type" class="form-control form-control-sm" v-model="payout_type" name="payout_type">
												<?php
												foreach (getPayoutTypes() as $key => $val) {
													$selected = '';
													echo '<option value="' . $key . '" ' . $selected . '>' . _e($val) . '</option>';
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Price Bonus</label>
										<input type="text" class="form-control" v-model="price_bonus" placeholder="Price Bonus" />
										<small class="form-text text-muted text-danger">Mức thưởng thêm cho mỗi sản phẩm tự sale.</small>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Price</label>
										<input type="text" class="form-control" v-model="price" placeholder="Price" />
										<small class="form-text text-muted text-danger">Nhập các mức giá ngăn cách bởi dấu "|".</small>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Price Deduction</label>
										<input type="number" class="form-control" v-model="price_deduct" placeholder="Price Deduction" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Price Ship:</label>
										<input type="number" class="form-control" v-model="price_ship" placeholder="Price Ship" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Key Api</label>
										<input type="text" class="form-control" v-model="key" placeholder="Key Api" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Type Ads</label>
										<select class="form-control" v-model="type_ads" id="type_ads" name="type_ads">
											<option value="0">Type Ads</option>
											<option v-for="data in ads" :value="data.id">{{data.ads}}</option>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Tracking token</label>
										<input type="text" class="form-control" v-model="tracking_token" placeholder="Tracking token" />
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label>Postback URL</label>
										<input type="text" class="form-control" v-model="s2s_postback_url" placeholder="Postback URL" />
									</div>
								</div>
								<div v-if="hideStatus" class="col-md-12">
									<div class="form-group">
										<label>Status</label>
										<select class="form-control" v-model="status" id="status" name="status">
											<option value="0">Status</option>
											<option v-for="statusOffter in statusOffters" :value="statusOffter.id" :key="statusOffter.name">{{ statusOffter.name }}</option>
										</select>
									</div>
								</div>
							</div>

							<br />
							<div align="center">
								<input type="hidden" v-model="hiddenId" />
								<button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal" @click="myModel=false">Hủy bỏ</button>
								<input type="button" class="btn btn-success btn-xs" v-model="actionButton" @click="submitData" />
							</div>
						</div>
					</div>
				</div>
			</div>
	</transition>
</div>