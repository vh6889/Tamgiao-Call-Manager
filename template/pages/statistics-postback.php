<?php
$offer = isset($_GET['offer']) ? trim($_GET['offer']) : 'all';
$ts = isset($_GET['ts']) && !empty($_GET['ts']) ? $_GET['ts'] : date('d/m/Y',strtotime('-6 days GMT+7 00:00'));
$te = isset($_GET['te']) && !empty($_GET['te']) ? $_GET['te'] : date('d/m/Y',time());
$time_ts = strtotime(str_replace('/', '-', $ts)." GMT+7 00:00");
$time_te = strtotime(str_replace('/', '-', $te)." GMT+7 23:59");

$results = array();
$list_time = array();


if($time_ts && $time_te){
  for ($i=$time_ts; $i <= $time_te ; $i+=86400){
    $list_time[] = date("Y/m/d",$i);
  }
}
if($view == "hour"){

  if($time_ts && $time_te){
    for ($i=0; $i <= 23 ; $i++) { 
      $results[($i < 10 ? '0'.$i : $i).':00']['count_total'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_presales'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_sales'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_earnings'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_uncheck'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_calling'] = 0;
      //$results[($i < 10 ? '0'.$i : $i).':00']['count_callback'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_callerror'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_pending'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_shipping'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_shipdelay'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_shiperror'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_rejected'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_trashed'] = 0;
      $results[($i < 10 ? '0'.$i : $i).':00']['count_approved'] = 0;
    }

  }

} else {

  if($time_ts && $time_te){
    for ($i=$time_ts; $i <= $time_te ; $i+=86400){
      $results[date("Y/m/d",$i)]['count_total'] = 0;
      $results[date("Y/m/d",$i)]['count_presales'] = 0;
      $results[date("Y/m/d",$i)]['count_sales'] = 0;
      $results[date("Y/m/d",$i)]['count_earnings'] = 0;
      $results[date("Y/m/d",$i)]['count_uncheck'] = 0;
      $results[date("Y/m/d",$i)]['count_calling'] = 0;
      //$results[date("Y/m/d",$i)]['count_callback'] = 0;
      $results[date("Y/m/d",$i)]['count_callerror'] = 0;
      $results[date("Y/m/d",$i)]['count_pending'] = 0;
      $results[date("Y/m/d",$i)]['count_shipping'] = 0;
      $results[date("Y/m/d",$i)]['count_shipdelay'] = 0;
      $results[date("Y/m/d",$i)]['count_shiperror'] = 0;
      $results[date("Y/m/d",$i)]['count_rejected'] = 0;
      $results[date("Y/m/d",$i)]['count_trashed'] = 0;
      $results[date("Y/m/d",$i)]['count_approved'] = 0;
    }
  }

}


$_group = getGroup($_user['group']);
$_offers = getOffer();

?>
<link rel="stylesheet" type="text/css" href="template/assets/css/addons/datatables.min.css">
<link rel="stylesheet" href="template/assets/css/addons/datatables-select.min.css">
<script type="text/javascript" src="template/assets/js/addons/datatables.min.js"></script>
<script type="text/javascript" src="template/assets/js/addons/datatables-select.min.js"></script>

<link rel="stylesheet" href="template/assets/css/daterangepicker.css">
<script type="text/javascript" src="template/assets/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="template/assets/js/daterangepicker/daterangepicker.min.js"></script>

<h2 class=" section-heading">Statistics Postback</h2>

<section class="row mb-5 pb-3">
    <div class="col-md-12 mx-auto white z-depth-1" style="overflow-x: hidden;">
      <form name="filter" method="GET">
        <input name="route" value="statistics-postback" type="hidden">      
        <input name="ts" value="" type="hidden">
        <input name="te" value="" type="hidden">
      <div class="row mb-3">
        <div class="col-sx-12 col-md-2 pb20">
          <select role="filter-select" id="filter-offer" class="mdb-select" name="offer">
            <option value="all" selected>All Offer</option>
            <?php if($_group['offers'] || isAller()){                      
              foreach ($_offers as $of) {      
                $data1=$_db->query("select * from `core_orders` where `date` in ('".implode("','", $list_time)."') and `offer`='".$of['id']."' ".$sql." order by `time` asc ")->fetch_array();                            
                $_db->query("UPDATE `core_marks` SET `name`='".$of['name']."',`mark`='".count($data1)."' WHERE name='".$of['name']."'");                            
                if(preg_match("#\|".$of['id'].",#si", $_group['offers']) || isAller())
                  echo '<option value="'.$of['id'].'" '.($offer == $of['id'] ? 'selected' : '').'>'._e($of['name']).'</option>';
              }
            }
			?>
          </select>
        </div>        

        


        <div class="col-sx-12 col-md-4 pb20">

          <span id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100%;display: block;margin-top: 5px;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span>
              <i class="fa fa-caret-down"></i>
          </span>
        </div>
        <div class="col-sx-12 col-md-2 pb20">
          <button class="btn btn-primary waves-effect waves-light mx-3" type="submit">Apply Filter</button>
          <button class="btn btn-danger waves-effect waves-light" type="button" id="clear-filter">Clear</button>
        </div>
      </div>
    </form>       
    <style>
#chart-container {
    width: 100%;
}
</style>
<div id="chart-container" style="margin-bottom:15px;">
  <div class="row">
      <div class="col-md-6">
        <div class="panel panel-default" style="border:1px solid #f0645e;">
          <div class="panel-heading" style="background:#ff3547;height:40px;line-height:40px;color:#fff;padding-left:10px;font-weight:bold;">Postback Today</div>
          <div class="panel-body" style="padding:15px;">
            <canvas id="chart-student-ages"></canvas>
          </div>
        </div>  
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>      
      </div>
      <div class="col-md-6">
      <div class="panel panel-default" style="border:1px solid #04b2dc;">
          <div class="panel-heading" style="background:#04b2dc;height:40px;line-height:40px;color:#fff;padding-left:10px;font-weight:bold;">Postback by weekend</div>
          <div class="panel-body" style="padding:15px;">          
          <canvas id="graphCanvas"></canvas>
          </div>
        </div>           
      </div>
  </div>        
