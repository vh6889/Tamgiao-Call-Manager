<style type="text/css">
    .load-more {
        width: 99%;
        font-size: 20px;
        font-weight: bold;
        background: #15a9ce;
        text-align: center;
        color: white;
        padding: 10px 0px;
        font-family: sans-serif;
    }
    .load-more:hover {
        cursor: pointer;
    }
</style>
<?php
$_status = 'shipfail';
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status . '_offer']) ? $_COOKIE[$_status . '_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y', strtotime('-2 month GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y', time());
$view = isset($_GET['view']) && in_array($_GET['view'], ['me', 'all', 'group']) ? $_GET['view'] : (isset($_COOKIE[$_status . '_view']) ? $_COOKIE[$_status . '_view'] : (isAller() ? 'all' : 'group'));
$user = isset($_GET['user']) ? $_GET['user'] : '';
if (((!isLeader() && $view == "all") && !isAller()) || ((!isLeader() && $view == "group") && !isAller())) {
    $view = "me";
} else if ((isLeader() && $view == "all") && !isAller()) {
    $view = "group";
}
if (isset($_GET['offer']) && $_GET['offer']) {
    setcookie($_status . '_offer', $offer, time() + 3600 * 24 * 365);
}
if (isset($_GET['view']) && $_GET['view']) {
    setcookie($_status . '_view', $view, time() + 3600 * 24 * 365);
}
if (isset($_GET['offer']) && !$_GET['offer']) {
    setcookie($_status . '_offer', "");
}
if (isset($_GET['view']) && !$_GET['view'] || $_GET['view'] == "me") {
    setcookie($_status . '_view', "");
}
$_group = getGroup($_user['group']);
$_offers = getOffer();
?>
<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>
<div id="crudApp">
    <section class="row">
        <div class="col-md-6">
            <h2 class="section-heading mb-4"><span class="badge <?= getBgOrder($_status); ?>">Ship Failed</span> <?php echo $lang['send-unsuccessful']; ?> (<span v-html="totalCount"></span>)</h2>
        </div>
        <div class="col-md-6 searchBox">
            <div class="input-group form-sm form-2 pl-0 mb-3">
                <input id="searchAll" class="form-control my-0 py-1 bg-white" type="text" placeholder="Search by Name or Phone" aria-label="Search">
                <div class="input-group-append">
                    <span class="input-group-text dark lighten-3" id="basic-text1"><i class="fas fa-search text-grey" aria-hidden="true"></i></span>
                </div>
                <div id="result_box"> </div>
            </div>
        </div>
    </section>
    <section class="row mb-5 pb-3">
        <div class="col-md-12 mx-auto white z-depth-1">
            <div class="mb-5">
                <form name="filter" method="GET">
                    <input name="route" value="order" type="hidden">
                    <input name="type" value="shipfail" type="hidden">
                    <input name="ts" value="" type="hidden">
                    <input name="te" value="" type="hidden">
                    <?php if ($user) { ?>
                        <input name="user" value="<?= _e($user); ?>" type="hidden">
                    <?php } ?>
                    <div class="row mb-3">
                        <div class="col-sx-12 col-md-5 pb20 row">
                            <div class="col-sx-12 col-md-6">
                                <select role="filter-select" id="filter-offer" class="mdb-select" name="offer">
                                    <option value="all" selected>All Offer</option>
                                    <?php if ($_group['offers'] || isAller()) {
                                        foreach ($_offers as $of) {
                                            if (preg_match("#\|" . $of['id'] . ",#si", $_group['offers']) || isAller())
                                                echo '<option value="' . $of['id'] . '" ' . ($offer == $of['id'] ? 'selected' : '') . '>' . _e($of['name']) . '</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-sx-12 col-md-6">
                                <select role="filter-select" id="filter-view" class="mdb-select" name="view">
                                    <option value="me" selected>Yourself</option>
                                    <?php if (isAller()) { ?>
                                        <option value="all" <?= ($view == "all" ? 'selected' : ''); ?>>All Members</option>
                                    <?php } ?>
                                    <?php if (isLeader()) { ?>
                                        <option value="group" <?= ($view == "group" ? 'selected' : ''); ?>>Group Members</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sx-12 col-md-4 pb20">
                            <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span>
                                <i class="fa fa-caret-down"></i>
                            </span>
                        </div>
                        <div class="col-sx-12 col-md-3 pb20">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Apply Filter</button>
                            <button class="btn btn-danger waves-effect waves-light" type="button" id="clear-filter">Clear</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-3" style="margin-bottom:15px;">
                        <span class="custom_select_show">Hiện thị</span>
                        <select name="pageSize" v-model="limit" id="pageSize" class="custom_select" @change="changePage($event)">
                            <option v-for="pageSize in pageSizes" :value="pageSize.id" :key="pageSize.name">{{ pageSize.name }}</option>
                        </select>
                        <span class="custom_select_record">bản ghi</span>
                        <div class="clear-fix"></div>
                    </div>
                    <div class="col-md-9 text-right">
                        <?php if (isShipper()) { ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <button @click="makeApprove" class="btn btn-info waves-effect waves-light" :disabled="disabled == false">Make Approve<i class="fas fa-user-check ml-1"></i></button>
                                    <button @click="makeShipError" class="btn btn-warning waves-effect waves-light" :disabled="disabled == false">Make ShipError<i class="fas fa-exclamation-circle ml-1"></i></button>
                                    <!--<button role="btn-importExcel" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#importExcel">Import Excel<i class="fas fa-file-import ml-1"></i></button>-->
                                    <button @click="cancelAllSelected" class="btn btn-danger waves-effect waves-light" :disabled="disabled == false">Cancel All Selected<i class="fas fa-times ml-1"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <br>
                </div>
                <input type="hidden" id="limit_hidden" v-model="limit_hidden" name="limit_hidden">
                <input type="hidden" id="totalCount" v-model="totalCount" name="totalCount">
                <input type="hidden" id="totalId" v-model="totalId" name="totalId">
                <div class="postList" style="overflow-x:auto;margin-bottom:15px;">
                    <table id="dtBasicExample" class="table table-sm  table-bordered table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-left">
                                    <?php if (isShipper()) { ?>
                                        <span class="form-check text-center">
                                            <input type="checkbox" class="form-check-input" id="checkAll" v-model="selectAll">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </span>
                                    <?php } ?>
                                </th>
                                <th class="text-center sort-heading table_desc" id="id-desc" data-toggle="tooltip" title="ID đơn hàng">#ID</th>
                                <th class="text-center sort-heading table_desc" id="update_time-desc" data-toggle="tooltip" title="Last Update">Last Update</th>
                                <th class="text-center sort-heading" data-toggle="tooltip" title="Số hiệu">Số hiệu</th>
                                <th class="text-center sort-heading" data-toggle="tooltip" title="Bưu điện">Bưu điện</th>
                                <th class="text-left" data-toggle="tooltip" title="Thông tin Order">Order info</th>
                                <th class="text-center" data-toggle="tooltip" title="Sản phẩm muốn mua">Offer</th>
                                <th class="text-center sort-heading table_desc" id="number-desc" data-toggle="tooltip" title="Số lượng mua">Number&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th class="text-center sort-heading table_desc" id="price_sell-desc" data-toggle="tooltip" title="Tổng tiền">Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th class="text-center sort-heading table_desc" id="time-desc" data-toggle="tooltip" title="Thời gian đặt hàng">Time</th>
                                <th class="text-center sort-heading table_asc" id="order_name-asc" data-toggle="tooltip" title="Tên người muốn mua">Order Name</th>
                                <th class="text-center" data-toggle="tooltip" title="Số điện thoại liên hệ">Order Phone</th>
                                <th class="text-left" data-toggle="tooltip" title="Ghi chú">Note</th>
                                <th class="text-left" data-toggle="tooltip" title="Địa chỉ mua hàng">Address</th>
                                <th class="text-left" data-toggle="tooltip" title="Tỉnh">Province</th>
                                <?php if (isShipper()  || isLeader() || isAller() || isPublisher()) { ?>
                                    <th class="text-center" data-toggle="tooltip" title="Người gọi">Caller</th>
                                <?php } ?>
                                <?php if (isCaller() || isColler()  || isLeader() || isAller() || isPublisher()) { ?>
                                    <th class="text-center" data-toggle="tooltip" title="Người nhận ship">Shipper</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in allData" :data-id="''+row.id">
                                <td class="text-center">
                                    <?php
                                    if (isShipper()) {
                                    ?>
                                        <input type="checkbox" v-model="checkedOrder" @change="check($event)" class="form-check-input" :id="'order_'+row.id" :value="''+row.id">
                                        <label class="form-check-label px-2" :for="'order_'+row.id"></label>
                                    <?php
                                    }
                                    ?>
                                    <a :href="'?route=editOrder&id='+row.id">
                                        <span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span>
                                    </a>
                                    <?php
                                    if (isShipper()) {
                                    ?>
                                        <span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" @click="deleteData(row.id)">
                                            <i class="fas fa-times ml-1"></i>
                                        </span>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td class="text-center">Đơn hàng #<strong v-html="row.id"></strong></td>
                                <td style="padding-left:30px;padding-right:30px;" class="text-center"><span v-html="row.update_time"></span></td>
                                <td class="text-center"><span v-html="row.parcel_code"></span></td>
                                <td class="text-center"><span v-html="row.post_office"></span></td>
                                <td class="text-left"><span v-html="row.order_info"></span></td>
                                <td class="text-center"><strong v-html="row.offer_name"></strong></td>
                                <td class="text-center">x<strong v-html="row.number"></strong></td>
                                <td class="text-center"><strong v-html="row.price_sell"></strong>k</td>
                                <td class="text-center"><span v-html="row.time"></span></td>
                                <td class="text-center"><span v-html="row.order_name"></span></td>
                                <td class="text-center"><span v-html="row.order_phone"></span></td>
                                <td class="text-left"><span v-html="row.note"></span></td>
                                <td class="text-left" v-html="row.note_read"></td>
                                <td class="text-left"><span v-html="row.order_province"></span></td>
                                <td class="text-center" v-html="row.order_caller"></td>
                                <td class="text-center" v-html="row.order_shipper"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h3 class="load-more" @click="readMore" v-if="hideMore">Xem thêm</h3>
            </div>
        </div>
    </section>
    <?php if (isShipper()) { ?>
        <?php include('template/modal/delete/delete.php') ?>
        <?php include('template/modal/shipfail/shiperror.php') ?>
        <?php include('template/modal/shipfail/approve.php') ?>
    <?php } ?>
</div>
<div class="loader-overlay">
    <div class="loader-content-container">
        <div class="loader-content">
            <div class="spinner-grow" role="status" style="width: 6rem; height: 6rem;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>
<script>
    var app = new Vue({
        el: '#crudApp',
        data: {
            allData: [],
            checkedOrder: [],
            pageSizes: [{
                    id: '100',
                    name: '100'
                },
                {
                    id: '250',
                    name: '250'
                },
                {
                    id: '500',
                    name: '500'
                },
                {
                    id: '750',
                    name: '750'
                },
                {
                    id: '1000',
                    name: '1000'
                },
            ],
            totalId: [],
            tableId: 0,
            limit: 100,
            limit_hidden: 100,
            hideMore: true,
            totalCount: 0,
            myArray: 0,
            disabled: false,
            myModelApprove: false,
            showModal: false,
            modelApproveTitle: 'Đánh dấu giao hàng thành công',
            modelApproveBody: 'Bạn thực sự muốn đánh dấu những đơn hàng này là đã giao thành công?',
            myModelShiperror: false,
            modelShiperrorTitle: 'Đánh dấu khách không nhận hàng',
            modelShiperrorBody: 'Bạn thực sự muốn đánh dấu những đơn hàng này là khách không nhận hàng?',
            delete_title: 'Hủy đơn hàng',
            delete_title_body: 'Bạn thực sự muốn từ bỏ vận chuyển đơn hàng này?'
        },
        computed: {
            selectAll: {
                get: function() {
                    return this.allData ? this.checkedOrder.length == this.allData.length : false;
                },
                set: function(value) {
                    var checkedOrder = [];
                    if (value) {
                        this.allData.forEach(function(event) {
                            checkedOrder.push(event.id);
                        });
                    }
                    this.checkedOrder = checkedOrder;
                    if (checkedOrder.length > 0) {
                        this.disabled = true;
                    } else {
                        this.disabled = false;
                    }
                    this.totalId = this.checkedOrder;
                }
            }
        },
        methods: {
            check: function(e) {
                app.totalId = this.checkedOrder;
                if (this.checkedOrder.length > 0) {
                    app.disabled = true;
                } else {
                    app.disabled = false;
                }
            },
            makeApprove: function() {
                app.myModelApprove = true;
            },
            makeShipError: function() {
                app.myModelShiperror = true;
            },
            doModelShiperror: function() {
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail', {
                    action: 'makeShiperror',
                    id: this.totalId
                }).then(function(response) {
                    alert(response.data.message);
                    app.myModelShiperror = false;
                    app.fetchAllData();
                });
            },
            cancelAllSelected: function() {
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail', {
                    action: 'cancelAllSelected',
                    id: this.totalId
                }).then(function(response) {
                    alert(response.data.message);
                    app.myModelApprove = false;
                    app.fetchAllData();
                });
            },
            deleteData: function(id) {
                this.tableId = id;
                this.showModal = true;
            },
            doRemovePostback: function() {
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail', {
                    action: 'delete',
                    id: this.tableId
                }).then(function(response) {
                    alert(response.data.message);
                    app.fetchAllData();
                    app.showModal = false;
                });
            },
            doModelApprove: function() {
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail', {
                    action: 'makeApprove',
                    id: this.totalId
                }).then(function(response) {
                    alert(response.data.message);
                    app.myModelApprove = false
                    app.fetchAllData();
                });
            },
            changePage(event) {
                this.limit_hidden = parseInt(event.target.value);
                this.limit = parseInt(this.limit_hidden);
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>', {
                    action: 'fetchall',
                    limit: this.limit,
                }).then(function(response) {
                    app.limit_hidden = app.limit_hidden;
                    app.limit = event.target.value;
                    if (app.limit_hidden > app.totalCount) {
                        app.hideMore = false;
                    }
                    app.allData = response.data;
                });
            },
            fetchAllData: function() {
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>&countLength=1', {
                    action: 'fetchall'
                }).then(function(response) {
                    app.totalCount = response.data.length;
                });
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>', {
                    limit: this.limit,
                    action: 'fetchall'
                }).then(function(response) {
                    app.allData = response.data;
                });
            },
            readMore: function() {
                this.limit = parseInt(this.limit_hidden) + 100;
                axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-shipfail&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>', {
                    action: 'fetchall',
                    limit: this.limit,
                }).then(function(response) {
                    app.limit_hidden = app.limit_hidden + app.limit;
                    app.limit = 100;
                    if (app.limit_hidden > app.totalCount) {
                        app.hideMore = false;
                    }
                    app.allData = response.data;
                });
            },
        },
        created: function() {
            this.fetchAllData();
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        loadOrderBy('<?= $_url; ?>/ajax.php?act=orderby-orderShipFail&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>');
    });
</script>
<script type="text/javascript">
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
            $('#reportrange span').html(moment().subtract(6, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(6, 'days').format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
            $("form[name=filter] input[name=ts]").val('');
            $("form[name=filter] input[name=te]").val('');
            $("#filter-offer").val('all');
            $("#filter-view").val('all');
            $("[role=filter-select]").materialSelect();
        });
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            $("form[name=filter] input[name=ts]").val(picker.startDate.format('DD/MM/YYYY'));
            $("form[name=filter] input[name=te]").val(picker.endDate.format('DD/MM/YYYY'));
        });
        $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
            $('#reportrange span').html(moment().subtract(6, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(6, 'days').format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
        });
    });
</script>
<?php
end:
?>