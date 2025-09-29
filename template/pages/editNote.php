<?php


if(!isAdmin())
  $_id = '';

$id = $_id ? $_id : $_user['group'];

$_group = getGroup($id);


if(!$_group || (!isAdmin() && $_group && $_user['id'] != $_group['leader'])){

  echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';

  goto end;
}


$note = isset($_REQUEST['note']) ? $_REQUEST['note'] : $_group['note'];

if(isset($_POST['submit'])){

  if($_db->exec_query("update `core_groups` set `note`='".escape_string($note)."' where `id`='".escape_string($id)."' ")){

    $_statusmessage['type'] = 'success';
    $_statusmessage['message'] = 'Lưu thành công.';
  } else {
    $_statusmessage['type'] = 'warning';
    $_statusmessage['message'] = 'Có lỗi xảy ra vui lòng thử lại sau ít phút.';
  }

}



?>

<script type="text/javascript" src="template/assets/js/vendor/tinymce/tinymce.min.js"></script>


<h2 class="section-heading mb-4">Edit Note - <?=_e($_group['name']);?></h2>
<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">

      <form method="POST">
        <div class="row">

          <div class="col-md-12">
            <?php if(!empty($_statusmessage)): ?>
              <div class="alert alert-<?=$_statusmessage["type"]; ?> lert-dismissible fade show" role="alert">
                <?=$_statusmessage["message"]; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php endif; ?>
            <div class="form-group">
              <textarea class="form-control" id="note" rows="12" name="note"><?=$note;?></textarea>
            </div>
          </div>
        </div>
        <input name="route" value="editNote" type="hidden">
        <div class="text-right pb-2">
          <button class="btn btn-info btn-rounded btn-sm waves-effect waves-light" name="submit" type="submit">Save Note</button>
        </div>
      </form>

    </div>
</section>

<script>tinymce.init({
  selector:'#note',
    menubar: false,
    plugins: ["image code link textcolor"],
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.onchange = function () {
            var file = this.files[0];
            var reader = new FileReader();

            reader.onload = function () {
                var id = 'blobid' + (new Date()).getTime();
                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), {title: file.name});
            };
            reader.readAsDataURL(file);
        };
        input.click();
    }
});</script>

<?php


end:


?>
