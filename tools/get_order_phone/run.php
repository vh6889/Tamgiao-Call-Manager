<?php

$rootPath = dirname(__DIR__, 2);

include $rootPath . '/includes/config.php';
$phoneFile = __DIR__ . '/phones.txt';

$limit = 1000;
$page  = 1;
if ($_POST) {
    $query = "SELECT `order_phone` FROM `core_orders` WHERE 1=1";
    if (isset($_POST['offer'])) {
        $offers = implode(",", $_POST['offer']);
        $query .= " AND `offer` IN ($offers)";
    }
    if (isset($_POST['status'])) {
        foreach ($_POST['status'] as $value) {
            $status .= ",'" . $value . "'";
        }
        $status = trim($status, ',');
        $query .= " AND `status` IN ($status)";
    }
    $query .= " GROUP BY `order_phone`";
    $phones = $_db->query($query)->fetch_array();

    $blist = [];
    if($_POST['escapeBlacklist']) {
        $blacklist = $_db->query("SELECT `phone_number` FROM `core_backlists` GROUP BY `phone_number`")->fetch_array();
        foreach ($blacklist as $phone) {
            $blist[] = $phone['phone_number'];
        }
    }

    $counter = 0;
    $fp = fopen($phoneFile, 'w') or die("Can't create file");
    foreach ($phones as $phone) {
        if (!in_array($phone['order_phone'], $blist)) {
            fwrite($fp, $phone['order_phone'] . "\n");
            $counter++;
        }
    }
    fclose($fp);

    echo "Export $counter phones finish</br>";
    echo "<a href='phones.txt'>download this click here</a>";
} else {
    $offers = $_db->query("SELECT * FROM `core_offers`")->fetch_array();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </head>

    <body>
        <div class="container">
            <form method="post">
                <h5>Lấy số điện thoại theo:</h5>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="offer[]">Offer</label>
                        <select name="offer[]" class="form-control" style="height:260px" multiple>
                            <?php foreach ($offers as $value) { ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status[]">Trạng thái đơn hàng</label>
                        <select name="status[]" class="form-control" style="height:260px" multiple>
                            <option value="uncheck">Uncheck</option>
                            <option value="calling">Calling</option>
                            <option value="pending">Pending</option>
                            <option value="callerror">Call Error</option>
                            <option value="rejected">Rejected</option>
                            <option value="trash">Trash</option>
                            <option value="shipping">Shipping</option>
                            <option value="shipdelay">Ship Delay</option>
                            <option value="shiperror">Ship Error</option>
                            <option value="shipfail">Ship Fail</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12 form-check">
                        <input type="checkbox" class="form-check-input" name="escapeBlacklist" id="escapeBlacklist" checked>
                        <label class="form-check-label" for="escapeBlacklist">Bỏ qua black list</label>
                    </div>
                    <div class="form-group col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Get phone number</button>
                    </div>
                </div>
            </form>
        </div>
    </body>

    </html>
<?php } ?>