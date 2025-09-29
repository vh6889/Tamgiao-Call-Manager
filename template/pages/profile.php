
<!-- Section: Team v.1 -->
<section class="section team-section mb-4">

  <h2 class="section-heading mb-4">Settings</h2>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security"
      aria-selected="true">Security</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#payment" role="tab" aria-controls="payment"
      aria-selected="false">Payment</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="security" role="tabpanel" aria-labelledby="security-tab">
    <form name="changePass" class="noSubmit">
      <div class="row">
        <div class="col-md-3">
          <div class="md-form form-sm mb-0">
            <input id="i-pass" type="password" class="form-control form-control-sm">
            <label for="i-pass">Old password</label>
            <div class="invalid-feedback">Password entered incorrectly.</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="md-form form-sm mb-0">
            <input id="i-pass_new" type="password" class="form-control form-control-sm">
            <label for="i-pass_new">New password</label>
            <div class="invalid-feedback">Please enter a New password.</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="md-form form-sm mb-0">
            <input id="i-pass_re" type="password" class="form-control form-control-sm">
            <label for="i-pass_re">Repeat new password</label>
            <div class="invalid-feedback">rePassword entered incorrectly.</div>
          </div>
        </div>
        <div class="col-md-3">
          <button class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" type="submit">Change Password</button>
        </div>
      </div>
    </form>
  </div>
  <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
    <p class="text-black">- Vui lòng điền chính xác thông tin bên dưới để có thể nhận được thanh toán.</p>
    <p class="text-black">- Không chịu trách nhiệm nếu thông tin điền vào không chính xác.</p>
    <form name="payment" class="noSubmit">
      <div class="row">
        <div class="col-md-4">
          <div class="md-form form-sm mb-0">
            <input id="i-payment_bank" type="text" class="form-control form-control-sm" value="<?=_e($_user['payment_bank']);?>">
            <label for="i-payment_bank">*Ngân hàng</label>
            <div class="invalid-feedback">Ngân hàng không được bỏ trống.</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="md-form form-sm mb-0">
            <input id="i-payment_branch" type="text" class="form-control form-control-sm" value="<?=_e($_user['payment_branch']);?>">
            <label for="i-payment_branch">*Chi nhánh</label>
            <div class="invalid-feedback">Chi nhánh ngân hàng không được bỏ trống.</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="md-form form-sm mb-0">
            <input id="i-payment_number" type="text" class="form-control form-control-sm" value="<?=_e($_user['payment_number']);?>">
            <label for="i-payment_number">*Số tài khoản</label>
            <div class="invalid-feedback">Số tài khoản không được bỏ trống.</div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="md-form form-sm mb-0">
            <input id="i-payment_name" type="text" class="form-control form-control-sm" value="<?=_e($_user['payment_name']);?>">
            <label for="i-payment_name">*Tên chủ tài khoản</label>
            <div class="invalid-feedback">Tên chủ tài khoản.</div>
          </div>
        </div>
        <div class="col-md-12">
          <button class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" type="submit">Save payment settings</button>
          <button class="btn btn-outline-dark btn-sm btn-rounded waves-effect waves-light" type="delete">Delete payment settings</button>
        </div>
      </div>
    </form>
  </div>
