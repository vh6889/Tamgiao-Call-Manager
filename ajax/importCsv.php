<?php
$_action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
$_type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$result = '';

switch ($_action) {
    case "import":
        switch ($_type) {
            case 'order':
                if (!empty($_FILES["csv_file"]) || !$_POST['offer']) {
                    $_import_status = 'uncheck';
                    $_import_time = time();
                    $_import_date = date('Y/m/d',time());
                    $_offer = getOffer($_POST['offer']);
                    $_price = explode('|',$_offer['price']);
                    $_orders = [];

                    /* Read Csv File Upload */
                    $csv_file = fopen($_FILES['csv_file']['tmp_name'], "r");
                    while (($row = fgetcsv($csv_file, 1000, ",")) !== FALSE) {
                        $_orders[] = $row;
                    }
                    fclose($csv_file);
                    if(!count($_orders)) {
                        $result = '<label class="text-danger">Empty data!</label>';
                        break;
                    }

                    // build insert query
                    $_counter = 0;
                    $_insert_str = '';
                    foreach ($_orders as $order) {
                        if(!count($order)) continue;
                        $_phone    = escape_string($order[0]);
                        $_name   = escape_string($order[1]);
                        $_address = escape_string($order[2]) ?? '';
                        $_note    = escape_string($order[3]) ?? '';
                        $_landing = escape_string($order[4]) ?? '';
                        $_ukey    = escape_string($order[5]) ?? '';

                        $_insert_str .= $_insert_str!='' ? ',' : '';
                        $_insert_str .= "(".
                                            $_offer['id'].",'".
                                            $_offer['name']."',".
                                            $_price[0].",".
                                            $_offer['price_deduct'].",'".
                                            $_import_status."',".
                                            $_import_time.",'".
                                            $_import_date."','".
                                            $_name."','".
                                            $_phone."','".
                                            $_address."','".
                                            $_note."','".
                                            $_landing."','".
                                            $_ukey.
                                        "')";
                        $_counter++;
                    }

                    /* Import Orders to database */
                    if($_db->query("INSERT INTO `core_orders` (`offer`, `offer_name`,`price`, `price_deduct`,`status`, `time`, `date`, `order_name`, `order_phone`, `order_address`, `note`,`landing`, `ukey`) VALUES ".$_insert_str)) {
                        $result = '<label class="text-success">Đã import thành công '.$_counter.' đơn hàng!</label>';
                        echo $result;
                        exit();
                    }else {
                        $result = '<label class="text-danger">Có lỗi xảy ra vui lòng thử lại sau ít phút</label>';
                    }

                } else {
                    $result = '<label class="text-danger">Invalid File</label>';
                }
                break;

            default:
                $result = '<label class="text-danger">Hành động không tồn tại!</label>';
                break;
        }
        break;

    default:
        $result = '<label class="text-danger">Hành động không tồn tại!</label>';
        break;

echo $result;
}
