<?php 
if(isset($_GET['action'])){}else{
    if(isset($_GET['id']) && filter_var($_GET['id'],FILTER_VALIDATE_INT,array('min_range'=>1))){
        $comment_category1_id = $_GET['id'];
        $comment_category1 = getInfoId($comment_category1_id,'core_comment_categories1');
    }else{
        header('Location: ?route=commentCategoryOne');
        exit();
    }
}
$_data = $_db->query("select * from `core_comment_categories` order by `id` DESC")->fetch_array();
$_statusmessage = array();
if($_SERVER['REQUEST_METHOD']=='POST'){
    //upload image
    $category_id = $_POST['category_id'];             
    $category_name = $_POST['category_name'];          
    $query = $_db->exec_query("update `core_comment_categories1` set `category_id`='".$category_id."',`category_name`='".$category_name."' where id='".$comment_category1['id']."' ");
    if($query==TRUE){         
        $_statusmessage['type'] = 'success';
        if(isset($_GET['id'])){            
            $_statusmessage['message'] = 'Cập nhật thành công.';  
            echo "<script>setTimeout(function() {location.reload();}, 200);</script>";          
        }       
    }
}
?>
<h2 class="section-heading mb-4">
    <?php         
        if(isset($_GET['id'])){
          ?>
          <span class="badge green"> Update</span>
                Sửa Comment Category 1  
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
                    <label class="active">Comment Category:</label>
                    <select id="category_id" name="category_id" class="mdb-select " >
                        <option value="-1">--Chọn Comment--</option>
                        <?php 
                            foreach ($_data as $arr) 
                            {
                                if(isset($_GET['id'])){
                                    if($arr['id']==$comment_category1['category_id']){
                                        ?>
                                        <option selected value="<?php echo $arr['id']; ?>"><?php echo $arr['name']; ?></option>
                                        <?php
                                    }else{
                                    ?>
                                    <option value="<?php echo $arr['id']; ?>"><?php echo $arr['name']; ?></option>
                                    <?php    
                                    }
                                }else{
                                ?>
                                <option value="<?php echo $arr['id']; ?>"><?php echo $arr['name']; ?></option>
                                <?php 
                                }
                            }
                        ?>
                    </select>                
                </div>      
                <div class="form-group mt-3">
                    <label class="active">Category Name:</label>
                    <input type="text" class="form-control" name="category_name" id="category_name" value="<?php if(isset($_GET['id'])){ echo $comment_category1['category_name'];} ?>">                    
                </div>               
            </div>
            <div class="form-group text-right">
                <span class="waves-input-wrapper waves-effect waves-light">
                    <a type="button" class="btn btn-danger" href="?route=commentCategoryOne">Quay lại</a>
                    <input class="btn btn-primary" name="submit" type="submit" value="Cập nhật">                    
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
        var category_id = $("#category_id").val();
        var category_name = $("#category_name").val(); 
        if(category_id == '-1'){
            alert('Comment Category chưa chọn');
            return false;
        } else if(category_name == ''){
            alert('Category Name không để trống');
            $("#category_name").focus();
            return false;
        }else {
            return true;
        }
        return false;
    }    
</script>