</div>
    <script type="text/javascript" src="template/assets/js/canvasjs.min.js"></script>
         <?php 
            $getdate=date('Y-m-d');
            $gra_offers=$_db->query("select * from `core_offers`")->fetch_array();  
            $data_mark = '[';
            $data_mark_count_no = '[';    
            $data_mark_count = '['; 
            $gra_sum=$_db->query("select count(id) as totalCount from `core_s2s_postback` where DATE_FORMAT(created,'%Y-%m-%d')='".$getdate."' and `response_code`=200")->fetch();     
            $dem_sum=1;
            foreach($gra_offers as $item_offers){                          
                $count_200 = $_db->query("select count(id) as total200 from `core_s2s_postback` where DATE_FORMAT(created,'%Y-%m-%d')='".$getdate."' and `offer_id`='".$item_offers['id']."' and `response_code`=200")->fetch();  
                $tong = $gra_sum["totalCount"] ?  round($count_200['total200']*100/($gra_sum["totalCount"])) : 0;;                    
                $data_mark.='"'.$item_offers['name'].' ('.$tong.'%)",'; 
                $data_mark_count_no.='"'.$count_200['total200'].'",';      
                $data_mark_count.='"'.$count_200['total200'].' postback ('.$tong.'%)",';  
                $dem_sum++; 
            }    
            $data_mark_count_no.= ']'; 
            $data_mark.=']'; 
            $data_mark_count.=']'; 
            $data_rs1='';
            $dem=1;
            foreach($list_time as $item_time){
                $convent_date=date('Y-m-d',strtotime($item_time));
                if($offer=='all'){
                    $count_date = $_db->query("select count(id) as totalCount from `core_s2s_postback` where DATE_FORMAT(created,'%Y-%m-%d')='".$convent_date."' and `response_code`=200")->fetch();                
                }else{
                    $count_date = $_db->query("select count(id) as totalCount from `core_s2s_postback` where `offer_id`='".$offer."' and DATE_FORMAT(created,'%Y-%m-%d')='".$convent_date."' and `response_code`=200")->fetch();                
                }
                $data_rs1.=",name".$dem."=>".$item_time."-".$count_date['totalCount'];
                $dem++;
            }  
            $mang_bar = substr($data_rs1,1);                      
        ?>
    <script>
        $(document).ready(function () {  
            showGraph();        
          draw_students_ages_diagram();          
        });
      function draw_students_ages_diagram() {
          var ctx = document.getElementById("chart-student-ages");
          var chart;
          var dataDraw = {
              'labels': <?php echo $data_mark; ?>,
              'values': <?php echo $data_mark_count_no; ?>,
              'colors': ["#04b2dc", "#ff3547", "#f0ad4e", "#8ed2ff","#c2c2a3",],
              'labelsCallback':<?php echo $data_mark_count; ?>
          };
          return drawMap(dataDraw, chart, ctx);
      }      
    function drawMap(dataDraw, chart, ctx) {
        var labels = dataDraw.labels;
        var newData = dataDraw.values;
        var allColor = dataDraw.colors;
        var labelsCallback = dataDraw.labelsCallback;
        var color = [];
        for (i = 0; i < labels.length; i++) {
            color[i] = allColor[i];
        }

        if (dataDraw.borderColors != undefined)
            var borderColors = dataDraw.borderColors;
        else
            var borderColors = color;

        data = {
            labels: labels,

            datasets: [
                {
                    data: newData,
                    hoverBorderWidth: [0, 0, 0],
                    backgroundColor: color,
                    hoverBackgroundColor: color
                }]
        };

        if (!chart === undefined)
            chart.destroy();

        var options = {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem,data) {
                        return labelsCallback[tooltipItem.index];
                        //return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '%';
                    }
                }
            }
        };


        chart = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: options
        });

        return chart;
    } 
    function showGraph()
        {
        	var name = [];
            var marks = [];
            <?php 
              $tong = 0;
              $mang_bar_tach = explode(',',$mang_bar);
              foreach($mang_bar_tach as $item_r){                                
                $tach_name = explode('=>',$item_r);
                $tach_name_name = explode('-',$tach_name[1]);                
                ?>
                name.push('<?php echo date('d-m-Y',strtotime($tach_name_name[0]));?>');
                marks.push('<?php echo $tach_name_name[1];?>');
                <?php
              }
            ?>                

            var chartdata = {
                labels: name,
                datasets: [
                    {
                        label: ['Statistics Postback'],
                        backgroundColor: [
                          "#04b2dc",
                          "#ff3547",
                          "#ff8397",
                          "#6970d5" ,
                          "#F7D358",
                          "#FA58D0",
                          "#0033CC",
                          "#8258FA",
                          "#8A0808",
                          "#FF4000",
                          "#8258FA"
                        ],
                        borderColor: [
                          "rgb(255, 99, 132)",
                          "rgb(255, 159, 64)",
                          "rgb(255, 205, 86)",
                          "rgb(75, 192, 192)",
                          "rgb(54, 162, 235)",
                          "rgb(153, 102, 255)",
                          "rgb(201, 203, 207)"
                        ],
                        hoverBackgroundColor: [
                          "#A9A9F5",
                          "#F7819F",
                          "#140718",
                          "#2A0A29",
                          "#9FF781",
                          "#E2A9F3",
                          "#F5A9D0",
                          "#A9A9F5",
                          "#F78181",
                          "#2ECCFA",
                          "#81F7F3",
                        ],
                        hoverBorderColor: '#666666',
                        data: marks,
                        responsive:true,
                        maintainAspectRatio: false,
                    }
                    
                ]
            };

            var graphTarget = $("#graphCanvas");

            var barGraph = new Chart(graphTarget, {
                type: 'bar',
                data: chartdata,
                options: {  
                  tooltipTemplate: "<%= label %>: <%= value %>%",            
                  tooltips: {
                    callbacks: {
                      label: function(tooltipItem, data) {
                        return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '';
                      }
                    }
                  }
                }
            });
        }
    </script>
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

<script type="text/javascript">
$(function() {

    var start = '<?=$ts;?>';
    var end = '<?=$te;?>';

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY')+ ' - ' + end.format('DD/MM/YYYY'));
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


    $('#reportrange span').html(start+ ' - ' + end);
    $("form[name=filter] input[name=ts]").val(start);
    $("form[name=filter] input[name=te]").val(end);

  $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
      $("form[name=filter] input[name=ts]").val(picker.startDate.format('DD/MM/YYYY'));
      $("form[name=filter] input[name=te]").val(picker.endDate.format('DD/MM/YYYY'));
  });

  $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
      $('#reportrange span').html(moment().subtract(6, 'days').format('DD/MM/YYYY')+ ' - ' + moment().format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setStartDate(moment().subtract(6, 'days').format('DD/MM/YYYY'));
      $('#reportrange').data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
  });
});
</script>
<?php


end:


?>
