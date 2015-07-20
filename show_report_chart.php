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
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <th>Objet</th>
                            <th><i class="fa fa-sort-desc" title="Min"></i></th>
                            <th><i class="fa fa-sort-asc" title="Max"></i></th>
                            <th><i class="fa fa-sliders"  title="Moyenne"></i></th>
                        </thead>
                        <tbody>
<?php
foreach($tmpDevices as $tmpDevice){
    //echo "<br/>";
    if($tmpDevice->incremental != "" && $tmpDevice->incremental != "0"){
        continue;
    }
?>
                            <tr>
                                <td><?php echo "#".$tmpDevice->id." ".$tmpDevice->name; ?></td>
                                <td>
                                    <span class="label label-sm label-info">
                                        <?php echo Device::showStateGeneric(number_format($min[$tmpDevice->id], 2, ",", " "),$tmpDevice->data_type,$tmpDevice->unite); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="label label-sm label-warning">
                                        <?php echo Device::showStateGeneric(number_format($max[$tmpDevice->id], 2, ",", " "),$tmpDevice->data_type,$tmpDevice->unite); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="label label-sm label-success">
                                        <?php echo Device::showStateGeneric(number_format($avg[$tmpDevice->id], 2, ",", " "),$tmpDevice->data_type,$tmpDevice->unite); ?>
                                    </span>
                                </td>
                            </tr>
                <!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="fa fa-comments"></i>
                        </div>
                        <div class="details">
                            <div class="number"> <?php echo number_format($avg[$tmpDevice->id], 2, ",", " ")." ".$tmpDevice->unite; ?> </div>
                            <div class="desc"> <i class="fa fa-sort-desc"></i> 1 | <i class="fa fa-sort-asc"></i> +22% </div>
                        </div>
                        <a class="more" href="#">
                            
                        </a>
                    </div>
                </div>-->
<?php

}
?>
                        </tbody>
                    </table>
                </div>
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
</script>
