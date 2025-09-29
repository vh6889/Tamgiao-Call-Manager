<?php
$_status = 'pending';
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status . '_offer']) ? $_COOKIE[$_status . '_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y', strtotime('-29 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y', time());
$view = isset($_GET['view']) && in_array($_GET['view'], ['me', 'all', 'group']) ? $_GET['view'] : (isset($_COOKIE[$_status . '_view']) ? $_COOKIE[$_status . '_view'] : (isAller() ? 'all' : 'group'));
$showall = isset($_GET['showall']) ? $_GET['showall'] : false;
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
    $_count = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') " . ($showall == false && !$user ? " and `user_ship`!='' " : "") . " " . $_sql_offer . " " . $sql)->num_rows();
    $_data = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') " . ($showall == false && !$user ? " and `user_ship`!='' " : "") . " " . $_sql_offer . " " . $sql . " order by id DESC LIMIT $perPage")->fetch_array();
} else {
    $_count = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') " . $_sql_offer . " " . $sql)->num_rows();
    $_data = $_db->query("select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') " . $_sql_offer . " " . $sql . " order by id DESC LIMIT $perPage")->fetch_array();
    // echo "select * from `core_orders` where `status` = '" . $_status . "' and (`time` >= '" . (strtotime(str_replace("/", "-", $ts) . " GMT+7 00:00")) . "' and `time` < '" . (strtotime(str_replace("/", "-", $te) . " GMT+7 23:59")) . "') " . $_sql_offer . " " . $sql . " order by id DESC LIMIT $perPage";
}
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
        <h2 class="section-heading mb-4"><span class="badge <?= getBgOrder($_status); ?>">Pending</span> Đơn hàng chuẩn bị giao (<?= $_count; ?>)</h2>
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
                <input name="type" value="pending" type="hidden">
                <input name="ts" value="" type="hidden">
                <input name="te" value="" type="hidden">
                <?php if ($user) { ?>
                    <input name="user" value="<?= _e($user); ?>" type="hidden">
                <?php } ?>
                <?php if ($showall == true) { ?>
                    <input name="showall" value="true" type="hidden">
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
                                    <option value="group" <?= ($view == "group" ? 'selected' : ''); ?>>Group Members
                                    </option>
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
                <div class="col-md-7" style="margin-bottom:15px;">
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
                <div class="col-md-5" style="margin-bottom:15px;">
                    <?php if (isShipper()) { ?>
                        <div class="row">
                            <div class="col-md-4 text-right">
                                <button role="btn-makeShipping" class="btn btn-info waves-effect waves-light" data-toggle="modal" data-target="#makeShipping" disabled="">Make Shipping<i class="fas fa-shipping-fast ml-1"></i></button>
                            </div>
                            <div class="col-md-8 text-right pb-2">
                                <button role="btn-exportExcel" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#exportExcel" disabled="">Export Excel<i class="fas fa-file-export ml-1"></i></button>
                                <button role="btn-cancelAll" class="btn btn-danger waves-effect waves-light" disabled="">Cancel All Selected<i class="fas fa-times ml-1"></i></button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
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
                            <th class="text-center sort-heading table_asc" id="id-asc" data-toggle="tooltip" title="ID đơn hàng">#ID</th>
                            <th class="text-center" data-toggle="tooltip" title="Sản phẩm muốn mua">Offer</th>
                            <th class="text-center sort-heading table_desc" data-toggle="tooltip" id="number-desc" title="Số lượng mua">Number&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="text-center sort-heading table_desc" id="price_sell-desc" data-toggle="tooltip" title="Tổng tiền">Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="text-center sort-heading table_asc" data-toggle="tooltip" id="time-asc" title="Thời gian đặt hàng">Time</th>
                            <th class="text-center sort-heading table_asc" id="order_name-desc" data-toggle="tooltip" title="Tên người muốn mua">Order Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="text-center" data-toggle="tooltip" title="Số điện thoại liên hệ">Order Phone</th>
                            <th class="text-left" data-toggle="tooltip" title="Ghi chú">Note</th>
                            <th class="text-left" data-toggle="tooltip" title="Địa chỉ mua hàng">Address</th>

                            <th class="text-left" data-toggle="tooltip" title="Tỉnh/Thành phố">Province</th>
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
                                echo '<td class="text-center">Đơn hàng #<b>' . $arr['id'] . '</b></td>';
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
                            <p><strong>Địa chỉ:</strong>&nbsp;' . _ucwords($arr['order_address']) . '</p>
                            <p><strong>Commune:</strong>&nbsp;' . _ucwords($arr['order_commune']) . '</p>
                            <p><strong>District:</strong>&nbsp;' . _ucwords($arr['order_district']) . '</p>                            
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Close</button>                                            
                    </div>
                  </div>
                </div>
              </div></td>';
                                echo '<td class="text-left">' . _ucwords($arr['order_province']) . '</td>';
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
    <!--Modal: exportExcel-->
    <div class="modal fade" id="exportExcel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-notify modal-warning" role="document">
            <!--Content-->
            <div class="modal-content text-left">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center">
                    <h1 class="heading text-dark">Xuất Excel (<b id="countSelect" class="text-danger">0</b>)</h1>
                </div>
                <!--Body-->
                <div class="modal-body">
                    <div class="form-sm mb-0">
                        <select id="i-type" class="mdb-select">
                            <option value="" selected disabled>Chọn một dịch vụ vận chuyển</option>
                            <option value="viettel">ViettelPost</option>
                            <option value="vnpost">VnPost</option>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn một dịch vụ.</div>
                    </div>
                    <div class="data-input input-viettel row" style="display: none;">
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i-name" type="text" class="form-control form-control-sm">
                                <label for="i-name" data-toggle="tooltip" title="Tên sản phẩm">Tên hàng hóa</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i-mass" type="text" class="form-control form-control-sm">
                                <label for="i-mass" data-toggle="tooltip" title="Khối lượng hàng hóa">Trọng lượng</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i-service" type="text" class="form-control form-control-sm" value="VCN">
                                <label for="i-service" data-toggle="tooltip" title="Loại hình chuyển phát">Loại hình chuyển
                                    phát</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i-payer" type="text" class="form-control form-control-sm" value="Người gửi trả">
                                <label for="i-payer" data-toggle="tooltip" title="Người trả tiền">Người trả tiền</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i-note" type="text" class="form-control form-control-sm" value="Cho xem hàng">
                                <label for="i-note" data-toggle="tooltip" title="Ghi chú">Ghi chú</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i-time" type="text" class="form-control form-control-sm" value="Cả ngày">
                                <label for="i-time" data-toggle="tooltip" title="Khối lượng hàng hóa">Thời gian giao</label>
                            </div>
                        </div>
                    </div>
                    <div class="data-input input-vnpost row" style="display: none;">
                        <div class="col-md-4">
                            <div class="md-form form-sm mb-0">
                                <select id="i2-service" class="select-wrapper mdb-select">
                                    <option value="1 - Chuyển phát nhanh - EMS (1)">1 - Chuyển phát nhanh - EMS (1)</option>
                                    <option value="2 - Chuyển phát thường (2)">2 - Chuyển phát thường (2)</option>
                                    <option value="3 - ECOD (3)">3 - ECOD (3)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="md-form form-sm mb-0">
                                <select id="i2-view" class="select-wrapper mdb-select">
                                    <option value="X">Cho xem hàng</option>
                                    <option value="">Không cho xem hàng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="md-form form-sm mb-0">
                                <select id="i2-collect" class="select-wrapper mdb-select">
                                    <option value="Thu gom tận nơi (1)">Thu gom tận nơi (1)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i2-name" type="text" class="form-control form-control-sm">
                                <label for="i2-name" data-toggle="tooltip" title="Tên sản phẩm">Nội dung</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm mb-0">
                                <input id="i2-mass" type="text" class="form-control form-control-sm">
                                <label for="i2-mass" data-toggle="tooltip" title="Khối lượng hàng hóa">Trọng lượng</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="md-form form-sm mb-0">
                                <input id="i2-note" type="text" class="form-control form-control-sm">
                                <label for="i2-note" data-toggle="tooltip" title="Chỉ dẫn giao hàng">Chỉ dẫn phát</label>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-sm mb-0">
                                <label>Dịch vụ hóa đơn</label>
                                <select id="i2-bill" class="select-wrapper mdb-select">
                                    <option value="">Không</option>
                                    <option value="X">Có</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-sm mb-0">
                                <label>Dịch vụ báo phát</label>
                                <select id="i2-ar" class="select-wrapper mdb-select">
                                    <option value="">Không</option>
                                    <option value="X">Có</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="form-sm mb-0">
                                <label>Cộng thêm cước vào tiền thu hộ</label>
                                <select id="i2-plus" class="select-wrapper mdb-select">
                                    <option value="">Không</option>
                                    <option value="X">Có</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Footer-->
                <div class="modal-footer flex-center">
                    <button class="btn btn-outline-warning" type="submit" name="submit">Xuất Excel</button>
                    <button class="btn btn-warning waves-effect" data-dismiss="modal">Đóng</button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: exportExcel-->
    <!--Modal: makeShipping-->
    <div class="modal fade" id="makeShipping" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-notify modal-success" role="document">
            <!--Content-->
            <div class="modal-content text-center">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center">
                    <p class="heading">Đánh dấu đang vận chuyển</p>
                </div>
                <!--Body-->
                <div class="modal-body">
                    <i class="fas fa-check fa-4x animated rotateIn"></i>
                    <div data-id="">
                        Bạn thực sự muốn đánh dấu những đơn hàng này là đang vận chuyển?
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
    <!--Modal: makeShipping-->
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
                url: '<?= $_url; ?>/ajax.php?act=pagination-orderPending&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>',
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
        loadOrderBy(
            '<?= $_url; ?>/ajax.php?act=orderby-orderPending&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>'
        );
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
            url: '<?= $_url; ?>/ajax.php?act=pagination-orderPending&ts=<?php echo $ts; ?>&te=<?php echo $te; ?>&offer=<?php echo $offer; ?>&view=<?php echo $view; ?>',
            success: function(html) {
                $("#content").html(html);
                $('td input[type=checkbox]').on("change", function() {
                    var checked = $('td input[type=checkbox]:checked');
                    if (checked.length > 0) {
                        $("[role=btn-exportExcel]").prop('disabled', false);
                        $("[role=btn-cancelAll]").prop('disabled', false);
                        $("[role=btn-makeShipping]").prop('disabled', false);
                        $("#action-make").prop('disabled', true);
                    } else {
                        $("[role=btn-exportExcel]").prop('disabled', true);
                        $("[role=btn-cancelAll]").prop('disabled', true);
                        $("[role=btn-makeShipping]").prop('disabled', true);
                        $("#action-make").prop('disabled', true);
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
                'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
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
            $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY') + ' - ' +
                moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days').format(
                'DD/MM/YYYY'));
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
            $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY') + ' - ' +
                moment().format('DD/MM/YYYY'));
            $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(29, 'days').format(
                'DD/MM/YYYY'));
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
                $("[role=btn-exportExcel]").prop('disabled', false);
                $("[role=btn-cancelAll]").prop('disabled', false);
                $("[role=btn-makeShipping]").prop('disabled', false);
                $("#action-make").prop('disabled', false);
            } else {
                $("[role=btn-exportExcel]").prop('disabled', true);
                $("[role=btn-cancelAll]").prop('disabled', true);
                $("[role=btn-makeShipping]").prop('disabled', true);
                $("#action-make").prop('disabled', true);
            }
        });
        $('td input[type=checkbox]').on("change", function() {
            var checked = $('td input[type=checkbox]:checked');
            if (checked.length > 0) {
                $("[role=btn-exportExcel]").prop('disabled', false);
                $("[role=btn-cancelAll]").prop('disabled', false);
                $("[role=btn-makeShipping]").prop('disabled', false);
                $("#action-make").prop('disabled', true);
            } else {
                $("[role=btn-exportExcel]").prop('disabled', true);
                $("[role=btn-cancelAll]").prop('disabled', true);
                $("[role=btn-makeShipping]").prop('disabled', true);
                $("#action-make").prop('disabled', true);
            }
        });
        $("#makeShipping").on('click', '[type=submit]', function() {
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
                    action: "shipping"
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
        $("body").on('click', '[role=btn-exportExcel]', function() {
            $idOrder = [];
            $("td input[type=checkbox]:checked").each(function() {
                $idOrder.push(this.value);
            });
            $("#countSelect").html($idOrder.length);

        });
        $("#exportExcel").on('change', '#i-type', function() {
            $(".data-input").hide();
            $(".input-" + $(this).val()).show();
        });
        $("#exportExcel").on('click', '[type=submit]', function() {
            var type = $("#i-type").val();
            if (type == "viettel")
                export_viettel();
            else if (type == "vnpost")
                export_vnpost();

            function export_viettel() {
                var type = $("#i-type"),
                    name = $("#i-name"),
                    mass = $("#i-mass"),
                    service = $("#i-service"),
                    payer = $("#i-payer"),
                    note = $("#i-note"),
                    time = $("#i-time");
                type.parent().siblings(".invalid-feedback").hide();
                if (!type.val()) {
                    type.parent().siblings(".invalid-feedback").show();
                } else {
                    $(".loader-overlay").show();
                    var xhr = new XMLHttpRequest();
                    var data = new FormData();
                    data.append('id', $idOrder.join(","));
                    data.append('type', type.val());
                    data.append('name', name.val());
                    data.append('mass', mass.val());
                    data.append('service', service.val());
                    data.append('payer', payer.val());
                    data.append('note', note.val());
                    data.append('time', time.val());
                    xhr.open('POST', '<?= $_url; ?>/ajax.php?act=excel&action=export', true);
                    xhr.responseType = 'blob';
                    xhr.onload = function(e) {
                        if (this.status == 200) {
                            var blob = new Blob([this.response], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });
                            var downloadUrl = URL.createObjectURL(blob);
                            var a = document.createElement("a");
                            a.href = downloadUrl;
                            <?php
                            if ($offer == 1) {
                            ?>
                                a.download = "Horusvn_<?= date('d-m-Y', time()); ?>_" + $idOrder.length + ".xlsx";
                            <?php
                            } elseif ($offer == 2) {
                            ?>
                                a.download = "LionMan_VN_<?= date('d-m-Y', time()); ?>_" + $idOrder.length + ".xlsx";
                            <?php
                            } else {
                            ?>
                                a.download = "export_<?= date('d-m-Y', time()); ?>_" + $idOrder.length + ".xlsx";
                            <?php
                            }
                            ?>
                            document.body.appendChild(a);
                            a.click();
                            toastr.success('Export Excel thành công.');
                        } else {
                            toastr.error('Export Excel thất bại.');
                        }
                        $(".loader-overlay").hide();
                    };
                    xhr.send(data);

                }
            }

            function export_vnpost() {
                var type = $("#i-type"),
                    name = $("#i2-name"),
                    mass = $("#i2-mass"),
                    service = $("#i2-service"),
                    view = $("#i2-view"),
                    collect = $("#i2-collect"),
                    note = $("#i2-note"),
                    bill = $("#i2-bill"),
                    ar = $("#i2-ar"),
                    plus = $("#i2-plus");
                type.parent().siblings(".invalid-feedback").hide();
                if (!type.val()) {
                    type.parent().siblings(".invalid-feedback").show();
                } else {
                    $(".loader-overlay").show();
                    var xhr = new XMLHttpRequest();
                    var data = new FormData();
                    data.append('id', $idOrder.join(","));
                    data.append('type', type.val());
                    data.append('name', name.val());
                    data.append('mass', mass.val());
                    data.append('service', service.val());
                    data.append('view', view.val());
                    data.append('collect', collect.val());
                    data.append('note', note.val());
                    data.append('bill', bill.val());
                    data.append('ar', ar.val());
                    data.append('plus', plus.val());
                    xhr.open('POST', '<?= $_url; ?>/ajax.php?act=excel&action=export', true);
                    xhr.responseType = 'blob';
                    xhr.onload = function(e) {
                        if (this.status == 200) {
                            var blob = new Blob([this.response], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });
                            var downloadUrl = URL.createObjectURL(blob);
                            var a = document.createElement("a");
                            a.href = downloadUrl;
                            a.download = "Horusvn_<?= date('d-m-Y', time()); ?>_" + $idOrder.length + ".xlsx";
                            document.body.appendChild(a);
                            a.click();
                            toastr.success('Export Excel thành công.');
                        } else {
                            toastr.error('Export Excel thất bại.');
                        }
                        $(".loader-overlay").hide();
                    };
                    xhr.send(data);

                }
            }
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