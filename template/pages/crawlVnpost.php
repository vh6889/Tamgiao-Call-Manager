<?php
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y', strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y', time());
?>

<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>
<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>

<section class="row mb-4">
    <h2 class="section-heading">Cập nhật trạng thái đơn hàng từ VNPost</h2>
</section>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">
        <form mehtod="post" name="filter" id="crawl_form">
            <input name="ts" value="" type="hidden">
            <input name="te" value="" type="hidden">
            <div class="row mb-3">
                <div class="col-sx-12 col-md-4 pb20">
                    <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span>
                        <i class="fa fa-caret-down"></i>
                    </span>
                </div>
                <div class="col-sx-12 col-md-3 pb20">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">Crawl Data</button>
                </div>
                <div class="col-12 col-md-5 text-right">
                    <button class="btn btn-danger waves-effect waves-light" role="btn-updateOrder" type="button" disabled onclick="updateOrders()">Update Order</button>
                </div>
            </div>
        </form>

        <div class="postList" style="overflow-x:auto;margin-bottom:15px;">
            <table id="dtBasicExample" class="table table-sm  table-bordered table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="text-left">
                            <span class="form-check text-center">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                                <label class="form-check-label" for="checkAll"></label>
                            </span>
                        </th>
                        <th class="text-center" id="id-desc" data-toggle="tooltip" title="ID đơn hàng">#ID</th>
                        <th class="text-center" data-toggle="tooltip" title="Số hiệu">Số hiệu</th>
                        <th class="text-center" data-toggle="tooltip" title="Bưu điện">Bưu điện</th>
                        <th class="text-center" data-toggle="tooltip" title="Sản phẩm muốn mua">Offer</th>
                        <th class="text-center" id="time-desc" data-toggle="tooltip" title="Thời gian đặt hàng">Time</th>
                        <th class="text-center" data-toggle="tooltip" title="Thời gian ship">Ship Time</th>
                        <th class="text-center" data-toggle="tooltip" title="Trạng thái hiện tại">Status</th>
                        <th class="text-center" data-toggle="tooltip" title="Trạng thái mới">Update Status</th>
                        <th class="text-center" id="order_name-asc" data-toggle="tooltip" title="Tên người muốn mua">Order Name</th>
                        <th class="text-center" data-toggle="tooltip" title="Số điện thoại liên hệ">Order Phone</th>
                        <th class="text-center" data-toggle="tooltip" title="Ghi chú">Note</th>
                        <th class="text-center" data-toggle="tooltip" title="Thông tin Order">Order info</th>
                    </tr>
                </thead>
                <tbody id="crawl_data">
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="ajax-loading"></div>

<script>
    var updateStatus = {};

    $('#checkAll').on("change", function() {
        var checkboxes = $('td input[type=checkbox]');
        if ($(this).prop('checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
        var checked = $('td input[type=checkbox]:checked');
        if (checked.length > 0) {
            $("[role=btn-updateOrder]").prop('disabled', false);
        } else {
            $("[role=btn-updateOrder]").prop('disabled', true);
        }
    });
    $(document).on("change", 'td input[type=checkbox]', function() {
        var checked = $('td input[type=checkbox]:checked');
        if (checked.length > 0) {
            $("[role=btn-updateOrder]").prop('disabled', false);
        } else {
            $("[role=btn-updateOrder]").prop('disabled', true);
        }
    });
    $('#crawl_form').on('submit', function(event) {
        $('.ajax-loader').show();
        event.preventDefault();
        $.ajax({
            url: "<?= $_url; ?>/ajax.php?act=crawlVnpost",
            method: "POST",
            data: {
                ts: $(this).children('input[name="ts"]').val(),
                te: $(this).children('input[name="te"]').val()
            },
            success: function(data) {
                let response = JSON.parse(data);
                updateStatus = response.status; // Update cralw orders's status to param
                $('#crawl_data').html(response.html);
                $('.ajax-loader').hide();
            }
        });
    });

    function updateOrders() {
        var checked = $('td input[type=checkbox]:checked');
        if (checked.length > 0) {
            let oids = [];
            checked.each(function(index) {
                oids.push($(this).val());
            });
            updateOrder(oids);
        }
    }

    function updateOrder(oid) {
        event.preventDefault();
        postData = {};
        if ($.isArray(oid)) {
            $.each(oid, function(index, value) {
                if (updateStatus[value])
                    postData[value] = updateStatus[value];
            })
        } else {
            postData[oid] = updateStatus[oid];
        }
        $.ajax({
            url: "<?= $_url; ?>/ajax.php?act=updateOrderStatus",
            method: "POST",
            data: {
                orders: postData
            },
            beforeSend: function() {
                $('.ajax-loader').show();
            },
            success: function(data) {
                let response = JSON.parse(data);
                // $('#crawl_data').html(response.html);
                if ($.isArray(oid)) {
                    $.each(oid, function(index, value) {
                        if (updateStatus[value])
                            $('tr#order-'+value).remove();
                    })
                } else {
                    $('tr#order-'+oid).remove();
                }
                $('.ajax-loader').hide();
                toastr.success(response.message);
            }
        });
    }

    $(function() {
        var start = '<?= $ts; ?>';
        var end = '<?= $te; ?>';

        function cb(start, end) {
            $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            $("form[name=filter] input[name=ts]").val(start.format('DD/MM/YYYY'));
            $("form[name=filter] input[name=te]").val(end.format('DD/MM/YYYY'));
        }
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Hôm nay': [moment(), moment()],
                'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 ngày gần nhất': [moment().subtract(6, 'days'), moment()],
                '30 ngày gần nhất': [moment().subtract(29, 'days'), moment()],
                'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Áp dụng",
                "cancelLabel": "Đặt lại",
                "fromLabel": "Từ ngày",
                "toLabel": "Đến ngày",
                "customRangeLabel": "Tùy chỉnh ngày",
                "weekLabel": "Tuần",
                "daysOfWeek": [
                    "CN",
                    "T2",
                    "T3",
                    "T4",
                    "T5",
                    "T6",
                    "T7"
                ],
                "monthNames": [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12"
                ],
                "firstDay": 1
            },
            showDropdowns: true,
            alwaysShowCalendars: true,
            linkedCalendars: false,
            autoApply: false,
            autoUpdateInput: true
        }, cb);
        $('#reportrange span').html(start + ' - ' + end);
        $("form[name=filter] input[name=ts]").val(start);
        $("form[name=filter] input[name=te]").val(end);

        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            $("form[name=filter] input[name=ts]").val(picker.startDate.format('DD/MM/YYYY'));
            $("form[name=filter] input[name=te]").val(picker.endDate.format('DD/MM/YYYY'));
        });
        $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
            $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days').format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
        });
    });
</script>