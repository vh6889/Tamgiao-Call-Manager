<?php 
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE['statistic_province_offer']) ? $_COOKIE['statistic_province_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('today GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());

if(isset($_GET['offer']) && $_GET['offer'])
  setcookie('landing_offer',$offer,time()+3600*24*365);

if(isset($_GET['offer']) && !$_GET['offer'])
  setcookie('landing_offer',"");

$time_ts = strtotime(str_replace('/', '-', $ts) . " GMT+7 00:00");
$time_te = strtotime(str_replace('/', '-', $te) . " GMT+7 23:59");

$results = array();
$sql_offer = $offer != "all" ? "AND `offer`='" . escape_string($offer) . "' " : "";

$query = $_db->query("SELECT `order_province`,`status`,COUNT(id) AS `so_luong` from `core_orders` WHERE (`time` BETWEEN '$time_ts' AND '$time_te') $sql_offer GROUP BY `order_province`,`status`")->fetch_array();

foreach ($query as $province) {
  $province_title = mb_strtolower(trim(escape_string($province['order_province'])));
  $province_title = str_replace(',', '', $province_title);

  $province_code  = Locdau($province_title);
  if (strpos($province_code, 'ho-chi-minh'))
    $province_code = 'ho-chi-minh';

  if (!isset($results[$province_code]['title']))
    $results[$province_code]['title'] = $province_title;
  $results[$province_code][$province['status']] = isset($results[$province_code][$province['status']]) ? $results[$province_code][$province['status']] + $province['so_luong'] : $province['so_luong'];

  $results[$province_code]['total'] = isset($results[$province_code]['total']) ? $results[$province_code]['total'] + $results[$province_code][$province['status']] : $results[$province_code][$province['status']];
}

/* Sort results by total number asc */
usort($results, function ($a, $b) {
  $orderParam = isset($_POST['column']) ? $_POST['column'] : 'total';
  return $a[$orderParam] <=> $b[$orderParam];
});
/* reverse result to desc */
if(isset($_POST['sortOrder']) && $_POST['sortOrder']=='desc')
  $results = array_reverse($results);

$resultHtml = '';
foreach ($results as $arr) {
  $row['title'] = ($arr['title'] && $arr['title']!='n-a') ? $arr['title'] : 'Null';
  $row['total'] = $arr['total'] ? $arr['total'] : 0;
  $row['approved'] = $arr['approved'] ? $arr['approved'] : 0;
  $row['uncheck'] = $arr['uncheck'] ? $arr['uncheck'] : 0;
  $row['pending'] = $arr['pending'] ? $arr['pending'] : 0;
  $row['rejected'] = $arr['rejected'] ? $arr['rejected'] : 0;
  $row['shipdelay'] = $arr['shipdelay'] ? $arr['shipdelay'] : 0;
  $row['shipping'] = $arr['shipping'] ? $arr['shipping'] : 0;
  $row['shiperror'] = $arr['shiperror'] ? $arr['shiperror'] : 0;
  $row['shipfail'] = $arr['shipfail'] ? $arr['shipfail'] : 0;
  $row['trashed'] = $arr['trashed'] ? $arr['trashed'] : 0;
  
  $bg = ($arr['title'] && $arr['title']!='n-a') ? '' : 'bg-light';
  $resultHtml.= '<tr class="tr-statistic '.$bg.'">';
  $resultHtml.= '<td class="text-center text-capitalize">'.$row['title'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['total'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['approved'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['uncheck'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['pending'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['rejected'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['shipdelay'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['shipping'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['shiperror'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['shipfail'].'</td>';
  $resultHtml.= '<td class="text-center">'.$row['trashed'].'</td>';
  $resultHtml.= '</tr>';
}

echo $resultHtml; 
?>