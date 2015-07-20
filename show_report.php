<?php
include "modules/header.php";
//include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB()  ;
include_once "models/Chart.php";
include_once "models/ChartDevice.php";
include_once "models/Device.php";
include_once "models/History.php";
include_once "models/Report.php";
include_once "models/ReportDevice.php";

if(!isset($_GET["idReport"])){
    throw new Exception("Veuillez renseigner un numero de rapport");
}
$idReport=$_GET["idReport"];
$report=Report::getReport($idReport);
$reportCharts=ReportDevice::getReportDevicesForReport($idReport);
$devices=$devicesByType=$charts=$history=$deviceIds=$deviceIdsInc=array();
foreach($reportCharts as $reportChart){
    $charts[$reportChart->deviceid]=Chart::getChart($reportChart->deviceid);
    foreach(ChartDevice::getChartDeviceForChart($reportChart->deviceid) as $tmpChartDevice){
        $tmpDevice=Device::getDevice($tmpChartDevice->deviceid);
        $devices[$reportChart->deviceid][]=$tmpDevice;
        $devicesByType[strtolower($tmpDevice->type)][]=$tmpDevice;
    }
}

foreach($devices as $chartName=>$types){
    foreach($types as $device){
        $deviceIds[]=$device->id;
        if($device->incremental != "" && !is_null($device->incremental)){
            $deviceIdsInc[]=$device->id;
        }
        $history[$device->id]=History::getConsolidation($device->id, $report->period);
    }
}

$sum=History::getTotalAvgForDevices($deviceIdsInc, $report->period, "SUM");
$avg=History::getTotalAvgForDevices($deviceIds, $report->period, "AVG");
$min=History::getMinMaxForDevices($deviceIds, $report->period, "MIN");
$max=History::getMinMaxForDevices($deviceIds, $report->period, "MAX");
?>
<!-- BEGIN PAGE -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row" style="margin-top:60px;">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    Rapport: <?php echo $report->name; ?>
                    <small><?php echo $report->description." - ".$report->period; ?></small>
                </h3>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="index.php">Admin</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>   
                        <a href="admin_report.php">Liste des rapports</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="#"><?php echo $report->name; ?></a>
                        <i class="icon-angle-right"></i>
                    </li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
        </div>
        <div class="row-fluid">
            <?php include "show_report_chart.php"; ?>
            <div class="col-md-12">
                <!--<div class="tabbable-custom ">
                    ><ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="show_report.php#tab_chart" data-toggle="tab">Graphique</a>
                        </li>
                        <li>
                            <a href="show_report.php#tab_data" data-toggle="tab">Données</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_chart">-->
                        <!--</div>
                        <div class="tab-pane" id="tab_data">
                            <?php //print_r($history); ?>
                        </div>
                    </div>
                </div>-->
                
            </div>
        </div>
    </div>
</div>
    
