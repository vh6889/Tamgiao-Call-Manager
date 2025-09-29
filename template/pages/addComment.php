<?php 
$_statusmessage = array();
if($_SERVER['REQUEST_METHOD']=='POST'){
    //upload image
    if ($_FILES['image']['size']==''){                     
    }else{            
        if(($_FILES['image']['type']!="image/gif")
                        &&($_FILES['image']['type']!="image/png")
                        &&($_FILES['image']['type']!="image/jpeg")
                        &&($_FILES['image']['type']!="image/jpg")){
            $message="File không đúng định dạng";	
        }elseif ($_FILES['image']['size']>1000000) {
            $message="Kích thước phải nhỏ hơn 1MB";						
        }elseif ($_FILES['image']['size']=='') {
            $message="Bạn chưa chọn file ảnh";
        }else{
            $img=$_FILES['images']['name'];                
            $temp = explode(".", $_FILES["image"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $link_img='template/avatars/'.$newfilename;
            move_uploaded_file($_FILES['image']['tmp_name'],dirname(dirname(dirname(__FILE__)))."/template/avatars/".$newfilename);	
        }
    }                    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];   
    $status = $_POST['status'];	
    $comment_category_id = $_POST['comment_category_id'];
    $comment_category_id1 = $_POST['comment_category_id1'];
    $comment_category_id2 = $_POST['comment_category_id2'];
    $comment_landing_id = $_POST['comment_landing_id'];
    $total_like = $_POST['total_like'];
    $data_ins = [
        'comment_category_id' => $comment_category_id,
        'comment_category_id1' => $comment_category_id1,
        'comment_category_id2' => $comment_category_id2,
        'comment_landing_id' => $comment_landing_id,
        'avatar' => $link_img,
        'name'  =>  $name,
        'email' => $email,
        'comment' => $comment,
        'total_like' => $total_like,
        'bad' => 0,
        'date'  => date('Y-m-d'),
        'status' => $status
    ];
    $query = createOrUpdate('core_comments','',$data_ins);    
    if($query==TRUE){         
        $_statusmessage['type'] = 'success';
        $_statusmessage['message'] = 'Thêm mới comment thành công.';
        
    }else{
        $_statusmessage['type'] = 'warning';
        $_statusmessage['message'] = 'Không có gì cần lưu lại.';    

    }    
}
$_data = getData('core_offers','id,name','','id','desc','');
$_data1 = getData('core_comment_categories1','id,category_name','','id','desc','');
$_data2 = getData('core_comment_categories2','id,category_name2','','id','desc','');
$_data3 = getData('core_comment_landings','id,landing','','id','desc','');
?>
<h2 class="section-heading mb-4">
    <span class="badge green"> New</span>
    Thêm mới Comment  
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
            <div class="px-2">
                <div class="form-group mt-3">
                    <label class="active" style="display:block;">Ảnh đại diện:</label>                                                        
                    <img id="blah" src="template/avatars/noavatar.png" alt="your image"  width="80"/>                                                      
                    <input style="margin-top:10px;" type="file" class="form-control" name="image" id="imgInp">                    
                    <div class="invalid-feedback">Họ tên không được bỏ trống</div>
                </div>  
                <div class="row">
                    <div class="form-group col-md-3">
                        <label class="active">Comment Category:</label>
                        <select id="comment_category_id" name="comment_category_id" class="form-control select-item" >
                            <option value="0">--Chọn Comment Category--</option>
                            <?php 
                                foreach($_data as $arr) 
                                {                                                                
                                ?>
                                <option value="<?php echo $arr['id']; ?>"><?php echo $arr['name']; ?></option>                                
                                <?php                                                                                                                               
                                }
                            ?>
                        </select>                
                    </div>
                    <div class="form-group col-md-3">
                        <label class="active">Comment Category 1:</label>
                        <select id="comment_category_id1" name="comment_category_id1" class="form-control select-item" >
                            <option value="0">--Chọn Comment Category 1--</option>
                            <?php 
                                foreach($_data1 as $arr1) 
                                {                                                                
                                ?>
                                <option value="<?php echo $arr1['id']; ?>"><?php echo $arr1['category_name']; ?></option>                                
                                <?php                                                                                                                               
                                }
                            ?>
                        </select>                
                    </div>
                    <div class="form-group col-md-3">
                        <label class="active">Comment Category 2:</label>
                        <select id="comment_category_id2" name="comment_category_id2" class="form-control select-item" >
                            <option value="0">--Chọn Comment Category 2--</option>
                            <?php 
                                foreach($_data2 as $arr2) 
                                {                                                                
                                ?>
                                <option value="<?php echo $arr2['id']; ?>"><?php echo $arr2['category_name2']; ?></option>                                
                                <?php                                                                                                                               
                                }
                            ?>
                        </select>                
                    </div>
                    <div class="form-group col-md-3">
                        <label class="active">Comment Landing:</label>
                        <select id="comment_landing_id" name="comment_landing_id" class="form-control select-item" >
                            <option value="0">--Chọn Landing Page--</option>
                            <?php 
                                foreach($_data3 as $arr3) 
                                {                                                                
                                ?>
                                <option value="<?php echo $arr3['id']; ?>"><?php echo $arr3['landing']; ?></option>                                
                                <?php                                                                                                                               
                                }
                            ?>
                        </select>                
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label class="active">Họ tên:</label>
                    <input type="text" class="form-control" name="name" id="name" value="">                    
                </div>
                <div class="form-group mt-3">
                    <label class="active">Email:</label>
                    <input type="text" class="form-control" name="email" id="email" value="">
                </div>                
                <div class="form-group mt-3">
                    <label class="active">Nội dung:</label>
                    <textarea type="text" class="form-control" name="comment" id="comment"></textarea>
                </div>
                <div class="form-group mt-3">
                    <label class="active">ToTal Like:</label>
                    <input type="text" class="form-control" name="total_like" id="total_like" value="">
                </div> 
				<div class="form-group mt-12">
                    <label class="active" style="display:block;">Trạng thái:</label>	
                    <label class="radio-inline">
					  <input style="position:static;opacity:1;" type="radio" name="status" value="0" checked> Uncheck
					</label>				
					<label class="radio-inline">
					  <input style="position:static;opacity:1;" type="radio" name="status" value="1"> Accept
					</label>
					<label class="radio-inline">
					  <input style="position:static;opacity:1;" type="radio" name="status" value="2"> Refuse
					</label>							
                </div>
            </div>
            <div class="form-group text-right">
                <span class="waves-input-wrapper waves-effect waves-light">
                    <input class="btn btn-dark btn-md btn-rounded waves-effect text-dark-50" name="submit" type="submit" value="Thêm mới">
                </span>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript">
    $('#comment_category_id').change(function() {
        giatri = this.value;
        $('#comment_category_id1').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect&id=' + giatri);                    
        $('#comment_category_id2').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect2&id=' + giatri); 
        $('#comment_landing_id').load('<?=$_url;?>/ajax.php?act=admincp-loadSelectLanding&id=' + giatri); 
    });
    $('#comment_category_id1').change(function() {
        giatri2 = this.value;
        $('#comment_category_id2').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect3&id=' + giatri2);
        $('#comment_landing_id').load('<?=$_url;?>/ajax.php?act=admincp-loadSelectLanding1&id=' + giatri2); 
    });
    $('#comment_category_id2').change(function() {
        giatri3 = this.value;
        $('#comment_landing_id').load('<?=$_url;?>/ajax.php?act=admincp-loadSelectLanding2&id=' + giatri3); 
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
        if(name == ''){
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
    function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
        $('#blah').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
    }

    $("#imgInp").change(function() {
    readURL(this);
    });
</script>