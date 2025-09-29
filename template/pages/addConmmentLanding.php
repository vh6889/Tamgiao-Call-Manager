<?php 
if(isset($_GET['id'])){
    if(isset($_GET['id']) && filter_var($_GET['id'],FILTER_VALIDATE_INT,array('min_range'=>1))){
        $comment_landing_id = $_GET['id'];
        $comment_landing_info = getInfoId($comment_landing_id,'core_comment_landings');      
    }else{
        header('Location: ?route=commentLanding');
        exit();
    }
}
$_data = $_db->query("select * from `core_offers` order by `id` DESC")->fetch_array();
$_data1 = $_db->query("select * from `core_comment_categories1` order by `id` DESC")->fetch_array();
$_data2 = $_db->query("select * from `core_comment_categories2` order by `id` DESC")->fetch_array();
$_statusmessage = array();
if($_SERVER['REQUEST_METHOD']=='POST'){    
    $category_id = $_POST['category_id'];             
    $category_id1 = $_POST['category_id1']; 
    $category_id2 = $_POST['category_id2']; 
    $landing = $_POST['landing'];     
    if(isset($_GET['id'])){     
        $query = $_db->exec_query("UPDATE core_comment_landings SET category_id='".$category_id."',category_id1='".$category_id1."',category_id2='".$category_id2."',landing='".$landing."' WHERE id='".$comment_landing_info["id"]."' ");
    }else{
        $query = $_db->exec_query("INSERT INTO core_comment_landings SET category_id='".$category_id."',category_id1='".$category_id1."',category_id2='".$category_id2."',landing='".$landing."' ");
    }
    if($query==TRUE){         
        $_statusmessage['type'] = 'success';
        if(isset($_GET['id'])){            
            $_statusmessage['message'] = 'Cập nhật thành công.';                
            ?>
            <script>setTimeout(function() {window.location.href="<?=$_url;?>/?route=commentLanding"}, 1000);</script>
            <?php       
        } else{
            $_statusmessage['message'] = 'Thêm mới thành công.';      
        }      
    }
}
?>
<h2 class="section-heading mb-4">
    <?php 
        if(isset($_GET['id'])){
        ?>
        <span class="badge green"> Update</span>
        Update Comment Landing Page 
        <?php
        }else{
        ?>
        <span class="badge green"> New</span>
        Add new Comment Landing Page 
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
                    <select id="category_id" name="category_id" class="form-control select-item" >
                        <option value="-1">--Chọn Comment--</option>
                        <?php 
                            foreach ($_data as $arr) 
                            {
                                if(isset($_GET['id'])){
                                    if($arr['id']==$comment_landing_info['category_id']){
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
                <div class="form-group">
                    <label class="active">Comment Category 1:</label>
                    <select id="category_id1" name="category_id1" class="form-control select-item">
                        <option value="-1">--Chọn Comment--</option>
                        <?php 
                            foreach ($_data1 as $arr1) 
                            {
                                if(isset($_GET['id'])){
                                    if($arr1['id']==$comment_landing_info['category_id1']){
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
                    <label class="active">Comment Category 2:</label>
                    <select id="category_id2" name="category_id2" class="form-control select-item">
                        <option value="-1">--Chọn Comment--</option>
                        <?php 
                            foreach ($_data2 as $arr2) 
                            {  
                                if(isset($_GET['id'])){   
                                    if($arr2['id']==$comment_landing_info['category_id2']){
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
                <div class="form-group mt-3">
                    <label class="active">Landing:</label>
                    <input type="text" class="form-control" name="landing" id="landing" value="<?php if(isset($_GET['id'])){ echo $comment_landing_info["landing"];} ?>">                    
                </div>               
            </div>
            <div class="form-group text-right">
                <span class="waves-input-wrapper waves-effect waves-light">
                    <a type="button" class="btn btn-danger" href="?route=commentLanding">Quay lại</a>
                    <input class="btn btn-primary" name="submit" type="submit" value="<?php if(isset($_GET['id'])){ echo "Cập nhật";}else{ echo 'Thêm mới';} ?>">                    
                </span>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript">
    $('#category_id').change(function() {
        giatri = this.value;
        $('#category_id1').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect&id=' + giatri);                    
        $('#category_id2').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect2&id=' + giatri); 
    });
    $('#category_id1').change(function() {
        giatri2 = this.value;
        $('#category_id2').load('<?=$_url;?>/ajax.php?act=admincp-loadSelect3&id=' + giatri2);
    });
    function checkMail(mail){
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;								
        if (!filter.test(mail)) return false;								
            return true;								
    }
    function submitUpdate(){
        var category_id = $("#category_id").val();
        var landing = $("#landing").val(); 
        if(category_id == '-1'){
            alert('Comment Category chưa chọn');
            return false;
        } else if(landing == ''){
            alert('Landing không để trống');
            $("#landing").focus();
            return false;
        }else {
            return true;
        }
        return false;
    }    
</script>