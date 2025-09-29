<?php 
if(isset($_GET['action'])){}else{
    if(isset($_GET['id']) && filter_var($_GET['id'],FILTER_VALIDATE_INT,array('min_range'=>1))){
        $comment_reply_id = $_GET['id'];
        $comment_reply_info = infoCommentReply($comment_reply_id);
    }else{
        header('Location: ?route=commentReply&type='.$_GET['type'].'');
        exit();
    }
}
$_data = $_db->query("select * from `core_comments` order by `date` DESC")->fetch_array();
$_statusmessage = array();
if($_SERVER['REQUEST_METHOD']=='POST'){
    //upload image
    if ($_FILES['image']['size']==''){  
        if(isset($_GET['id'])){
            $link_img = $_POST['avatar_hidden'];  
        }                      
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
    $comment_id = $_POST['comment_id'];             
    $name = $_POST['name'];
    $email = $_POST['email'];
    $content = $_POST['content'];  
	$status = $_POST['status'];
    if(isset($_GET['id'])){
        $query = $_db->exec_query("update `core_comment_replys` set `comment_id`='".$comment_id."',`name`='".$name."',`email`='".$email."',`avatar`='".$link_img."',`content`='".$content."',`status`='".$status."' where id='".$comment_reply_info['id']."' ");
    }else{        
        $query = $_db->exec_query("insert into `core_comment_replys` set `comment_id`='".$comment_id."',`name`='".$name."',`email`='".$email."',`avatar`='".$link_img."',`content`='".$content."',`created`='".date('Y-m-d')."',`status`='".$status."' ");
    }

    if($query==TRUE){         
        $_statusmessage['type'] = 'success';
        if(isset($_GET['id'])){            
            $_statusmessage['message'] = 'Cập nhật comment reply thành công.';               
            ?>
            <script>setTimeout(function() {window.location.href="<?=$_url;?>/?route=commentReply&type=<?php echo $_GET['type']; ?>"}, 1000);</script>
            <?php       
        }else{
            $_statusmessage['message'] = 'Thêm mới comment reply thành công.';
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
                Thêm mới Comment Reply
            <?php  
            }          
        }
        if(isset($_GET['id'])){
          ?>
          <span class="badge green"> New</span>
                Sửa Comment Reply         
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
            <div class="px-2">
                <div class="form-group mt-3">
                    <label class="active" style="display:block;">Ảnh đại diện:</label> 
                    <?php 
                        if(isset($_GET['id'])){
                            if(!empty($comment_reply_info['avatar'])){
                            ?>
                            <img id="blah" src="<?php echo $comment_reply_info['avatar']; ?>" alt="your image"  width="80"/> 
                            <?php    
                            }
                        }else{
                        ?>                                                       
                        <img id="blah" src="template/avatars/noavatar.png" alt="your image"  width="80"/> 
                        <?php 
                        }
                    ?>                                                     
                    <input style="margin-top:10px;" type="file" class="form-control" name="image" id="imgInp">
                    <?php 
                        if(isset($_GET['id'])){
                        ?>
                        <input type="hidden" name="avatar_hidden" value="<?php echo $comment_reply_info['avatar']; ?>">
                        <?php 
                        }
                    ?>
                    <div class="invalid-feedback">Họ tên không được bỏ trống</div>
                </div>  
                <div class="form-group">
                    <label class="active">Comment:</label>
                    <select id="comment_id" name="comment_id" class="mdb-select " >
                        <option value="0">--Chọn Comment--</option>
                        <?php 
                            foreach ($_data as $arr) 
                            {
                                if(isset($_GET['id'])){
                                    if($arr['id']==$comment_reply_info['comment_id']){
                                        ?>
                                        <option selected value="<?php echo $arr['id']; ?>"><?php echo $arr['comment']; ?></option>
                                        <?php
                                    }else{
                                    ?>
                                    <option value="<?php echo $arr['id']; ?>"><?php echo $arr['comment']; ?></option>
                                    <?php    
                                    }
                                }else{
                                ?>
                                <option value="<?php echo $arr['id']; ?>"><?php echo $arr['comment']; ?></option>
                                <?php 
                                }
                            }
                        ?>
                    </select>                
                </div>      
                <div class="form-group mt-3">
                    <label class="active">Họ tên:</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php if(isset($_GET['id'])){ echo $comment_reply_info['name'];} ?>">                    
                </div>
                <div class="form-group mt-3">
                    <label class="active">Email:</label>
                    <input type="text" class="form-control" name="email" id="email" value="<?php if(isset($_GET['id'])){ echo $comment_reply_info['email'];} ?>">
                </div>                
                <div class="form-group mt-3">
                    <label class="active">Nội dung:</label>
                    <textarea type="text" class="form-control" name="content" id="content"><?php if(isset($_GET['id'])){ echo $comment_reply_info['content'];} ?></textarea>
                </div>
				<div class="form-group mt-12">
                    <label class="active" style="display:block;">Trạng thái:</label>	
					<?php 
						if(isset($_GET['id'])){
							if($comment_reply_info['status']=='0'){
								?>
								<label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="0" checked> UnCheck
								</label>
								<label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="1"> Accept
								</label>
                                <label class="radio-inline">
								  <input style="position:static;opacity:1;" type="radio" name="status" value="2"> Refuse
								</label>	
								<?php
							}elseif($comment_reply_info['status']=='1'){
								?>
								<label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="0"> UnCheck
                                </label>
                                <label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="1" checked> Accept
                                </label>
                                <label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="2"> Refuse
                                </label>	
								<?php
							}else{
                                ?>
                                <label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="0"> UnCheck
                                </label>
                                <label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="1"> Accept
                                </label>
                                <label class="radio-inline">
                                    <input style="position:static;opacity:1;" type="radio" name="status" value="2" checked> Refuse
                                </label>	
                                <?php
                            }
						}else{
						?>	
						<label class="radio-inline">
						  <input style="position:static;opacity:1;" type="radio" name="status" value="0" checked> UnCheck
						</label>
						<label class="radio-inline">
						  <input style="position:static;opacity:1;" type="radio" name="status" value="1"> Accept
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
    function checkMail(mail){
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;								
        if (!filter.test(mail)) return false;								
            return true;								
    }
    function submitUpdate(){
        var comment_id = $("#comment_id").val();
        var name = $("#name").val();
        var email = $("#email").val();
        var phone = $("#phone").val();
        var comment = $("#comment").val();
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