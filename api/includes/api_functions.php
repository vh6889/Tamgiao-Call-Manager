<?php

/**
 * Footec new API Functions
 * Author: caohaininh@gmail.com
 * Verion: 1.0
 * Since: Nov,2020
 */


function escape_string($text)
{
    global $_db;
    return $_db->real_escape_string($text);
}

/**
 * db_get_data({table},{conditions},{fields},{limit});
 * 
 * {table}      : String.
 * {fields}     : String.
 * {limit}      : Int.
 * {conditions} : Array({Column} => {Value}) or Array(array({Column}, {Operator}, {Value}))
 * 
 * Result data  : Record | records.
 * Result type  : Array.
 */
function db_get_data($table, $conditions = array(), $fields = '*', $limit = null)
{
    global $_db;

    $query = 'select ' . escape_string($fields) . ' from `' . escape_string($table) . '`';

    if (count($conditions)) {
        $_con_string = '';
        foreach ($conditions as $col => $val) {
            $_con_string    .=  ($_con_string == '') ? ' where ' : ' and ';

            $_column        =   (is_array($val)) ? escape_string($val[0]) : escape_string($col);
            $_operator      =   (is_array($val)) ? escape_string($val[1]) : '=';
            $_value         =   (is_array($val)) ? escape_string($val[2]) : escape_string($val);

            $_con_string    .=  "`" . $_column . "`" . $_operator . "'" . $_value . "'";
        }
        $query .= $_con_string;
    }

    if ($limit != null && is_int($limit))
        $query .= ' limit ' . $limit;

    $result = ($limit == 1) ? $_db->query($query)->fetch() : $_db->query($query)->fetch_array();
    return $result;
}

/**
 * db_insert_data({table},{data});
 * 
 * {table}  : String.
 * {data}   : Array({Column1} => {Value1},{Column2} => {Value2}).
 * 
 * Result data  : true | false.
 * Result type  : boolean.
 */
function db_insert_data($table, $data = array(),$debug=false)
{
    global $_db;

    $query = 'insert into `' . escape_string($table) . '` set';
    if (count($data)) {
        $_con_string = '';
        foreach ($data as $col => $val) {
            $_con_string .= ($_con_string == '') ? '' : ',';
            $_con_string .= "`" . escape_string($col) . "`='" . escape_string($val) . "'";
        }
        $query .= $_con_string;
    }
    if($debug)
        return $query;
    $result = $_db->exec_query($query);
    return $result;
}

/**
 * db_update_data({table},{data},{conditions});
 * 
 * {table}      : String.
 * {data}       : Array({Column1} => {Value1},{Column2} => {Value2}).
 * {conditions} : Array({Column} => {Value}) or Array(array({Column}, {Operator}, {Value}))
 * 
 * Result data  : true | false.
 * Result type  : boolean.
 */
function db_update_data($table, $data = array(), $conditions = null)
{
    global $_db;

    $query = 'update `' . escape_string($table) . '` set';
    if (count($data)) {
        $_con_string = '';
        foreach ($data as $col => $val) {
            $_con_string .= ($_con_string == '') ? '' : ',';
            $_con_string .= "`" . escape_string($col) . "`='" . escape_string($val) . "'";
        }
        $query .= $_con_string;
    }
    if (is_array($conditions)) {
        $_con_string = '';
        foreach ($conditions as $col => $val) {
            $_con_string    .=  ($_con_string == '') ? ' where ' : ' and ';

            $_column        =   (is_array($val)) ? escape_string($val[0]) : escape_string($col);
            $_operator      =   (is_array($val)) ? escape_string($val[1]) : '=';
            $_value         =   (is_array($val)) ? escape_string($val[2]) : escape_string($val);

            $_con_string    .=  "`" . $_column . "` " . $_operator . " '" . $_value . "'";
        }
        $query .= $_con_string;
    } else {
        $query .= ($conditions != '') ? ' where ' . $conditions : '';
    }
    $result = $_db->exec_query($query);
    return $result;
}

/**
 * This function increament number of visit landing page.
 * 
 * Result data  : landing page data.
 * Result type  : Array | false.
 */
