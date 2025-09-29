<?php
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] : date('d/m/Y', strtotime('today GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] : date('d/m/Y', time());
if (isset($_GET['type'])) {
    if ($_GET['type'] == 'success') {
        $response_code = 200;
    } elseif ($_GET['type'] == 'fail') {
        $response_code = 404;
    }
} else {
    $response_code = 200;
}
?>
<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>
<div id="crudApp">
    <section class="row">
        <div class="col-md-6">
            <h2 class="section-heading mb-4">
                <?php
                if (isset($_GET['type'])) {
                    if ($_GET['type'] == 'success') {
                        echo 'Postback Success';
                    } else {
                        echo 'Postback Fail';
                    }
                } else {
                    echo 'Postback Success';
                }
                ?> (<span v-html="myArray"></span>)
            </h2>
        </div>
    </section>
    <section class="row mb-5 pb-3">
        <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
            <form name="filter" method="GET">
                <input name="route" value="postback" type="hidden">
                <input name="type" value="<?= $_GET['type'] ?>" type="hidden">
                <input name="ts" type="hidden" id="ts_vue" class="form-control" value="<?php echo $ts; ?>">
                <input name="te" type="hidden" id="te_vue" class="form-control" value="<?php echo $te; ?>">
                <div class="row">
                    <div class="col-md-4">
                        <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 0px;">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span>
                            <i class="fa fa-caret-down"></i>
                        </span>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control" v-model='types' id="type_ads_id" name="type_ads_id">
                                <option value="0">Type</option>
                                <option v-for="data in ads" :value="data.id">{{data.ads}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control" v-model="offer_id" id="offer_id" name="offer_id">
                                <option value="0">Offers</option>
                                <option v-for="data in offers" :value="data.id">{{data.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 pb20 text-right">
                        <button class="btn btn-primary" @click="doSearchPostback">Apply Filter</button>
                        <button class="btn btn-danger" type="button" id="clear-filter">Clear</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-2" style="margin-bottom:15px;">
                    <span class="custom_select_show">Hiện thị</span>
                    <select name="pageSize" v-model="limit" id="pageSize" class="custom_select" @change="changePage($event)">
                        <option v-for="pageSize in pageSizes" :value="pageSize.id" :key="pageSize.name">{{ pageSize.name }}</option>
                    </select>
                    <span class="custom_select_record">bản ghi</span>
                    <div class="clear-fix"></div>
                </div>
                <div class="col-md-10 text-right">
                    <input type="hidden" id="i-type" value="postback">
                    <input type="hidden" id="i-type-ads-id" value="<?php echo $type_ads_id; ?>">
                    <input type="hidden" id="i-offer-id" value="<?php echo $offer_id; ?>">
                    <input type="hidden" id="i-ts" value="<?php echo $ts; ?>">
                    <input type="hidden" id="i-te" value="<?php echo $te; ?>">
                    <input type="hidden" id="i-response-code" value="<?php echo $response_code; ?>">
                    <button id="export_excel" class="btn btn-success waves-effect waves-light">Export Excel<i class="fas fa-file-export ml-1"></i></button>
                </div>
            </div>
            <div class="mb-5">
                <input type="hidden" id="totalCount" v-model="limit_hidden" name="limit_hidden">
                <input type="hidden" id="totalCount" v-model="totalCount" name="totalCount">
                <div class="postList" style="overflow-x:auto;margin-bottom:15px;">
                    <table style="margin-top:10px;" class="table table-sm table-hover table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr class="bg-info text-light" style="color:#fff;">
                                <th></th>
                                <th style="cursor:pointer;" class="text-center text-white" title="ID Comment">#ID</th>
                                <th style="cursor:pointer;color:#fff;" class="text-center text-white" title="Offer">Offer</th>
                                <th style="cursor:pointer;color:#fff;" class="text-center text-white" title="Landing Page">Landing Page</th>
                                <th class="text-center text-white" title="Date">Date</th>
                                <th class="text-center text-white" title="Commtent Category">Type</th>
                                <th class="text-center text-white" title="Commtent Category">State</th>
                                <th class="text-center text-white" title="Commtent Category">Request url</th>
                                <th class="text-center text-white" title="Nội dung">Response code</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in allData">
                                <td><button type="button" name="delete" class="btn btn-danger btn-xs delete" @click="deleteData(row.id)"><i class="fas fa-trash ml-1"></i></button></td>
                                <td>Postback #<strong v-html="row.id"></strong></td>
                                <td><span v-html="row.offer_id"></span></td>
                                <td><span v-html="row.landing_page"></span></td>
                                <td><span v-html="row.created"></span></td>
                                <td><span v-html="row.type_ads_id"></span></td>
                                <td><span v-html="row.state"></span></td>
                                <td><span v-html="row.request_url"></span></td>
                                <td><span v-html="row.response_code"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h3 class="load-more" @click="readMore" v-if="hideMore">Xem thêm</h3>
            </div>
            <?php include('template/modal/delete/delete.php') ?>

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
</div>
<!--End crudApp-->
<?php
if (isset($_GET['type'])) {
    if ($_GET['type'] == 'success') {
        $url = $_url . '/ajax.php?act=vue_api-postback&type=success&response_code=' . $response_code;
    } elseif ($_GET['type'] == 'fail') {
        $url = $_url . '/ajax.php?act=vue_api-postback&type=fail&response_code=' . $response_code;
    }
} else {
    $url = $_url . '/ajax.php?act=vue_api-postback&type=success&response_code=' . $response_code;
}
?>
<script>
    var app = new Vue({
        el: '#crudApp',
        data: {
            pageSizes: [{
                    id: '10',
                    name: '10'
                },
                {
                    id: '20',
                    name: '20'
                },
                {
                    id: '30',
                    name: '30'
                },
                {
                    id: '50',
                    name: '50'
                },
                {
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
            ],
            delete_title: "Xóa Postback",
            delete_title_body: "Postback sẽ bị xóa vĩnh viễn khỏi hệ thống.Bạn thực sự muốn xóa postback này?",
            hideMore: true,
            ts: $("#ts_vue").val(),
            te: $("#te_vue").val(),
            limit: 10,
            showModal: false,
            limit_hidden: 10,
            totalCount: 0,
            allData: '',
            myArray: '',
            tableId: '',
            types: 0,
            offer_id: 0,
            offers: [],
            ads: [],
        },
        methods: {
            getOffer: function() {
                axios.get('<?= $_url; ?>/ajax.php?act=vue_api-loadOffers')
                    .then(function(response) {
                        app.offers = response.data;
                    })
            },
            getAdsType: function() {
                axios.get('<?= $_url; ?>/ajax.php?act=vue_api-loadAds')
                    .then(function(response) {
                        app.ads = response.data;
                    });
            },
            fetchAllData: function() {
                axios.post('<?php echo $url; ?>&countLength=1', {
                    action: 'fetchall',
                    limit: this.limit,
                    ts: this.ts,
                    te: this.te,
                    types: this.types,
                    offer_id: this.offer_id
                }).then(function(response) {
                    app.totalCount = response.data.length;
                    app.myArray = response.data.length;
                    if (app.totalCount < app.limit_hidden) {
                        app.hideMore = false;
                    }
                });
                axios.post('<?php echo $url; ?>', {
                    action: 'fetchall',
                    limit: this.limit,
                    ts: this.ts,
                    te: this.te,
                    types: this.types,
                    offer_id: this.offer_id
                }).then(function(response) {
                    app.allData = response.data;
                });
            },
            changePage(event) {
                this.limit_hidden = parseInt(event.target.value);
                this.limit = parseInt(this.limit_hidden);
                axios.post('<?php echo $url; ?>', {
                    action: 'fetchall',
                    limit: this.limit,
                    ts: this.ts,
                    te: this.te,
                    types: this.types,
                    offer_id: this.offer_id
                }).then(function(response) {
                    app.limit_hidden = app.limit_hidden;
                    app.limit = event.target.value;
                    if (app.limit_hidden > app.totalCount) {
                        app.hideMore = false;
                    }
                    app.allData = response.data;
                });
            },
            readMore: function() {
                this.limit = parseInt(this.limit_hidden) + 10;
                axios.post('<?php echo $url; ?>', {
                    action: 'fetchall',
                    limit: this.limit,
                    ts: this.ts,
                    te: this.te,
                    types: this.types,
                    offer_id: this.offer_id
                }).then(function(response) {
                    app.limit_hidden = app.limit_hidden + app.limit;
                    app.limit = 10;
                    if (app.limit_hidden > app.totalCount) {
                        app.hideMore = false;
                    }
                    app.allData = response.data;
                });
            },
            deleteData: function(id) {
                this.tableId = id;
                this.showModal = true;
            },
            doRemovePostback: function() {
                axios.post('<?= $_url; ?>/ajax.php?act=vue_api-postback', {
                    action: 'delete',
                    id: this.tableId
                }).then(function(response) {
                    alert(response.data.message);
                    app.fetchAllData();
                    app.showModal = false;
                });
            },
            doSearchPostback: function() {
                app.fetchAllData();
            },
        },
        created: function() {
            this.fetchAllData();
            this.getOffer();
            this.getAdsType();
        }
    })
</script>
<script type="text/javascript">
    $(document).on('click', '#export_excel', function() {
        var type = $("#i-type"),
            offer_id = $("#i-offer-id"),
            type_ads_id = $("#i-type-ads-id"),
            ts = $("#i-ts"),
            te = $("#i-te"),
            response_code = $("#i-response-code");
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('type', type.val());
        data.append('offer_id', offer_id.val());
        data.append('type_ads_id', type_ads_id.val());
        data.append('ts', ts.val());
        data.append('te', te.val());
        data.append('response_code', response_code.val());
        xhr.open('POST', '<?= $_url; ?>/ajax.php?act=excelData&action=export', true);
        xhr.responseType = 'blob';
        xhr.onload = function(e) {
            if (this.status == 200) {
                var blob = new Blob([this.response], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                var downloadUrl = URL.createObjectURL(blob);
                var a = document.createElement("a");
                a.href = downloadUrl;
                a.download = "postback_<?= date('d-m-Y', time()); ?>_.xlsx";
                document.body.appendChild(a);
                a.click();
                toastr.success('Export Excel thành công.');
            } else {
                toastr.error('Export Excel thất bại.');
            }
        };
        xhr.send(data);
    });
    <?php
    if (isset($_GET['type'])) {
        if ($_GET['type'] == 'success') {
    ?>
            loadOrderBy('<?= $_url; ?>/ajax.php?act=orderby-postback&type=success&type_ads_id=<?php echo $type_ads_id; ?>&offer_id=<?php echo $offer_id; ?>&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>');
        <?php
        } elseif ($_GET['type'] == 'fail') {
        ?>
            loadOrderBy('<?= $_url; ?>/ajax.php?act=orderby-postback&type=fail&type_ads_id=<?php echo $type_ads_id; ?>&offer_id=<?php echo $offer_id; ?>&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>');
        <?php
        }
    } else {
        ?>
        loadOrderBy('<?= $_url; ?>/ajax.php?act=orderby-postback&type=success&type_ads_id=<?php echo $type_ads_id; ?>&offer_id=<?php echo $offer_id; ?>&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>');
    <?php
    }
    ?>
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
            $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days').format('DD/MM/YYYY'));
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
            $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days').format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
        });
    });
</script>
<?php
end:
?>