</div>

  <!-- Grid row -->
  <div class="row text-center">

    <!-- Grid column -->
    <div class="col-md-8 mb-4">

      <!-- Card -->
      <div class="card card-cascade cascading-admin-card">

        <!-- Card content -->
        <div class="card-body card-body-cascade">

          <form name="profile" class="noSubmit">
          <!-- Grid row -->
          <div class="row">
            <!-- Grid column -->
            <div class="col-lg-4">

              <div class="md-form form-sm mb-0">
                <input type="text" class="form-control form-control-sm" value="<?=_e($_user['name']);?>" disabled>
                <label class="">Username</label>
              </div>

            </div>
            <!-- Grid column -->

            <!-- Grid column -->
            <div class="col-lg-4">

              <div class="md-form form-sm mb-0">
                <input id="i-mail" type="text" class="form-control form-control-sm" name="mail" value="<?=_e($_user['mail']);?>">
                <label for="i-mail">Gmail</label>
                <div class="invalid-feedback">Please enter a gmail address.</div>
              </div>

            </div>
            <!-- Grid column -->

            <!-- Grid column -->
            <div class="col-lg-4">

              <div class="md-form form-sm mb-0">
                <input id="i-phone" type="text" class="form-control form-control-sm" name="phone" value="<?=_e($_user['phone']);?>">
                <label for="i-phone">Phone</label>
                <div class="invalid-feedback">Incorrect phone number format.</div>
              </div>

            </div>
            <!-- Grid column -->


          </div>
          <!-- Grid row -->

          <!-- Grid row -->
          <div class="row">

            <!-- Grid column -->
            <div class="col-md-12">

              <div class="md-form form-sm mb-0">
                <input id="i-full_name" type="text" class="form-control form-control-sm" name="full_name" value="<?=_e($_user['full_name']);?>">
                <label for="i-full_name" class="">Full Name</label>
              </div>

            </div>
            <!-- Grid column -->

          </div>
          <!-- Grid row -->
          <div class="row text-left">
            <div class="form-row my-4">
              <div class="form-check pl-4">
                <input class="form-check-input" type="checkbox" value="1" id="i-notifi" <?=($_user['notifi'] == 1 ? 'checked':'');?>>
                <label class="form-check-label text-black-50" for="i-notifi">Nhận thông báo qua mail</label>
                <p class="text-black-50">(Nhập chính xác gmail để có thể nhận thông báo.)</p>
              </div>
            </div>
          </div>

          <div class="row text-right">
            <button class="btn btn-dark btn-md btn-rounded waves-effect waves-light" type="submit">Save</button>
          </div>

          </form>

        </div>
        <!-- Card content -->

      </div>
      <!-- Card -->

    </div>
    <!-- Grid column -->

    <!-- Grid column -->
    <div class="col-md-4 mb-4">

      <!-- Card -->
      <div class="card profile-card">

        <!-- Avatar -->
        <div class="avatar z-depth-1-half mb-4">
          <img id="urlAvatar" src="<?=getAvatar($_user['id']);?>" class="rounded-circle" width="150" height="150">
        </div>

        <div class="card-body pt-0 mt-0">

          <input type="file" id="fileAvatar" name="fileAvatar" style="display: none;" />
          <button class="btn btn-dark btn-sm btn-rounded waves-effect waves-light" id="changeAvatar"> Change Avatar</button>

        </div>

      </div>
      <!-- Card -->

    </div>
    <!-- Grid column -->

  </div>
  <!-- Grid row -->

