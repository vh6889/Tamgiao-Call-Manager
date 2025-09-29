<?php 
if(isset($_GET['action'])){}else{
    if(isset($_GET['id']) && filter_var($_GET['id'],FILTER_VALIDATE_INT,array('min_range'=>1))){
        $comment_bad_id = $_GET['id'];
        $comment_bad_info = infoCommentBad($comment_bad_id);
    }else{
        header('Location: ?route=commentBad');
        exit();
    }
}
$_data = $_db->query("select * from `core_comments` order by `date` DESC")->fetch_array();
$_statusmessage = array();
if($_SERVER['REQUEST_METHOD']=='POST'){
    //upload image
    $comment_id = $_POST['comment_id'];             
    $name = $_POST['name'];
    $email = $_POST['email'];    
    $content = $_POST['content'];   
    if(isset($_GET['id'])){
        $query = $_db->exec_query("update `core_comment_bads` set `comment_id`='".$comment_id."',`name`='".$name."',`email`='".$email."',`content`='".$content."' where id='".$comment_bad_info['id']."' ");
    }else{        
        $query = $_db->exec_query("insert into `core_comment_bads` set `comment_id`='".$comment_id."',`name`='".$name."',`email`='".$email."',`content`='".$content."',`bad_date`='".date('Y-m-d')."' ");
    }

    if($query==TRUE){         
        $_statusmessage['type'] = 'success';
        if(isset($_GET['id'])){            
            $_statusmessage['message'] = 'Cập nhật báo xấu thành công.';              
            ?>
            <script>setTimeout(function() {window.location.href="<?=$_url;?>/?route=commentBad"}, 1000);</script>
            <?php         
        }else{
            $_statusmessage['message'] = 'Thêm mới báo xấu thành công.';
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
                Thêm mới Báo xấu
            <?php  
            }          
        }
        if(isset($_GET['id'])){
          ?>
          <span class="badge green"> New</span>
                Sửa Báo xấu      
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
                <div class="form-group">
                    <label class="active">Comment:</label>
                    <select id="comment_id" name="comment_id" class="mdb-select " >
                        <option value="0">--Chọn Comment--</option>
                        <?php 
                            foreach ($_data as $arr) 
                            {
                                if(isset($_GET['id'])){
                                    if($arr['id']==$comment_bad_info['comment_id']){
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
                    <input type="text" class="form-control" name="name" id="name" value="<?php if(isset($_GET['id'])){ echo $comment_bad_info['name'];} ?>">                    
                </div>
                <div class="form-group mt-3">
                    <label class="active">Email:</label>
                    <input type="text" class="form-control" name="email" id="email" value="<?php if(isset($_GET['id'])){ echo $comment_bad_info['email'];} ?>">
                </div>                
                <div class="form-group mt-3">
                    <label class="active">Nội dung:</label>
                    <textarea type="text" class="form-control" name="content" id="content"><?php if(isset($_GET['id'])){ echo $comment_bad_info['content'];} ?></textarea>
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
</script>