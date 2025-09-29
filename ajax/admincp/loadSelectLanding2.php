<?php 
$id = $_GET['id'];
$district = $_db->query("select * from `core_comment_landings` where category_id2='".$id."' order by `id` DESC")->fetch_array();
?>
<option value="-1">-- Chọn Landing Page--</option>  
<?php
foreach ($district as $item) {
    ?>
    <option value="<?php echo $item["id"]; ?>"><?php echo $item["landing"]; ?></option>
    <?php
}

?>