</section>
<script type="text/javascript">
    $(document).ready(function (e) {
        $('#changeAvatar').on('click', function () {
            $('#fileAvatar').click();
        });

        $('#fileAvatar').change(function() {

          var curr_btn = $("#changeAvatar").html();

            $("#changeAvatar").prop('disabled', true);
            $("#changeAvatar").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="sr-only">Uploading...</span>');

            var file_data = $('#fileAvatar').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=profile-uploadAvatar',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                  if(response.status == 200){
                    $("#urlAvatar").attr("src",response.url);
                    toastr.success(response.message);
                  }
                  else
                    toastr.error(response.message);

                  $("#changeAvatar").prop('disabled', false);
                  $("#changeAvatar").html(curr_btn);
                },
                error: function (response) {
                  toastr.error('Could not connect to API!');
                  $("#changeAvatar").prop('disabled', false);
                  $("#changeAvatar").html(curr_btn);
                }
            });
        });



        $("form[name=profile]").on("click","button[type=submit]",function(){

          var validate_gmail = /\S+@([gmail|GMAIL])+\.([com|COM])+/;
          var validate_phone = /^[0-9-+]+$/;

          var mail = $("#i-mail"),
              phone = $("#i-phone"),
              name = $("#i-full_name"),
              notifi = $("#i-notifi");

          mail.siblings(".invalid-feedback").hide();
          phone.siblings(".invalid-feedback").hide();

          if(!mail.val().trim() || !validate_gmail.test(mail.val().trim()) ){
            mail.siblings(".invalid-feedback").show();
          } else if(phone.val().trim() && !validate_phone.test(phone.val().trim()) ){
            phone.siblings(".invalid-feedback").show();
          } else {

            var $this = $(this);
            $this.prop('disabled', true);
            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'+$this.html());
            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=profile-updateProfile',
                dataType: 'json',
                data: {name: name.val(), mail: mail.val(), phone: phone.val(), notifi: notifi.is(':checked')},
                type: 'post',
                success: function (response) {
                  if(response.status == 200){
                    toastr.success(response.message);
                  } else
                    toastr.error(response.message);
                  $this.html('Save');
                  $this.prop('disabled', false);
                },
                error: function (response) {
                  toastr.error('Could not connect to API!');
                  $this.prop('disabled', false);
                  $this.html('Save');
                }
            });
          }
        });


        $("form[name=changePass]").on("click","button[type=submit]",function(){


          var pass = $("#i-pass"),
              passNew = $("#i-pass_new"),
              passRe = $("#i-pass_re");

          pass.siblings(".invalid-feedback").hide();
          passNew.siblings(".invalid-feedback").hide();
          passRe.siblings(".invalid-feedback").hide();

          if(!pass.val().trim()){
            pass.siblings(".invalid-feedback").show();
          } else if(!passNew.val().trim()){
            passNew.siblings(".invalid-feedback").show();
          } else if(!passRe.val().trim()){
            passRe.siblings(".invalid-feedback").show();
          } else {

            var $this = $(this);
            $this.prop('disabled', true);
            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'+$this.html());
            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=profile-changePass',
                dataType: 'json',
                data: {pass: pass.val(), passNew: passNew.val(), passRe: passRe.val()},
                type: 'post',
                success: function (response) {
                  if(response.status == 200){
                    toastr.success(response.message);
                  } else
                    toastr.error(response.message);
                  $this.html('Change Password');
                  $this.prop('disabled', false);
                },
                error: function (response) {
                  toastr.error('Could not connect to API!');
                  $this.prop('disabled', false);
                  $this.html('Change Password');
                }
            });
          }
        });

        $("form[name=payment]").on("click","button[type=submit]",function(){


          var bank = $("#i-payment_bank"),
              name = $("#i-payment_name"),
              branch = $("#i-payment_branch"),
              number = $("#i-payment_number");

          bank.siblings(".invalid-feedback").hide();
          name.siblings(".invalid-feedback").hide();
          branch.siblings(".invalid-feedback").hide();
          number.siblings(".invalid-feedback").hide();

          if(!bank.val().trim()){
            bank.siblings(".invalid-feedback").show();
          } else if(!branch.val().trim()){
            branch.siblings(".invalid-feedback").show();
          } else if(!number.val().trim()){
            number.siblings(".invalid-feedback").show();
          } else if(!name.val().trim()){
            name.siblings(".invalid-feedback").show();
          } else {

            var $this = $(this);
            $this.prop('disabled', true);
            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'+$this.html());
            $.ajax({
                url: '<?=$_url;?>/ajax.php?act=profile-updatePayment',
                dataType: 'json',
                data: {bank: bank.val(), branch: branch.val(), number: number.val(), name: name.val()},
                type: 'post',
                success: function (response) {
                  if(response.status == 200){
                    toastr.success(response.message);
                  } else
                    toastr.error(response.message);
                  $this.html('Save payment settings');
                  $this.prop('disabled', false);
                },
                error: function (response) {
                  toastr.error('Could not connect to API!');
                  $this.prop('disabled', false);
                  $this.html('Save payment settings');
                }
            });
          }
        });

        $("form[name=payment]").on("click","button[type=delete]",function(){
          var bank = $("#i-payment_bank"),
              name = $("#i-payment_name"),
              branch = $("#i-payment_branch"),
              number = $("#i-payment_number");

          bank.siblings(".invalid-feedback").hide();
          name.siblings(".invalid-feedback").hide();
          branch.siblings(".invalid-feedback").hide();
          number.siblings(".invalid-feedback").hide();

          var $this = $(this);
          $this.prop('disabled', true);
          $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'+$this.html());
          $.ajax({
              url: '<?=$_url;?>/ajax.php?act=profile-deletePayment',
              dataType: 'json',
              type: 'post',
              success: function (response) {
                if(response.status == 200){
                  toastr.success(response.message);
                  bank.val('');
                  branch.val('');
                  number.val('');
                  name.val('');
                } else
                  toastr.error(response.message);
                $this.html('Delete payment settings');
                $this.prop('disabled', false);
              },
              error: function (response) {
                toastr.error('Could not connect to API!');
                $this.prop('disabled', false);
                $this.html('Delete payment settings');
              }
          });

        });


    });
</script>