function landing_visit_today_counter($key, $landing, $tracking_token, $ukey = null)
{
    $_landing_offer = db_get_data(
        'core_offers',
        array(
            'key' => $key,
            'status' => 'run',
            'tracking_token' => $tracking_token,
        ),
        'id',
        1
    );
    if (!$_landing_offer['id'])
        return false;

    $today = date('d-m-Y', time());
    $landing_stats = db_get_data(
        'core_landing_stats',
        array(
            'date'      => $today,
            'landing'   => $landing,
            'offer'     => $_landing_offer['id'],
            'ukey' => $ukey
        ),
        '*',
        1
    );

    if ($landing_stats['id']) {
        $query = db_update_data(
            'core_landing_stats',
            array('viewPage' => $landing_stats['viewPage'] + 1),
            array(
                'date'      => $today,
                'landing'   => $landing,
                'offer'     => $_landing_offer['id'],
                'ukey'      => $ukey,
            )
        );
        if ($query)
            return $landing_stats;
    } else {
        $query = db_insert_data(
            'core_landing_stats',
            array(
                'offer'     => $_landing_offer['id'],
                'landing'   => $landing,
                'date'      => $today,
                'viewPage'  => 1,
                'order'     => 0,
                'ukey'      => $ukey,
            )
        );
        if ($query)
            return db_get_data(
                'core_landing_stats',
                array(
                    'date'      => $today,
                    'landing'   => $landing,
                    'offer'     => $_landing_offer['id'],
                    'ukey'      => $ukey,
                ),
                '*',
                1
            );
    }
    return false;
}

function check_phone($phone = "")
{
    $phone = str_replace(array(',', '.', '=', '-', '='), '', $phone);
    $phone = preg_replace("#^84([0-9]+)#si", "0$1", $phone);
    $phone = preg_replace("#^016([0-9]+)#si", "03$1", $phone);
    $phone = preg_replace("#^0120([0-9]+)#si", "070$1", $phone);
    $phone = preg_replace("#^0121([0-9]+)#si", "079$1", $phone);
    $phone = preg_replace("#^0122([0-9]+)#si", "077$1", $phone);
    $phone = preg_replace("#^0126([0-9]+)#si", "076$1", $phone);
    $phone = preg_replace("#^0128([0-9]+)#si", "078$1", $phone);
    $phone = preg_replace("#^0123([0-9]+)#si", "083$1", $phone);
    $phone = preg_replace("#^0124([0-9]+)#si", "084$1", $phone);
    $phone = preg_replace("#^0125([0-9]+)#si", "085$1", $phone);
    $phone = preg_replace("#^0127([0-9]+)#si", "081$1", $phone);
    $phone = preg_replace("#^0129([0-9]+)#si", "082$1", $phone);
    $phone = preg_replace("#^0188([0-9]+)#si", "058$1", $phone);
    $phone = preg_replace("#^0186([0-9]+)#si", "056$1", $phone);
    $phone = preg_replace("#^0199([0-9]+)#si", "059$1", $phone);
    if (!is_numeric($phone))
        return false;
    return strlen($phone) == 10 ? $phone : false;
}
function check_phone_banned($phone)
{
    global $_db;
    $banned = $_db->query("select `id` from `core_orders` where `order_phone`='" . escape_string($phone) . "' and `status`='shiperror' ")->num_rows();
    return $banned;
}
function check_phone_blacklist($phone)
{
    global $_db;
    $blacklist = $_db->query("select `id` from `core_backlists` where `phone_number`='" . escape_string($phone) . "' ")->num_rows();
    return $blacklist;
}
function check_phone_today_order($phone, $offer_id, $time = null)
{
    global $_db;
    $today = strtotime('today GMT+7 00:00');
    $tomorrow = strtotime('+1 days GMT+7 00:00');
    $today_order = $_db->query("select `id` from `core_orders` where `offer`='" . escape_string($offer_id) . "' and `order_phone`='" . escape_string($phone) . "' and ((`time`>='" . $today . "' and `time`< '" . $tomorrow . "') or `status`='uncheck') and `time`!='" . $time . "'")->num_rows();
    return $today_order;
}

function getOffers($key = "all")
{
    global $_db;
    if (strtolower($key) == "all")
        $_offers = $_db->query("select * from `core_offers` where `status`='run'")->fetch_array();
    else
        $_offers = $_db->query("select * from `core_offers` where `status`='run' AND `key`='" . escape_string($key) . "'")->fetch_array();
    return $_offers;
}

function curl_sendNotifi($url, $timeout = 100)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
}

function send_postback($postback_url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $postback_url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code;
}

function update_s2s_postback($postback_url, $response_code, $offer, $landing, $ukey = null)
{
    if ($response_code === 200) {
        $state = 'Success';
    } else {
        $state = 'Fail';
    }
    $post_back = db_get_data('core_s2s_postback', array('response_code' => $response_code, 'request_url' => $postback_url, 'id', 1));

    if (!$post_back['id']) {
        $_update_datas = array(
            'offer_id'      => $offer['id'],
            'type_ads_id'   => $offer['type_ads'],
            'landing_page'  => $landing,
            'state'         => $state,
            'request_url'   => $postback_url,
            'response_code' => $response_code,
            'created'       => date('Y-m-d H:i:s'),
            'ukey'          => $ukey,
        );
        $_update_stats = db_insert_data('core_s2s_postback', $_update_datas);
        return $_update_stats;
    }
    return false;
}
