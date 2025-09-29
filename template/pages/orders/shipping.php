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
$_status = 'shipping';
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status . '_offer']) ? $_COOKIE[$_status . '_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y', strtotime('-6 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y', time());
$view = isset($_GET['view']) && in_array($_GET['view'], ['me', 'all', 'group']) ? $_GET['view'] : (isset($_COOKIE[$_status . '_view']) ? $_COOKIE[$_status . '_view'] : (isAller() ? 'all' : 'group'));
$user = isset($_GET['user']) ? $_GET['user'] : '';
if (((!isLeader() && $view == "all") && !isAller()) || ((!isLeader() && $view == "group") && !isAller()))
    $view = "me";
else if ((isLeader() && $view == "all") && !isAller())
    $view = "group";
if (isset($_GET['offer']) && $_GET['offer'])
    setcookie($_status . '_offer', $offer, time() + 3600 * 24 * 365);
if (isset($_GET['view']) && $_GET['view'])
    setcookie($_status . '_view', $view, time() + 3600 * 24 * 365);
if (isset($_GET['offer']) && !$_GET['offer'])
    setcookie($_status . '_offer', "");
if (isset($_GET['view']) && !$_GET['view'] || $_GET['view'] == "me")
    setcookie($_status . '_view', "");
$_group = getGroup($_user['group']);

// $_offers = getOffer();
$_temp = getOffer();
$_offers = array();
foreach ($_temp as $key => $value) {
    $_offers[$value['id']] = $value;
}

$sql = "";
if ($offer != "all")
    $sql = " and `offer`='" . escape_string($offer) . "' ";
if (isCaller() || isColler()) {
    if ($view == "group")
        $sql .= " and `group`='" . $_user['group'] . "' ";
    if ($view == "me")
        $sql .= " and `user_call`='" . $_user['id'] . "' ";
    else if ($user)
        $sql .= " and `user_call`='" . escape_string($user) . "' ";
} elseif (isPublisher()) {
    if ($view == "group")
        $sql .= " and `group`='" . $_user['group'] . "' ";
    if ($view == "me")
        $sql .= " and `ukey`='" . $_user['ukey'] . "' ";
    else
        $sql .= " and `ukey`='" . $_user['ukey'] . "' ";
} else {
    $memGroup = memberGroup($_group['id']);
    $user_ship = array();
    foreach ($memGroup as $us)
        $user_ship[] = $us['id'];
    if ($view == "group")
        $sql .= " and `user_ship` in ('" . implode("','", $user_ship) . "') ";
    if ($view == "me")
        $sql .= " and `user_ship`='" . $_user['id'] . "' ";
    else if ($user)
        $sql .= " and `user_ship`='" . escape_string($user) . "' ";
}
$perPage = 10;
if (isAller()) {
    $data_number = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') and `user_ship`!='' " . $_sql_offer . " " . $sql)->fetch_array();
    $_data = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') and `user_ship`!='' " . $_sql_offer . " " . $sql . "limit $perPage")->fetch_array();
} else {
    $data_number = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') " . $_sql_offer . " " . $sql)->fetch_array();
    $_data = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') " . $_sql_offer . " " . $sql . "limit $perPage")->fetch_array();
}
$_count = count($data_number);
?>
<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>
<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>
<section class="row">
    <div class="col-md-6">
        <h2 class="section-heading mb-4"><span class="badge <?= getBgOrder($_status); ?>">Shipping</span> Đang vận chuyển (<?= $_count; ?>)</h2>
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
                <input name="type" value="shipping" type="hidden">
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
                    <select name="pageSize" id="pageSize" class="custom_select">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                        <option value="700">700</option>
                        <option value="1000">1000</option>
                    </select>
                    <span class="custom_select_record">bản ghi</span>
                    <div class="clear-fix"></div>
                </div>
                <div class="col-md-9 text-right">
                    <?php if (isShipper()) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <button role="btn-makeApprove" class="btn btn-info waves-effect waves-light" data-toggle="modal" data-target="#makeApprove" disabled="">Make Approve<i class="fas fa-user-check ml-1"></i></button>
                                <button role="btn-makeShiperror" class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#makeShiperror" disabled="">Make ShipError<i class="fas fa-exclamation-circle ml-1"></i></button>
                                <button role="btn-importExcel" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#importExcel">Import Excel<i class="fas fa-file-import ml-1"></i></button>
                                <button role="btn-cancelAll" class="btn btn-danger waves-effect waves-light" disabled="">Cancel All Selected<i class="fas fa-times ml-1"></i></button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <br>
            </div>
            <input type="hidden" id="limit" name="limit" value="10">
            <input type="hidden" id="totalCount" name="limit" value="<?php echo $_count; ?>">
            <div class="postList" style="overflow-x:auto;margin-bottom:15px;">
                <table id="dtBasicExample" class="table table-sm  table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="text-left">
                                <?php if (isShipper()) { ?>
                                    <span class="form-check text-center">
                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                        <label class="form-check-label" for="checkAll"></label>
                                    </span>
                                <?php } ?>
                            </th>
                            <th class="text-center sort-heading table_desc" id="id-desc" data-toggle="tooltip" title="ID đơn hàng">#ID</th>
                            <th class="text-center sort-heading" data-toggle="tooltip" title="Số hiệu">Số hiệu</th>
                            <th class="text-center sort-heading" data-toggle="tooltip" title="Bưu điện">Bưu điện</th>
                            <th class="text-center" data-toggle="tooltip" title="Sản phẩm muốn mua">Offer</th>
                            <th class="text-center sort-heading table_desc" id="number-desc" data-toggle="tooltip" title="Số lượng mua">Number&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="text-center sort-heading table_desc" id="price_sell-desc" data-toggle="tooltip" title="Tổng tiền">Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="text-center sort-heading table_desc" id="time-desc" data-toggle="tooltip" title="Thời gian đặt hàng">Time</th>
                            <th class="text-center sort-heading table_asc" id="order_name-asc" data-toggle="tooltip" title="Tên người muốn mua">Order Name</th>
                            <th class="text-center" data-toggle="tooltip" title="Số điện thoại liên hệ">Order Phone</th>
                            <th class="text-left" data-toggle="tooltip" title="Ghi chú">Note</th>
                            <th class="text-left" data-toggle="tooltip" title="Thông tin Order">Order info</th>
                            <th class="text-left" data-toggle="tooltip" title="Địa chỉ mua hàng">Address</th>
                            <th class="text-left" data-toggle="tooltip" title="Tỉnh">Province</th>
                            <?php if (isShipper()  || isLeader() || isAller()) { ?>
                                <th class="text-center" data-toggle="tooltip" title="Người gọi">Caller</th>
                            <?php } ?>
                            <?php if (isCaller() || isColler()  || isLeader() || isAller()) { ?>
                                <th class="text-center" data-toggle="tooltip" title="Người nhận ship">Shipper</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="content">
                        <?php
                        if ($_data) {
                            $isCaller = isCaller() || isColler() ? true : false;
                            $isShipper = isShipper() ? true : false;
                            $sql_check = array();
                            foreach ($_data as $arr)
                                $sql_check[] = " ( `order_phone` like '" . $arr['order_phone'] . "' and `id`!='" . $arr['id'] . "') ";
                            $sql_check = $sql_check ? ' where ' . implode(" or ", $sql_check) : '';
                            $checkOrder = checkOrders($sql_check);
                            foreach ($_data as $arr) {
                                $check = isset($checkOrder[$arr['order_phone']]) ?  $checkOrder[$arr['order_phone']] : '';

                                $_offer_name = strlen($arr['offer_name'])>20 ? substr($arr['offer_name'],0,20).'...' : $arr['offer_name'];
                                $_offer_name = ($_offers[$arr['offer']]['offer_link']) ? '<a href="'.$_offers[$arr['offer']]['offer_link'].'" target="__blank">'.$_offer_name.'<i class="fas fa-external-link-alt ml-1 text-primary"></i></a>' : $_offer_name;

                                $caller = getUser($arr['user_call']);
                                $shipper = getUser($arr['user_ship']);
                                echo '<tr data-id="' . $arr['id'] . '">';
                                echo '<td class="text-center">';
                                if (isShipper())
                                    echo '<input type="checkbox" class="form-check-input" id="order_' . $arr['id'] . '" value="' . $arr['id'] . '">
                        <label class="form-check-label px-2" for="order_' . $arr['id'] . '"></label>';
                                echo '<a href="?route=editOrder&id=' . $arr['id'] . '">
                          <span class="btn btn-warning btn-sm waves-effect waves-light"><i class="w-fa fas fa-edit ml-1"></i></span>
                        </a>';
                                if (isShipper())
                                    echo '<span role="btn-cancelOrder" class="btn btn-danger btn-sm buttonDelete waves-effect waves-light" data-toggle="modal" data-target="#cancelOrder">
                          <i class="fas fa-times ml-1"></i>
                        </span>';
                                echo '</td>';
                                echo '<td class="text-center">Đơn hàng #<strong>' . $arr['id'] . '</strong></td>';
                                if (!empty($arr['parcel_code'])) {
                                    echo '<td class="text-center">' . $arr['parcel_code'] . '<a target="_blank" href="http://www.vnpost.vn/en-us/dinh-vi/buu-pham?key=' . $arr['parcel_code'] . '"><i class="fas fa-external-link-alt ml-1"></i></a></td>';
                                } else {
                                    echo '<td class="text-center"></td>';
                                }
                                echo '<td class="text-center"><b>' . $arr['post_office'] . '</b></td>';
                                echo '<td class="text-center"><b>' . $_offer_name . '</b></td>';
                                echo '<td class="text-center">x<b>' . _e($arr['number']) . '</b></td>';
                                echo '<td class="text-center"><b>' . addDotNumber($arr['price_sell']) . '</b>k</td>';
                                echo '<td class="text-center">' . date('Y/m/d H:i', $arr['time']) . '</td>';
                                echo '<td class="text-center">' . _ucwords($arr['order_name']) . '</td>';
                                echo '<td class="text-center">' . _e($arr['order_phone']);
                                if ($check) {
                                    foreach ($check as $dup) {
                                        $data_dup = $_db->query("select * from `core_orders` where `id` = '" . $dup['id'] . "' ")->fetch();
                                        $caller_dup = getUser($data_dup['user_call']);
                                        echo '<br><small>- ' . $caller_dup['name'] . ' #<a target="_blank" href="?route=editOrder&id=' . $dup['id'] . '"><b class="text-danger">' . $dup['id'] . ' (' . $dup['status'] . ')</b></a></small>';
                                    }
                                }
                                echo '</td>';
                                echo '<td class="text-left">' . nl2br(_ucwords($arr['note'])) . '</td>';
                                echo '<td class="text-left">' . nl2br(_ucwords($arr['order_info'])) . '</td>';
                                echo '<td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal' . $arr['id'] . '">
                      Chi tiết
                    </button>
                    <div class="modal fade" id="exampleModal' . $arr['id'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Địa chỉ mua hàng</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                           <p style="margin-bottom:0;"><strong>Địa chỉ:</strong> ' . _ucwords($arr['order_address']) . '</p>
                           <p style="margin-bottom:0;"><strong>Commune:</strong> ' . _ucwords($arr['order_commune']) . '</p>
                           <p style="margin-bottom:0;"><strong>District:</strong> ' . _ucwords($arr['order_district']) . '</p>
                           
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>                          
                        </div>
                      </div>
                    </div>
                  </div></td>';
                                echo '<td class="text-left"> ' . _ucwords($arr['order_province']) . '</td>';
                                if ($isShipper || isLeader() || isAller()) {
                                    echo '<td class="text-center">';
                                    if ($caller)
                                        echo '<div class="chip align-middle">
                            <a target="_blank" href="?route=statistics&user=' . $caller['id'] . '">
                              <img src="' . getAvatar($caller['id']) . '"> ' . (!isBanned($caller) ? _e($caller['name']) : '<strike class="text-dark"><b>' . _e($caller['name']) . '</b></strike>') . '
                            </a>
                          </div>';
                                    echo '</td>';
                                }
                                if ($isCaller  || isLeader() || isAller()) {
                                    echo '<td class="text-center">';
                                    if ($shipper)
                                        echo '<div class="chip align-middle">
                            <a target="_blank" href="?route=statistics&user=' . $shipper['id'] . '">
                              <img src="' . getAvatar($shipper['id']) . '"> ' . (!isBanned($shipper) ? _e($shipper['name']) : '<strike class="text-dark"><b>' . _e($shipper['name']) . '</b></strike>') . '
                            </a>
                          </div>';
                                    echo '</td>';
                                }
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
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
    </div>
</section>
<?php if (isShipper()) { ?>
    <!--Modal: modalConfirmDelete-->
    <div class="modal fade" id="cancelOrder" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
            <!--Content-->
            <div class="modal-content text-center">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center">
                    <p class="heading">Hủy đơn hàng</p>
                </div>
                <!--Body-->
                <div class="modal-body">
                    <i class="fas fa-times fa-4x animated rotateIn"></i>
                    <div data-id="">
                        Bạn thực sự muốn từ bỏ vận chuyển đơn hàng này?
                    </div>
                </div>
                <!--Footer-->
                <div class="modal-footer flex-center">
                    <button class="btn btn-outline-danger" type="submit" name="cancelOrder">Hủy đơn hàng</button>
                    <button class="btn btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: modalConfirmDelete-->
    <!--Modal: importExcel-->
    <div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-notify modal-warning" role="document">
            <!--Content-->
            <div class="modal-content text-left">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center">
                    <p class="heading">Nhập Excel</p>
                </div>
                <!--Body-->
                <div class="modal-body row">
                    <div class="col-md-12 input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputFile1">Upload</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputFile2" aria-describedby="inputFile1">
                            <label class="custom-file-label" for="inputFile2"><span>Choose file</span></label>
                        </div>
                        <div class="invalid-feedback">Vui lòng chọn một file Excel.</div>
                    </div>
                    <div class="col-md-12 md-form form-sm mb-0">
                        <select id="i-type" class="mdb-select md-form">
                            <option value="" selected disabled>Chọn một dịch vụ vận chuyển</option>
                            <option value="viettel">Viettel Post</option>
                            <option value="vnpost">VnPost</option>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn một dịch vụ.</div>
                    </div>
                    <div class="col-md-6 form-sm mb-0">
                        <select id="i-status" class="mdb-select md-form">
                            <option value="" selected disabled>Lọc trạng thái đơn</option>
                            <option value="approved">Giao thành công</option>
                            <option value="shiperror">Duyệt hoàn</option>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn một trạng thái.</div>
                    </div>
                    <div class="col-md-6 form-sm mb-0">
                        <select id="i-make" class="mdb-select md-form">
                            <option value="" selected disabled>Đánh dấu trạng thái</option>
                            <option value="approved">Approved</option>
                            <option value="shiperror">ShipError</option>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn một trạng thái.</div>
                    </div>
                </div>
                <!--Footer-->
                <div class="modal-footer flex-center">
                    <button class="btn btn-outline-warning" type="submit" name="submit">Áp dụng</button>
                    <button class="btn btn-warning waves-effect" data-dismiss="modal">Đóng</button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: importExcel-->
    <!--Modal: showExcel-->
    <div class="modal fade" id="showExcel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-warning modal-dialog-scrollable" role="document">
            <!--Content-->
            <div class="modal-content text-left">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center orange">
                    <h3 class="heading" id="headerShowExcel">Tìm thấy tất cả <b class="text-white">0</b> đơn hàng hợp lệ</h3>
                </div>
                <!--Body-->
                <div class="modal-body" id="bodyShowExcel"></div>
                <!--Footer-->
                <div class="modal-footer text-center">
                    <button class="btn btn-outline-warning" type="submit" name="submit" id="btn-showExcel">Đồng ý</button>
                    <button class="btn btn-warning waves-effect" data-dismiss="modal">Hủy bỏ</button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: showExcel-->
    <div class="modal fade" id="makeShiperror" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-notify modal-danger" role="document">
            <!--Content-->
            <div class="modal-content text-center">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center">
                    <p class="heading">Đánh dấu khách không nhận hàng</p>
                </div>
                <!--Body-->
                <div class="modal-body">
                    <i class="fas fa-times fa-4x animated rotateIn"></i>
                    <div data-id="">
                        Bạn thực sự muốn đánh dấu những đơn hàng này là khách không nhận hàng?
                    </div>
                </div>
                <!--Footer-->
                <div class="modal-footer flex-center">
                    <button class="btn btn-outline-danger" type="submit" name="cancelOrder">Đồng ý</button>
                    <button class="btn btn-danger waves-effect" data-dismiss="modal">Hủy bỏ</button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: makeShiperror-->
    <!--Modal: makeAprrove-->
    <div class="modal fade" id="makeApprove" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-notify modal-success" role="document">
            <!--Content-->
            <div class="modal-content text-center">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center">
                    <p class="heading">Đánh dấu giao hàng thành công</p>
                </div>
                <!--Body-->
                <div class="modal-body">
                    <i class="fas fa-check fa-4x animated rotateIn"></i>
                    <div data-id="">
                        Bạn thực sự muốn đánh dấu những đơn hàng này là đã giao thành công?
                    </div>
                </div>
                <!--Footer-->
                <div class="modal-footer flex-center">
                    <button class="btn btn-outline-success" type="submit">Đồng ý</button>
                    <button class="btn btn-success waves-effect" data-dismiss="modal">Hủy bỏ</button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: makeAprrove-->
<?php } ?>
<div class="loader-overlay">
    <div class="loader-content-container">
        <div class="loader-content">
            <div class="spinner-grow" role="status" style="width: 6rem; height: 6rem;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#inputFile2").on('change', function(e) {
            var $this = $(e.target),
                $label = $this.next('.custom-file-label'),
                $files = $this[0].files;
            var fileName = '';
            if ($files && $files.length > 1)
                fileName = ($this.attr('data-multiple-target') || '').replace('{count}', $files.length);
            else if (e.target.value)
                fileName = e.target.value.split('\\').pop();
            if (fileName) {
                $label.find('span').html(fileName);
            } else {
                $label.find('span').html($label.html());
            }
        });
        var $idImport;
        var $makeImport;
        $("#importExcel").on('click', '[type=submit]', function() {
            var type = $("#i-type"),
                status = $("#i-status"),
                make = $("#i-make"),
                file = $("#inputFile2").prop('files')[0];
            if (file)
                var fileExtension = file.name.substr(file.name.length - 4);
            type.parent().siblings(".invalid-feedback").hide();
            status.parent().siblings(".invalid-feedback").hide();
            make.parent().siblings(".invalid-feedback").hide();
            if (!file) {
                toastr.error('Vui lòng chọn một file Excel.');
            } else if (!fileExtension.includes("xls")) {
                toastr.error('Định dạng file không phải là Excel.');
            } else if (!type.val()) {
                type.parent().siblings(".invalid-feedback").show();
            } else if (!status.val()) {
                status.parent().siblings(".invalid-feedback").show();
            } else if (!make.val()) {
                make.parent().siblings(".invalid-feedback").show();
            } else {
                $(".loader-overlay").show();
                $makeImport = make.val();
                var data = new FormData();
                data.append('file', file);
                data.append('type', type.val());
                data.append('status', status.val());
                data.append('make', make.val());
                $.ajax({
                    url: '<?= $_url; ?>/ajax.php?act=excel&action=import',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    type: 'post',
                    success: function(response) {
                        $(".loader-overlay").hide();
                        if (response.status == 200) {
                            $('#importExcel').modal('hide');
                            $("#headerShowExcel").html('Tìm thấy tất cả <b class="text-white">' + response.data.length + '</b> đơn hàng hợp lệ');
                            $("#bodyShowExcel").html('');
                            $("#btn-showExcel").html('Make ' + response.make);
                            $idImport = [];
                            var e = 1;
                            for (var i in response.data) {
                                $idImport.push(response.data[i].id);
                                $("#bodyShowExcel").append('<p> - (' + e + ') Đơn hàng <b>#' + response.data[i].id + '</b> / <b>' + response.data[i].name + '</b> / <b class="text-success">' + response.data[i].status_text + '</b></p>');
                                e++;
                            }
                            $('#showExcel').modal('show');
                            toastr.success(response.message);
                        } else
                            toastr.error(response.message);
                    },
                    error: function(response) {
                        toastr.error('Could not connect to API!');
                        $(".loader-overlay").hide();
                    }
                });
            }
        });
        $("#showExcel").on('click', '[type=submit]', function() {
            $(".loader-overlay").show();
            $.ajax({
                url: '<?= $_url; ?>/ajax.php?act=actionMake',
                dataType: 'json',
                data: {
                    id: $idImport.join(","),
                    action: $makeImport
                },
                type: 'post',
                success: function(response) {
                    $(".loader-overlay").hide();
                    if (response.status == 200) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.href = '?route=order&type=' + $makeImport;
                        }, 500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    $(".loader-overlay").hide();
                    toastr.error('Could not connect to API!');
                }
            });
        });
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
                url: '<?= $_url; ?>/ajax.php?act=pagination-orderShipping&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>',
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
        loadOrderBy('<?= $_url; ?>/ajax.php?act=orderby-orderShipping&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>');
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
            url: '<?= $_url; ?>/ajax.php?act=pagination-orderShipping&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>',
            success: function(html) {
                $("#content").html(html);
                $('td input[type=checkbox]').on("change", function() {
                    var checked = $('td input[type=checkbox]:checked');
                    if (checked.length > 0) {
                        $("[role=btn-cancelAll]").prop('disabled', false);
                        $("[role=btn-makeApprove]").prop('disabled', false);
                        $("[role=btn-makeShiperror]").prop('disabled', false);
                    } else {
                        $("[role=btn-cancelAll]").prop('disabled', true);
                        $("[role=btn-makeApprove]").prop('disabled', true);
                        $("[role=btn-makeShiperror]").prop('disabled', true);
                    }
                });
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
<?php if (isShipper()) { ?>
    <script type="text/javascript">
        var $idOrder;
        $('#checkAll').on("change", function() {
            var checkboxes = $('td input[type=checkbox]');
            if ($(this).prop('checked')) {
                checkboxes.prop('checked', true);
            } else {
                checkboxes.prop('checked', false);
            }
            var checked = $('td input[type=checkbox]:checked');
            if (checked.length > 0) {
                $("[role=btn-cancelAll]").prop('disabled', false);
                $("[role=btn-makeApprove]").prop('disabled', false);
                $("[role=btn-makeShiperror]").prop('disabled', false);
            } else {
                $("[role=btn-cancelAll]").prop('disabled', true);
                $("[role=btn-makeApprove]").prop('disabled', true);
                $("[role=btn-makeShiperror]").prop('disabled', true);
            }
        });
        $('td input[type=checkbox]').on("change", function() {
            var checked = $('td input[type=checkbox]:checked');
            if (checked.length > 0) {
                $("[role=btn-cancelAll]").prop('disabled', false);
                $("[role=btn-makeApprove]").prop('disabled', false);
                $("[role=btn-makeShiperror]").prop('disabled', false);
            } else {
                $("[role=btn-cancelAll]").prop('disabled', true);
                $("[role=btn-makeApprove]").prop('disabled', true);
                $("[role=btn-makeShiperror]").prop('disabled', true);
            }
        });
        $("#makeApprove").on('click', '[type=submit]', function() {
            $idOrder = [];
            $("td input[type=checkbox]:checked").each(function() {
                $idOrder.push(this.value);
            });
            $(".loader-overlay").show();
            $.ajax({
                url: '<?= $_url; ?>/ajax.php?act=actionMake',
                dataType: 'json',
                data: {
                    id: $idOrder.join(","),
                    action: "approved"
                },
                type: 'post',
                success: function(response) {
                    $(".loader-overlay").hide();
                    if (response.status == 200) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    $(".loader-overlay").hide();
                    toastr.error('Could not connect to API!');
                }
            });
        });
        $("#makeShiperror").on('click', '[type=submit]', function() {
            $idOrder = [];
            $("td input[type=checkbox]:checked").each(function() {
                $idOrder.push(this.value);
            });
            $(".loader-overlay").show();
            $.ajax({
                url: '<?= $_url; ?>/ajax.php?act=actionMake',
                dataType: 'json',
                data: {
                    id: $idOrder.join(","),
                    action: "shiperror"
                },
                type: 'post',
                success: function(response) {
                    $(".loader-overlay").hide();
                    if (response.status == 200) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    $(".loader-overlay").hide();
                    toastr.error('Could not connect to API!');
                }
            });
        });
        $("body").on('click', '[role=btn-cancelAll]', function() {
            $idOrder = [];
            $("td input[type=checkbox]:checked").each(function() {
                $idOrder.push(this.value);
            });
            $(".loader-overlay").show();
            $.ajax({
                url: '<?= $_url; ?>/ajax.php?act=cancelOrderPending',
                dataType: 'json',
                data: {
                    id: $idOrder.join(",")
                },
                type: 'post',
                success: function(response) {
                    $(".loader-overlay").hide();
                    if (response.status == 200) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        setTimeout(function() {
                            //location.reload();
                        }, 500);
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    $(".loader-overlay").hide();
                    toastr.error('Could not connect to API!');
                }
            });
        });
        $("body").on('click', '[role=btn-cancelOrder]', function() {
            var $tr = $(this).parents('tr');
            $idOrder = [$tr.attr("data-id")];
        });
        $("#cancelOrder").on("click", "[name=cancelOrder]", function() {
            $(".loader-overlay").show();
            $.ajax({
                url: '<?= $_url; ?>/ajax.php?act=cancelOrderPending',
                dataType: 'json',
                data: {
                    id: $idOrder.join(",")
                },
                type: 'post',
                success: function(response) {
                    $(".loader-overlay").hide();
                    if (response.status == 200) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        setTimeout(function() {
                            //location.reload();
                        }, 500);
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    $(".loader-overlay").hide();
                    toastr.error('Could not connect to API!');
                }
            });
        });
    </script>
<?php } ?>