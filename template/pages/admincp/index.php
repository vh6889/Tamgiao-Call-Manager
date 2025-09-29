<?php echo "Forbidden"; ?>
<?php
$_postback = $_db->query("SELECT * FROM `core_s2s_postback` WHERE `created` BETWEEN '2022-08-24 00:00:00' AND '2022-08-24 23:59:59' ORDER BY `id` ASC")->fetch_array();
$_orders = $_db->query("SELECT id,status,time,date,landing FROM `core_orders` WHERE `date`='2022/08/24' ORDER BY `id` ASC")->fetch_array();
// $tempTime = array(1661329669,1661328086,1661323931,1661323886,1661312335,1661310674,1661307418,1661299716,1661293998,1661272561,1661271659,1661271098,1661266450,1661262682,1661262391,1661249376,1661249201,1661248311,1661244615,1661241286,1661238797);
// foreach ($tempTime as $key => $value) {
//     echo date('m/d/Y H:i:s', $value).'</br>';
// }
?>
<div class="row">
    <div class="col-md-6">
        <h4>Order</h4>
        <table class="table">
            <th>
                <tr>
                    <td>ID</td>
                    <td>Status</td>
                    <td>Time</td>
                    <td>Landing</td>
                </tr>
            </th>
            <?php foreach ($_orders as $key => $value) { ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['status'] ?></td>
                    <td><?= date('m/d/Y H:i:s', $value['time']) ?></td>
                    <!-- <td><?= $value['date'] ?></td> -->
                    <td><?= $value['landing'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-md-6">
        <h4>Postback</h4>
        <table class="table">
            <th>
                <tr>
                    <td>ID</td>
                    <td>Landing</td>
                    <td>State</td>
                    <td>Time</td>
                </tr>
            </th>
            <?php foreach ($_postback as $key => $value) { ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['landing_page'] ?></td>
                    <td><?= $value['state'] ?></td>
                    <td><?= $value['created'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>