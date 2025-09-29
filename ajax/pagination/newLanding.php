<?php 
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : (isset($_COOKIE[$_status.'_offer']) ? $_COOKIE[$_status.'_offer'] : 'all');
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] :  date('d/m/Y',strtotime('today GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] :  date('d/m/Y',time());

if(isset($_GET['offer']) && $_GET['offer'])
  setcookie('landing_offer',$offer,time()+3600*24*365);

if(isset($_GET['offer']) && !$_GET['offer'])
  setcookie('landing_offer',"");


$_offers = getOffer();
$sql = "";
if($offer != "all")
  $sql = " and `offer`='".escape_string($offer)."' ";


$list_time = array();
$time_ts = strtotime(str_replace('/', '-', $ts)." GMT+7 00:00");
$time_te = strtotime(str_replace('/', '-', $te)." GMT+7 23:59");
if($time_ts && $time_te){
  for ($i=$time_ts; $i <= $time_te ; $i+=86400){
    $list_time[] = date("d-m-Y",$i);
  }
}
if(isset($_POST['limit'])){
    $perPage = $_POST['limit'];
}else{
    $perPage = 50;
}

if(!isAdmin() && isPublisher()){
    $sql .= " and `ukey`= '".$_user['ukey']."'";
}

$result = $_db->query("select `id`,`offer`,`date`,`landing`, sum(`viewPage`) as `viewPage`, sum(`order`) as `order`,`ukey` from `core_landing_stats` where `date` in ('".implode("','", $list_time)."') ".$sql." group by `landing`,`date` order by id DESC LIMIT $perPage")->fetch_array();
$paginationHtml = '';
$total_viewPage = 0;
$total_order = 0;
$total_count_apo = 0;
$total_sales = 0;
$total_rejected = 0;
$total_trashed = 0;
foreach ($result as $arr) {
    $offer = getOffer($arr['offer']);
    $orders = $_db->query("select * from `core_orders` where `offer`='".$offer['id']."' and (`time` >= '".(strtotime($arr['date']." GMT+7 00:00"))."' and `time` < '".(strtotime($arr['date']." GMT+7 23:59"))."') and `landing`='".$arr['landing']."' ")->fetch_array();
    $od_rejected = 0;
    $od_trashed = 0;
    $od_sales = 0;
    $count_apo = 0;
    foreach ($orders as $od) {
        if($od['status'] == "rejected"){
            $od_rejected = $od_rejected + 1;
        }
        if($od['status'] == "trashed"){
            $od_trashed = $od_trashed + 1;
        }
        if(in_array($od['status'], ['approved','shipping','pending','shipdelay','shiperror'])){
            $od_sales = $od_sales + $od['number'];
            $count_apo = $count_apo + 1;
        }
    }
    $apo = count($orders) > 0 ? round($count_apo/count($orders)*100,2) : 0;
    $paginationHtml.= '<tr data-id="'.$arr['id'].'">';
    $paginationHtml.= '<tr class="tr-statistic">';
    $paginationHtml.= '<td class="text-left">'.$arr['date'].'</b></td>';
    $paginationHtml.= '<td class="text-left">'.($offer ? '<b  class="trigger green lighten-3">'._e($offer['name']).'</b>': 'Unknown').'</td>';
    $paginationHtml.= '<td class="text-left"><b class="number">'._e($arr['landing']).' <a target="_blank" href="https://'._e($arr['landing']).'"><i class="fas fa-external-link-alt ml-1"></i></a></b></td>';
    $paginationHtml.= '<td class="text-center">'.($arr['viewPage'] > 0 ? '<b class="number">'.$arr['viewPage'].'</b>':'0').'</td>';
    $paginationHtml.= '<td class="text-center">'.($arr['order'] > 0 ? '<b class="number">'.$arr['order'].'</b>':'0').'</td>';
    $paginationHtml.= '<td class="text-center">'.($apo > 0 ? '<b class="number">'.$apo.'</b>% (<small>'.$count_apo.'</small>)':'0').'</td>';
    $paginationHtml.= '<td class="text-center">'.($od_sales > 0 ? '<b class="number">'.$od_sales.'</b>':'0').'</td>';
    $paginationHtml.= '<td class="text-center">'.($od_rejected > 0 ? '<b class="number">'.$od_rejected.'</b>':'0').'</td>';
    $paginationHtml.= '<td class="text-center">'.($od_trashed > 0 ? '<b class="number">'.$od_trashed.'</b>':'0').'</td>';
    $paginationHtml. '</tr>';
    $total_viewPage = $total_viewPage+$arr['viewPage'];
    $total_order = $total_order+$arr['order'];
    $total_count_apo = $total_count_apo + $count_apo;
    $total_rejected = $total_rejected + $od_rejected;
    $total_trashed = $total_trashed + $od_trashed;
    $total_sales = $total_sales + $od_sales;
}
echo $paginationHtml;
