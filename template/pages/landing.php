<?php
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status . '_offer']) ? $_COOKIE[$_status . '_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y', strtotime('today GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y', time());
if (isset($_GET['offer']) && $_GET['offer'])
    setcookie('landing_offer', $offer, time() + 3600 * 24 * 365);
if (isset($_GET['offer']) && !$_GET['offer'])
    setcookie('landing_offer', "");
if (!isAdmin() && !isPublisher()) {
    echo '<div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">An error occurred!</h4>
        <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
        <hr>
        <p class="mb-0">Please return to the previous page.</p>
      </div>';
    goto end;
}
$_offers = getOffer();
$sql = "";
if ($offer != "all")
    $sql = " and `offer`='" . escape_string($offer) . "' ";
$list_time = array();
$time_ts = strtotime(str_replace('/', '-', $ts) . " GMT+7 00:00");
$time_te = strtotime(str_replace('/', '-', $te) . " GMT+7 23:59");
if ($time_ts && $time_te) {
    for ($i = $time_ts; $i <= $time_te; $i += 86400) {
        $list_time[] = date("d-m-Y", $i);
    }
}
$perPage = 50;

if(!isAdmin() && isPublisher()){
    $sql .= " and `ukey`= '".$_user['ukey']."'";
}

$_count = $_db->query("select * from `core_landing_stats` where `date` in ('" . implode("','", $list_time) . "') " . $sql . " order by id DESC")->num_rows();
$_data = $_db->query("select `id`,`offer`,`date`,`landing`, sum(`viewPage`) as `viewPage`, sum(`order`) as `order`,`ukey` from `core_landing_stats` where `date` in ('" . implode("','", $list_time) . "') " . $sql . " group by `landing`,`date` order by id DESC LIMIT $perPage")->fetch_array();
?>
<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>
<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>
<h2 class="section-heading mb-4">Thống kê Landing Page (<?= $_count; ?>)</h2>
<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1">
        <form name="filter" method="GET">
            <input name="route" value="landing" type="hidden">
            <input name="ts" value="" type="hidden">
            <input name="te" value="" type="hidden">
            <div class="row mb-3">
                <div class="col-sx-12 col-md-4 pb20">
                    <select role="filter-select" id="filter-offer" class="mdb-select" name="offer">
                        <option value="all" selected>All Offer</option>
                        <?php
                        foreach ($_offers as $of) {
                            echo '<option value="' . $of['id'] . '" ' . ($offer == $of['id'] ? 'selected' : '') . '>' . _e($of['name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sx-12 col-md-4 pb20">
                    <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span>
                        <i class="fa fa-caret-down"></i>
                    </span>
                </div>
                <div class="col-sx-12 col-md-4 pb20">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">Apply Filter</button>
                    <button class="btn btn-danger waves-effect waves-light" type="button" id="clear-filter">Clear</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-10" style="margin-bottom:15px;">
                <span class="custom_select_show">Hiện thị</span>
                <select name="pageSize" id="pageSize" class="custom_select">
                    <!-- <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option> -->
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="100">250</option>
                    <option value="100">500</option>
                </select>
                <span class="custom_select_record">bản ghi</span>
                <div class="clear-fix"></div>
            </div>
            <div class="col-md-2 text-right">

            </div>
            <br>
        </div>
        <input type="hidden" id="limit" name="limit" value="10">
        <input type="hidden" id="totalCount" name="limit" value="<?php echo $_count; ?>">
        <div class="postList" style="overflow-x:auto;margin-bottom:15px;">
            <table id="dtBasicExample" class="table table-sm table-hover table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="text-left sort-heading table_asc" id="date-asc" data-toggle="tooltip" title="Thời gian">Time</th>
                        <th class="text-left sort-heading table_asc" id="offer-asc" data-toggle="tooltip" title="Sản phẩm">Offer</th>
                        <th class="text-left" data-toggle="tooltip" title="Landing Page">Landing</th>
                        <th class="text-center sort-heading table_desc" id="viewPage-desc" data-toggle="tooltip" title="Số lần hiển thị">PageView</th>
                        <th class="text-center" data-toggle="tooltip" title="Số lần mua hàng">Purchase</th>
                        <th class="th-sm text-center" data-toggle="tooltip" title="Tỉ lệ chấp nhận đơn hàng">APO (%)</th>
                        <th class="th-sm text-center" data-toggle="tooltip" title="Sản phẩm bán được ban đầu">Pre Sales</th>
                        <th class="th-sm text-center" data-toggle="tooltip" title="Từ chối mua hàng">Uncheck</th>
                        <th class="th-sm text-center" data-toggle="tooltip" title="Từ chối mua hàng">Call Error</th>
                        <th class="th-sm text-center" data-toggle="tooltip" title="Từ chối mua hàng">Rejected</th>
                        <th class="th-sm text-center" data-toggle="tooltip" title="Đơn hàng rác">Trashes</th>
                    </tr>
                </thead>
                <tbody id="content">
                    <?php
                    $total_viewPage = 0;
                    $total_order = 0;
                    $total_count_apo = 0;
                    $total_sales = 0;
                    $total_uncheck = 0;
                    $total_callerror = 0;
                    $total_rejected = 0;
                    $total_trashed = 0;
                    if ($_data) {
                        foreach ($_data as $arr) {
                            $offer = getOffer($arr['offer']);
                            $_publisher_query = (!isAdmin() && isPublisher()) ? " and `ukey`= '".$_user['ukey']."'" : '';
                            $orders = $_db->query("select * from `core_orders` where `offer`='" . $offer['id'] . "' and (`time` >= '" . (strtotime($arr['date'] . " GMT+7 00:00")) . "' and `time` < '" . (strtotime($arr['date'] . " GMT+7 23:59")) . "') and `landing`='" . $arr['landing'] . "'".$_publisher_query)->fetch_array();
                            $od_uncheck = 0;
                            $od_callerror = 0;
                            $od_rejected = 0;
                            $od_trashed = 0;
                            $od_sales = 0;
                            $count_apo = 0;
                            foreach ($orders as $od) {
                                if ($od['status'] == "uncheck")
                                    $od_uncheck = $od_uncheck + 1;
                                if ($od['status'] == "callerror")
                                    $od_callerror = $od_callerror + 1;
                                if ($od['status'] == "rejected")
                                    $od_rejected = $od_rejected + 1;
                                if ($od['status'] == "trashed")
                                    $od_trashed = $od_trashed + 1;
                                if (in_array($od['status'], ['approved', 'shipping', 'pending', 'shipdelay', 'shiperror'])) {
                                    $od_sales = $od_sales + $od['number'];
                                    $count_apo = $count_apo + 1;
                                }
                            }
                            $apo = count($orders) > 0 ? round($count_apo / count($orders) * 100, 2) : 0;
                            echo '<tr class="tr-statistic">';
                            echo '<td class="text-left">' . $arr['date'] . '</b></td>';
                            echo '<td class="text-left">' . ($offer ? '<b  class="trigger green lighten-3">' . _e($offer['name']) . '</b>' : 'Unknown') . '</td>';
                            echo '<td class="text-left"><b class="number">' . _e($arr['landing']) . ' <a target="_blank" href="https://' . _e($arr['landing']) . '"><i class="fas fa-external-link-alt ml-1"></i></a></b></td>';
                            echo '<td class="text-center">' . ($arr['viewPage'] > 0 ? '<b class="number">' . $arr['viewPage'] . '</b>' : '0') . '</td>';
                            echo '<td class="text-center">' . ($arr['order'] > 0 ? '<b class="number">' . $arr['order'] . '</b>' : '0') . '</td>';
                            echo '<td class="text-center">' . ($apo > 0 ? '<b class="number">' . $apo . '</b>% (<small>' . $count_apo . '</small>)' : '0') . '</td>';
                            echo '<td class="text-center">' . ($od_sales > 0 ? '<b class="number">' . $od_sales . '</b>' : '0') . '</td>';
                            echo '<td class="text-center">' . ($od_uncheck > 0 ? '<b class="number">' . $od_uncheck . '</b>' : '0') . '</td>';
                            echo '<td class="text-center">' . ($od_callerror > 0 ? '<b class="number">' . $od_callerror . '</b>' : '0') . '</td>';
                            echo '<td class="text-center">' . ($od_rejected > 0 ? '<b class="number">' . $od_rejected . '</b>' : '0') . '</td>';
                            echo '<td class="text-center">' . ($od_trashed > 0 ? '<b class="number">' . $od_trashed . '</b>' : '0') . '</td>';
                            echo '</tr>';
                            $total_viewPage = $total_viewPage + $arr['viewPage'];
                            $total_order = $total_order + $arr['order'];
                            $total_count_apo = $total_count_apo + $count_apo;
                            $total_callerror = $total_callerror + $od_callerror;
                            $total_uncheck = $total_uncheck + $od_uncheck;
                            $total_rejected = $total_rejected + $od_rejected;
                            $total_trashed = $total_trashed + $od_trashed;
                            $total_sales = $total_sales + $od_sales;
                        }
                    }
                    ?>
                </tbody>
                <?php
                $total_apo = $total_order > 0 ? round($total_count_apo / $total_order * 100, 2) : 0;
                ?>
                <tfoot>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center"><?= ($total_viewPage > 0 ? '<b class="number">' . $total_viewPage . '</b>' : '0'); ?></td>
                    <td class="text-center"><?= ($total_order > 0 ? '<b class="number">' . $total_order . '</b>' : '0'); ?></td>
                    <td class="text-center"><?= ($total_apo > 0 ? '<b class="number">' . $total_apo . '</b>% (<small>' . $total_count_apo . '</small>)' : '0'); ?></td>
                    <td class="text-center"><?= ($total_sales > 0 ? '<b class="number">' . $total_sales . '</b>' : '0'); ?></td>
                    <td class="text-center"><?= ($total_uncheck > 0 ? '<b class="number">' . $total_uncheck . '</b>' : '0'); ?></td>
                    <td class="text-center"><?= ($total_callerror > 0 ? '<b class="number">' . $total_callerror . '</b>' : '0'); ?></td>
                    <td class="text-center"><?= ($total_rejected > 0 ? '<b class="number">' . $total_rejected . '</b>' : '0'); ?></td>
                    <td class="text-center"><?= ($total_trashed > 0 ? '<b class="number">' . $total_trashed . '</b>' : '0'); ?></td>
                </tfoot>
            </table>
        </div>
        <?php
        if ($_count > 10) {
        ?>
            <h3 class="load-more">Xem thêm</h3>
        <?php
        }
        ?>
    </div>
</section>
<div class="loader-overlay">
    <div class="loader-content-container">
        <div class="loader-content">
            <div class="spinner-grow" role="status" style="width: 6rem; height: 6rem;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>
<?php $offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status . '_offer']) ? $_COOKIE[$_status . '_offer'] : 'all'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#pageSize").on('change', function() {
            var limit_curent = parseInt($(this).val());
            $("#limit").val((limit_curent));
            limit = parseInt($(this).val());
            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    limit: limit
                },
                url: '<?= $_url; ?>/ajax.php?act=pagination-newLanding&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>',
                success: function(html) {
                    $("#content").html(html);
                    loadPagination(limit);
                }
            });
        });
        $(document).on('click', '.load-more', function() {
            var limit_curent = parseInt($("#limit").val());
            $("#limit").val((limit_curent + 10));
            loadPagination(limit_curent + 10);
        });
        loadOrderBy('<?= $_url; ?>/ajax.php?act=orderby-newLanding&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>');
        $("#btn-exportExcel").on('click', function() {
            $(".loader-overlay").show();
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?= $_url; ?>/ajax.php?act=export-landing&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>', true);
            xhr.responseType = 'blob';
            xhr.onload = function(e) {
                if (this.status == 200) {
                    var blob = new Blob([this.response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var downloadUrl = URL.createObjectURL(blob);
                    var a = document.createElement("a");
                    a.href = downloadUrl;
                    a.download = "export_<?= date('d-m-Y', time()); ?>.xlsx";
                    document.body.appendChild(a);
                    a.click();
                    toastr.success('Export Excel thành công.');
                } else {
                    toastr.error('Export Excel thất bại.');
                }
                $(".loader-overlay").hide();
            };
            xhr.send(data);
        });
    });
</script>
<script type="text/javascript">
    function loadPagination(limit) {
        $(".load-more").text("Load more");
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                limit: limit
            },
            beforeSend: function() {
                $(".load-more").text("Loading...");
            },
            url: '<?= $_url; ?>/ajax.php?act=pagination-newLanding&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>',
            success: function(html) {
                $("#content").html(html);
                $(".load-more").text("Load more");
                var limit_check = parseInt($("#limit").val());
                var totalCount = parseInt($("#totalCount").val());
                if (totalCount < limit_check) {
                    $(".load-more").css("display", 'none');
                }
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
        $("#clear-filter").on("click", function() {
            $('#reportrange span').html(moment().format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
            $("form[name=filter] input[name=ts]").val('');
            $("form[name=filter] input[name=te]").val('');
            $("#filter-offer").val('all');
            $("[role=filter-select]").materialSelect();
        });
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            $("form[name=filter] input[name=ts]").val(picker.startDate.format('DD/MM/YYYY'));
            $("form[name=filter] input[name=te]").val(picker.endDate.format('DD/MM/YYYY'));
        });
        $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
            $('#reportrange span').html(moment().format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
        });
    });
</script>
<?php
end:
?>