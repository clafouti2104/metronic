<div class="col-md-12">
<?php
foreach($devicesByType as $type=>$tmpDevices){
?>
    <div class="col-md-6">
        <div class="portlet box green-haze">
            <div class="portlet-title">
                <div class="caption"> <?php echo ucwords($type); ?> </div>
            </div>
            <div class="portlet-body">
                
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