<script description="text/javascript">
$(document).ready(function () {
<?php
foreach($charts as $chart){
    if($chart->type == "temps" || $chart->type == "ligne"){
        echo "\n$('.container-".$chart->id."').highcharts({";
        echo " chart: {";
        if($chart->type=='temps'){
            echo " zoomType: 'x'";
        }elseif($chart->type=='ligne'){
            echo " type: 'spline'";
        }
        //echo ",height:'".$heightChart."px'";
        //echo ",width:'375px'";
        echo " },";
        echo "title: {";
        echo " text: '".$chart->name."'";
        echo "},";
        echo "subtitle: {";
        echo "text: '".$chart->description." - ".$chart->getBorneDates()."'";
        echo "},";
        echo "xAxis: {";
        echo "type: 'datetime',";
        echo "title: {";
        echo "text: '".$chart->abscisse."'";
        echo " }";
        echo "},";
        echo "tooltip: {";
        echo "animation:false";
        /*echo " formatter: function() { ";
        echo " return  '<b>' + this.series.name +'</b><br/>' +";
        echo " Highcharts.dateFormat('%e - %b - %Y %Hh%M' ,";
        echo " new Date(this.x)) ";
        echo " + ' , ' + this.y + ' ';";
        echo " } ";*/
        echo "},";
        echo "plotOptions: {";
        echo "series: { enableMouseTracking: false, shadow: false, animation: false }";
        echo "},";
        echo "yAxis: {";
        $incremental=FALSE;
        foreach($devices[$chart->id] as $device){
            if($device->incremental != "" && $device->incremental != "0"){
                $incremental=TRUE;
            }
        }
        if(!$incremental){
            if($chart->scaleMin != "" &&  $chart->scaleMax != $chart->scaleMin){
                echo " min: ".$chart->scaleMin.",";
            }
            if($chart->scaleMax != "" &&  $chart->scaleMax != $chart->scaleMin){
                echo " max: ".$chart->scaleMax.",";
            }

        }
        echo "title: {";
        echo "text: '".$chart->ordonne."'";
        echo "}";
        echo "},";
        echo "series: [";
        $i=0;
        $data="";
        foreach($devices[$chart->id] as $device){
            //print_r($history[$device->id]);
            $data = History::getDataForChart($history[$device->id], $report->period, $device->incremental);
            if($i>0){
                echo ",";
            }
            echo "{";  
            if($chart->type=='temps'){
                echo "type:'area',";  
            }
            echo "animation:false,";  
            echo "name:'".$device->name."',";  
            //echo "data:[ [Date.UTC(2014,8,21,0,8),18],[Date.UTC(2014,8,21,1,14),17.8],[Date.UTC(2014,8,21,2,20),17.4],[Date.UTC(2014,8,21,3,26),17.4] ]";  
            echo "data:[ ".$data." ]";  

            /*if($chart->price && !is_null($device->chart_formula)){
                echo ",tooltip: {";
                echo " valueDecimals: 2,";
                echo "valuePrefix: '€',";
                echo "valueSuffix: ' EUR'";
                echo "}";
            }*/


            echo "}";
            $i++;
        }
        echo "]";
        echo "});";
    }elseif($chart->type == "barre"){
            echo "$('.container-".$chart->id."').highcharts({";
            echo " chart: {";
            echo " type: 'column'";
            echo " },";
            echo "title: {";
            echo " text: '".$chart->name."'";
            echo "},";
            echo "subtitle: {";
            echo "text: '".$chart->description." - ".$chart->getBorneDates()."'";
            echo "},";
            echo "xAxis: {";
            echo "categories: [";
            switch($chart->period){
                case '1':
                    $j=$chart->getHeureFormatted();
                    for($i=0;$i<=23;$i++){
                        $j=($j > 23) ? 0 : $j;
                        if($i>0){echo ",";}
                        echo "'".$j."'";
                        $j++;
                    }
                    break;
                case '2':
                    echo $chart->getDaysForWeek();
                    break;
                case '3':
                    echo $chart->getDaysForMonth();
                    break;
                case '4':
                    echo $chart->getMonthForYear();
                    break;
            }
            echo "]";
            echo "},";
            echo "yAxis: {";
            echo "min: 0,";
            echo "title: {";
            echo "text: '".$chart->ordonne."'";
            echo "}";
            echo ",stackLabels: {";
            echo "style: {";
            echo "fontWeight: 'bold',";
            echo "color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'";
            echo "}";
            echo "}";
            echo "},";
            echo "plotOptions: {";
            echo "series: { enableMouseTracking: false, shadow: false, animation: false }";
            echo ",column: {";
            echo "stacking: 'normal',";
            echo " dataLabels: {";
            echo " color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',";
            echo "style: {";
            //echo "textShadow: '0 0 3px black'";
            echo "}";
            echo "}";
            echo "}";
            echo "},";
            echo "series: [";
            $k=0;
            foreach($devices[$chart->id] as $device){
                //print_r($history[$device->id]);
                $data = History::getDataForChart($history[$device->id], $report->period, $device->incremental);
                if($k>0){
                    echo ",";
                }
                echo "{";  
                echo "name:'".$device->name."',";  
                //echo "data:[ [Date.UTC(2014,8,21,0,8),18],[Date.UTC(2014,8,21,1,14),17.8],[Date.UTC(2014,8,21,2,20),17.4],[Date.UTC(2014,8,21,3,26),17.4] ]";  
                echo "data:[ ".$data." ]";
                /*if($chart->price && !is_null($device->chart_formula)){
                    echo ",tooltip: {";
                    echo " valueDecimals: 2,";
                    echo "valuePrefix: '€',";
                    echo "valueSuffix: ' EUR'";
                    echo "}";
                }*/
                echo "}";
                $k++;
                /*if(substr($data,-1) == ","){
                    $data = substr($data, 0, strlen($data) - 1);
                }*/
                //print_r($data);
            }
            /*foreach(ChartDevice::getChartDeviceForChart($item->chartId) as $chartDevice){
                $device=Device::getDevice($chartDevice->deviceid);

                $chartFormula=($chart->price && !is_null($device->chart_formula)) ? $device->chart_formula : NULL;
                $data=History::getHistoryHighchartBarre($chartDevice->deviceid, $chart->period, $chart->from, $chartFormula);
                //print_r($data);
                //exit;
                //$data=0;
                if($k>0){
                    echo ",";
                }
                echo "{";  
                echo "name:'".$device->name."',";  
                //echo "data:[ [Date.UTC(2014,8,21,0,8),18],[Date.UTC(2014,8,21,1,14),17.8],[Date.UTC(2014,8,21,2,20),17.4],[Date.UTC(2014,8,21,3,26),17.4] ]";  
                echo "data:[ ".$data." ]";
                if($chart->price && !is_null($device->chart_formula)){
                    echo ",tooltip: {";
                    echo " valueDecimals: 2,";
                    echo "valuePrefix: '€',";
                    echo "valueSuffix: ' EUR'";
                    echo "}";
                }
                echo "}";
                $k++;
            }*/
            echo "]";
            echo "});";
        }
}
?>
});
</script>
<?php
include "modules/footer.php";
?>