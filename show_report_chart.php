<?php
foreach($reportCharts as $reportChart){
    $chartTmp = Chart::getChart($reportChart->deviceid);
    echo "<br/>Chart #".$chartTmp->id;
    //print_r($devices[$chartTmp->id]);
?>
<div class="row">
    <div class="portlet box blue-steel tabbable">
        <div class="portlet-title">
            <div class="caption"> <?php echo $chartTmp->name; ?> </div>
        </div>
        <div class="portlet-body">
            <?php 
            foreach($devices[$chartTmp->id] as $deviceTmp){
                echo "<br/>".$deviceTmp->name;
            }
            ?>
            <div class="cell col-lg-6 col-md-6 col-sm-6 col-xs-12" style="height:400px;">
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
