<?php 
if(isset($_GET['action'])){}else{
    if(isset($_GET['id']) && filter_var($_GET['id'],FILTER_VALIDATE_INT,array('min_range'=>1))){
        $email_id = $_GET['id'];
        $email_info = infoEmail($email_id);
    }else{
        header('Location: ?route=emailMarketing');
        exit();
    }
}
$_data = $_db->query("select * from `core_email_marketings` order by `date` DESC")->fetch_array();
$_category_key = $_db->query("select * from `core_offers` order by `id` DESC")->fetch_array();
$_category1 = $_db->query("select * from `core_comment_categories1` order by `id` DESC")->fetch_array();
$_category2 = $_db->query("select * from `core_comment_categories1` order by `id` DESC")->fetch_array();
$landing_pages = $_db->query("select * from `core_comment_landings` order by `id` DESC")->fetch_array();
$_statusmessage = array();
if($_SERVER['REQUEST_METHOD']=='POST'){
    //upload image
    $email_setting_id = $_POST['email_setting_id'];  
    $email_category_id1 = $_POST['email_category_id1'];
    $email_category_id2 = $_POST['email_category_id2'];
    $email_landing_id = $_POST['email_landing_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];    
    $phone = $_POST['phone'];   
	$status = $_POST['status'];
    if(isset($_GET['id'])){
        $query = $_db->exec_query("update `core_email_marketings` set `email_setting_id`='".$email_setting_id."',`email_category_id1`='".$email_category_id1."',`email_category_id2`='".$email_category_id2."',`email_landing_id`='".$email_landing_id."',`name`='".$name."',`phone`='".$phone."',`email`='".$email."',`status`='".$status."' where id='".$email_info['id']."' ");
    }else{        
        $query = $_db->exec_query("insert into `core_email_marketings` set `email_setting_id`='".$email_setting_id."',`email_category_id1`='".$email_category_id1."',`email_category_id2`='".$email_category_id2."',`email_landing_id`='".$email_landing_id."',`name`='".$name."',`phone`='".$phone."',`email`='".$email."',`email_date`='".date('Y-m-d')."',`status`='".$status."' ");
    }

    if($query==TRUE){         
        $_statusmessage['type'] = 'success';
        if(isset($_GET['id'])){            
            $_statusmessage['message'] = 'Cập nhật Email thành công.';  
            if(isset($_GET['type'])){
                if($_GET['type']=='accept'){
                    ?>
                    <script>setTimeout(function() {window.location.href="<?=$_url;?>/?route=emailMarketing&type=accept"}, 1000);</script>
                    <?php
                }elseif($_GET['type']=='refuse'){
                    ?>
                    <script>setTimeout(function() {window.location.href="<?=$_url;?>/?route=emailMarketing&type=refuse"}, 1000);</script>
                    <?php
                }
            }else{
            ?>
            <script>setTimeout(function() {window.location.href="<?=$_url;?>/?route=emailMarketing"}, 1000);</script>
            <?php
            }            
        }else{
            $_statusmessage['message'] = 'Thêm mới Email thành công.';
        }        
    }
}
?>
<h2 class="section-heading mb-4">
    <?php 
        if(isset($_GET['action'])){
            if($_GET['action']=='add'){
            ?>
            <span class="badge green"> New</span>
                Thêm mới Email Marketing
            <?php  
            }          
        }
        if(isset($_GET['id'])){
          ?>
          <span class="badge green"> Update</span>
                Sửa Email Marketing      
          <?php          
        }
    ?>    
</h2>
<section class="row mb-5 py-3">
    <div class="col-md-12 mx-auto white z-depth-1">
        <form id="formUpdate" method="POST" onsubmit="return submitUpdate();" enctype="multipart/form-data">
            <div class="col-md-12">
            <?php if(!empty($_statusmessage)){ ?>
              <div class="alert alert-<?php echo $_statusmessage["type"]; ?> lert-dismissible fade show" role="alert">
                <?php echo $_statusmessage["message"]; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php 
                }
            ?>
          </div>            
            <div class="col-md-12">   				              
                <div class="form-group">
                    <label class="active">Key:</label>
                    <select id="email_setting_id" name="email_setting_id" class="form-control select-item" >
                        <option value="0">--Chọn Key--</option>
                        <?php 
                            foreach($_category_key as $arr) 
                            {    
                                if(isset($_GET['id'])){
                                    if($arr['id']==$email_info['email_setting_id']){
                                        ?>
                                        <option selected value="<?php echo $arr['id']; ?>"><?php echo $arr['name'].'('.$arr['key']; ?>)</option> 
                                        <?php
                                    }else{
                                        ?>
                                        <option value="<?php echo $arr['id']; ?>"><?php echo $arr['name'].'('.$arr['key']; ?>)</option> 
                                        <?php
                                    } 
                                }else{
                                    ?>
                                    <option value="<?php echo $arr['id']; ?>"><?php echo $arr['name'].'('.$arr['key']; ?>)</option>
                                    <?php  
                                }                                                                                                                                                        
                            }
                        ?>
                    </select>                
                </div>
                <div class="form-group">
                    <label class="active">Category 1: </label>
                    <select id="email_category_id1" name="email_category_id1" class="form-control select-item" >
                        <option value="0">--Chọn Category 1--</option>
                        <?php 
                            foreach($_category1 as $arr1) 
                            {
                                if(isset($_GET['id'])){
                                    if($arr1['id']==$email_info['email_category_id1']){
                                        ?>
                                        <option selected value="<?php echo $arr1['id']; ?>"><?php echo $arr1['category_name']; ?></option>
                                        <?php
                                    }else{
                                        ?>
                                        <option value="<?php echo $arr1['id']; ?>"><?php echo $arr1['category_name']; ?></option>
                                        <?php
                                    }
                                }else{
                                    ?>
                                    <option value="<?php echo $arr1['id']; ?>"><?php echo $arr1['category_name']; ?></option> 
                                    <?php
                                }                                                                                                                                                                                                                     
                            }
                        ?>
                    </select>                
                </div>
                <div class="form-group">
                    <label class="active">Category 2: </label>
                    <select id="email_category_id2" name="email_category_id2" class="form-control select-item" >
                        <option value="0">--Chọn Category 2--</option>
                        <?php 
                            foreach($_category2 as $arr2) 
                            {     
                                if(isset($_GET['id'])){
                                    if($arr2['id']==$email_info['email_category_id2']){
                                        ?>
                                        <option selected value="<?php echo $arr2['id']; ?>"><?php echo $arr2['category_name2']; ?></option>
                                        <?php
                                    }else{
                                        ?>
                                        <option value="<?php echo $arr2['id']; ?>"><?php echo $arr2['category_name2']; ?></option> 
                                        <?php
                                    }
                                }else{                                                           
                                ?>
                                <option value="<?php echo $arr2['id']; ?>"><?php echo $arr2['category_name2']; ?></option>                                
                                <?php
                                }                                                                                                                               
                            }
                        ?>
                    </select>                
                </div>
                <div class="form-group">
                    <label class="active">Landing Page:</label>
                    <select id="email_landing_id" name="email_landing_id" class="form-control select-item" >
                        <option value="0">--Chọn Comment Landing Page--</option>
                        <?php 
                            foreach ($landing_pages as $arr3) 
                            {   
                                if(isset($_GET['id']))                             {
                                    if($arr3['id']==$email_info['email_landing_id']){
                                    ?>
                                    <option selected value="<?php echo $arr3['id']; ?>"><?php echo $arr3['landing']; ?></option>
                                    <?php
                                    }else{
                                    ?>
                                    <option value="<?php echo $arr3['id']; ?>"><?php echo $arr3['landing']; ?></option>
                                    <?php    
                                    }  
                                }else{
                                    ?>
                                     <option value="<?php echo $arr3['id']; ?>"><?php echo $arr3['landing']; ?></option>
                                    <?php
                                }                                                                                             
                            }
                        ?>
                    </select>                
                </div>  
                <div class="form-group mt-3">
                    <label class="active">Họ tên:</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php if(isset($_GET['id'])){ echo $email_info['name'];} ?>">                    
                </div>
				<div class="form-group mt-3">
                    <label class="active">Điện thoại:</label>
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php if(isset($_GET['id'])){ echo $email_info['phone'];} ?>">
                </div>    
                <div class="form-group mt-3">
                    <label class="active">Email:</label>
                    <input type="text" class="form-control" name="email" id="email" value="<?php if(isset($_GET['id'])){ echo $email_info['email'];} ?>">
                </div>  
				<div class="form-group mt-12">
                    <label class="active" style="display:block;">Trạng thái:</label>	
					<?php 
						if(isset($_GET['id'])){
							if($email_info['status']=='1'){
								?>
                                <label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="0"> Uncheck
								</label>
								<label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="1" checked> Accept
								</label>
								<label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="2"> Refuse
								</label>	
								<?php
							}elseif($email_info['status']=='2'){
                                ?>
                                <label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="0"> Uncheck
								</label>
								<label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="1"> Accept
                                </label>
                                <label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="2" checked> Refuse
                                </label>	
                                <?php
                            }else{
								?>
                                <label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="0" checked> Uncheck
								</label>
								<label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="1"> Accept
                                </label>
                                <label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="2"> Refuse
                                </label>	
								<?php
							}
						}else{
						?>	
                        <label class="radio-inline">
                            <input style="position:static;opacity:1;" type="radio" name="status" value="0" checked> Uncheck
                        </label>
						<label class="radio-inline">
						  <input style="position:static;opacity:1;" type="radio" name="status" value="1" checked> Accept
						</label>
						<label class="radio-inline">
						  <input style="position:static;opacity:1;" type="radio" name="status" value="2"> Refuse
						</label>	
						<?php 
						}
					?>
                </div>	
            </div>
            <div class="form-group text-right">
                <span class="waves-input-wrapper waves-effect waves-light">
                    <input class="btn btn-dark btn-md btn-rounded waves-effect text-dark-50" name="submit" type="submit" value="<?php if(isset($_GET['id'])){ echo 'Cập nhật';}else{ echo 'Thêm mới';} ?>">
                </span>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript">
    $('#email_setting_id').change(function() {
        giatri = this.value;
        $('#email_category_id1').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect&id=' + giatri);                    
        $('#email_category_id2').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect2&id=' + giatri);    
        $('#email_landing_id').load('<?=$_url;?>/ajax.php?act=admincp-loadSelectLanding&id=' + giatri);              
    });
    $('#email_category_id1').change(function() {
        giatri2 = this.value;
        $('#email_category_id2').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect3&id=' + giatri2);
        $('#email_landing_id').load('<?=$_url;?>/ajax.php?act=admincp-loadSelectLanding1&id=' + giatri2); 
    });
    $('#email_category_id2').change(function() {
        giatri3 = this.value;
        $('#email_landing_id').load('<?=$_url;?>/ajax.php?act=admincp-loadSelectLanding2&id=' + giatri3); 
    });
    function checkMail(mail){
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;								
        if (!filter.test(mail)) return false;								
            return true;								
    }
    function submitUpdate(){
        var key = $("#key").val();
        var name = $("#name").val();
        var email = $("#email").val();
        var comment = $("#comment").val();
		if(key == ''){
			alert('Key không để trống');
            $("#key").focus();
            return false;
		}
        else if(name == ''){
            alert('Họ tên không để trống');
            $("#name").focus();
            return false;
        }
        else if(!checkMail(email)){
            alert("Email không đúng định dạng");
            $("#email").focus();
            return false;
        }else {
            return true;
        }
        return false;
    }    
</script>