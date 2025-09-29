<?php 
$id = $_GET['id'];
$district = $_db->query("select * from `core_comment_categories1` where category_id='".$id."' order by `id` DESC")->fetch_array();
?>
<option value="-1">Comment Category 1</option>  
<?php
foreach ($district as $item) {
    ?>
    <option value="<?php echo $item["id"]; ?>"><?php echo $item["category_name"]; ?></option>
    <?php
}

?>