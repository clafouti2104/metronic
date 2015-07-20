<div class="col-md-12">
<?php
foreach($devicesByType as $type=>$tmpDevices){
?>
    <div class="col-md-12">
        <div class="portlet box green-haze">
            <div class="portlet-title">
                <div class="caption"> <?php echo ucwords($type); ?> </div>
            </div>
            <div class="portlet-body">
<?php
foreach($tmpDevices as $tmpDevice){
    if($tmpDevice->incremental != "" && $tmpDevice != "0"){
        continue;
    }
?>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="fa fa-comments"></i>
                        </div>
                        <div class="details">
                            <div class="number"> <?php echo number_format($avg[$tmpDevice->id], 2, ",", " ")." ".$tmpDevice->unite; ?> </div>
                            <div class="desc"> <i class="fa fa-sort-desc"></i> 1 | <i class="fa fa-sort-asc"></i> +22% </div>
                        </div>
                        <a class="more" href="#">
                            <?php echo "<br/>".$tmpDevice->name; ?>
                            <!--<i class="m-icon-swapright m-icon-white"></i>-->
                        </a>
                    </div>
                </div>
<?php
    
}
?>
            </div>
        </div>
    </div>
<?php  
}
echo "</div>";
echo "<div class=\"col-md-12\">";
foreach($reportCharts as $reportChart){
    $chartTmp = Chart::getChart($reportChart->deviceid);
    //echo "<br/>Chart #".$chartTmp->id;
    //print_r($devices[$chartTmp->id]);
?>
<div class="col-md-6">
    <div class="portlet box blue-steel tabbable" style="background-color: #FFF;">
        <div class="portlet-title">
            <div class="caption"> <?php echo $chartTmp->name; ?> </div>
        </div>
        <div class="portlet-body">
            <div class="cell col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="container-<?php echo $chartTmp->id; ?>" class="container-<?php echo $chartTmp->id; ?> dashboard-stat"></div>
                <div class="deleteItemPage" style="margin-bottom:10px;position: absolute;top:370px;right:10px;">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
</div>

<script type="text/javascript">
    
<?php
    foreach($reportCharts as $reportCharts){
        if($reportChart->deviceid == ""){
            continue;
        }
        echo "var chart".$reportChart->deviceid.";";
    }
?>
$( document ).ready(function() {
    
});
</script>
