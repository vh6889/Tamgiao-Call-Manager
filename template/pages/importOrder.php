<style>
    .table_import {
        width: 50%;
        margin: auto;
        border: 1px solid #ddd;
    }

    .table_import td {
        border: 1px solid #ddd;
        padding: 10px 5px;
    }

    .table_import td label {
        font-size: 16px;
    }

    .table_head {
        background: #ffbb33;
        color: #fff;
    }
</style>
<section class="row mb-4">
    <h2 class="section-heading">Import Order</h2>
</section>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">
        <form mehtod="post" id="import_csv">
            <table class="table_import">
                <tr class="table_head">
                    <td colspan="2" class="text-center">
                        <h2>Nhập đơn hàng từ file CSV</h2>
                    </td>
                </tr>
                <tr>
                    <td><label>Chọn Offer</label></td>
                    <td>
                        <div class="form-group">
                        <select id="import_offer" name="offer" class="form-control" required>
                            <option value="">Chọn offer cho đơn hàng</option>
                        <?php
                            $offers = getOffer();
                            foreach ($offers as $offer) {
                        ?>
                            <option class="<?php echo $offer['status']=='stop' ? 'bg-danger' : ''; ?>" value="<?= $offer['id'] ?>"><?= $offer['name'] ?></option>
                        <?php
                            }
                        ?>
                        </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><label>Chọn File Import</label></td>
                    <td>
                        <div class="form-group">
                            <input type="file" id="csv_file" class="form-control-file" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="position:relative;">
                        <a href="tools/data_import/demo_import_data.csv" style="position:absolute;top:20px;left:20px;">Xem cấu trúc file Import</a>
                        <button type="submit" style="margin:auto;display:block;" class="btn btn-primary">Import Orders</button>
                    </td>
                </tr>
            </table>
        </form>
        <div id="import_result"></div>
    </div>
</section>
<div class="ajax-loading"></div>
<script>
    $('#import_csv').on('submit', function(event) {
        let file = $("#csv_file").prop('files')[0];
        let data = new FormData();
        $('.ajax-loader').show();
        data.append('csv_file', file);
        data.append('offer', $('#import_offer').val());
        event.preventDefault();
        $.ajax({
            url: "<?= $_url; ?>ajax.php?act=importCsv&action=import&type=order",
            method: "POST",
            data: data,
            contentType: false,
            processData: false,
            success: function(data) {
                $('#import_result').html(data);
                $('#csv_file').val('');
                $('#import_offer').val('');
                $('.ajax-loader').hide(2000);
            }
        });
    });
</script>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">
        <form mehtod="post" id="export_excel">
            <table class="table_import">
                <tr class="table_head">
                    <td colspan="2" class="text-center">
                        <h2>Cập nhật trạng thái đơn hàng</h2>
                    </td>
                </tr>
                <tr>
                    <td><label>Chọn File Export</label></td>
                    <td>
                        <div class="form-group">
                            <input type="file" id="excel_file" class="form-control-file">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="position:relative;">
                        <a href="tools/data_import/data_order.xlsx" style="position:absolute;top:20px;left:20px;">Xem cấu trúc file Import</a>
                        <button type="submit" style="margin:auto;display:block;" class="btn btn-primary">Export Excel</button>
                    </td>
                </tr>
            </table>
        </form>
        <div id="result"></div>
    </div>
</section>
<script>
    $('#export_excel').on('submit', function(event) {
        var file = $("#excel_file").prop('files')[0];
        var data = new FormData();
        data.append('file', file);
        event.preventDefault();
        $.ajax({
            url: "<?= $_url; ?>/ajax.php?act=excelData&action=import&type=order",
            method: "POST",
            data: data,
            contentType: false,
            processData: false,
            success: function(data) {
                $('#result').html(data);
                $('#excel_file').val('');
            }
        });
    });
</script>