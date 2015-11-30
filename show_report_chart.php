<?php
//Affichage des graphiques

echo "<div class=\"col-md-12\">";
foreach($reportCharts as $reportChart){
    $chartTmp = Chart::getChart($reportChart->deviceid);
    //echo "<br/>Chart #".$chartTmp->id;
    //print_r($devices[$chartTmp->id]);
?>
<div class="col-md-6" align="center" <?php if($isPDF){echo " style=\"width:650px;page-break-after: always;\"";}  ?>>
    <div class="portlet" style="background-color: #FFF;">
        <div class="portlet-title">
            <div class="caption"> <?php echo $chartTmp->name; ?> </div>
        </div>
        <div class="portlet-body">
            <div class="cell col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="container-<?php echo $chartTmp->id; ?>" class="container-<?php echo $chartTmp->id; ?> dashboard-stat" style="overflow: auto;"></div>
                <!--<div class="deleteItemPage" style="margin-bottom:10px;position: absolute;top:370px;right:10px;">
                    
                </div>-->
            </div>
        </div>
    </div>
</div>
<?php
}
?>
</div>
<div class="col-md-12">
<?php
foreach($devicesByType as $type=>$tmpDevices){
?>
    <div class="col-md-6" <?php if($isPDF){ echo " style=\"500px;\" "; } ?> >
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption"> <?php echo ucwords($type); ?> </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-hover"  <?php if($isPDF){ echo " style=\"border:none;\" "; } ?>>
                        <thead>
                            <th>Objet</th>
                            <th><i class="fa fa-sort-desc" title="Min"></i></th>
                            <th><i class="fa fa-sort-asc" title="Max"></i></th>
                            <th><i class="fa fa-sliders"  title="Moyenne"></i></th>
                        </thead>
                        <tbody>
<?php
foreach($tmpDevices as $tmpDevice){
    $txtAvg=$txtMin=$txtMax="";
    /*if($tmpDevice->incremental != "" && $tmpDevice->incremental != "0" && !is_null($tmpDevice->incremental)){
        if(isset($sum[intval($tmpDevice->id)])){
            $txtAvg = $sum[$tmpDevice->id];
        }
    } else {*/
        $txtMin = Device::showStateGeneric($min[$tmpDevice->id],$tmpDevice->data_type,$tmpDevice->unite);
        $txtMax = Device::showStateGeneric($max[$tmpDevice->id],$tmpDevice->data_type,$tmpDevice->unite);
        $txtAvg = Device::showStateGeneric($avg[$tmpDevice->id],$tmpDevice->data_type,$tmpDevice->unite);
    //}
?>
                            <tr>
                                <td><?php echo "#".$tmpDevice->id." ".$tmpDevice->name; ?></td>
                                <td>
                                    <span class="label label-sm label-info">
                                        <?php echo $txtMin; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="label label-sm label-warning">
                                        <?php echo $txtMax; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="label label-sm label-success">
                                        <?php echo $txtAvg; ?>
                                    </span>
                                </td>
                            </tr>
<?php

}

if(isset($devicesIncByType[$type])){
    foreach($devicesIncByType[$type] as $tmpDeviceInc){
        $txtAvg = Device::showStateGeneric($sum[$tmpDeviceInc->id],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
        $txtAvgLast = Device::showStateGeneric($sumLastPeriod[$tmpDeviceInc->id],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
        $percent = History::getPercent($sum[$tmpDeviceInc->id], $sumLastPeriod[$tmpDeviceInc->id]);
        $minValue=History::getMinMaxForDevicesInc($tmpDeviceInc->id, $report->period, "min");
        $maxValue=History::getMinMaxForDevicesInc($tmpDeviceInc->id, $report->period, "max");
        $txtMin = Device::showStateGeneric($minValue['value'],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
        $txtMax = Device::showStateGeneric($maxValue['value'],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
?>
                            <tr>
                                <td><?php echo "#".$tmpDeviceInc->id." ".$tmpDeviceInc->name; ?></td>
                                <td>
                                    <span class="label label-sm label-info">
                                        <?php echo $txtMin; ?>
                                    </span><br/><span style="font-size:10px;"><?php echo $minValue['date']; ?></span>
                                </td>
                                <td>
                                    <span class="label label-sm label-warning">
                                        <?php echo $txtMax; ?>
                                    </span><br/><span style="font-size:10px;"><?php echo $maxValue['date']; ?></span>
                                </td>
                                <td>
                                    <span class="label label-sm label-success">
                                        <?php echo $txtAvg." (".$percent."%)"; ?>
                                    </span>
                                </td>
                            </tr>
<?php
    }
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

foreach($devicesIncByType as $type=>$tmpDevices){
?>
    <div class="col-md-6" <?php if($isPDF){ echo " style=\"500px;\" "; } ?> >
        <div class="portlet box green-haze">
            <div class="portlet-title">
                <div class="caption"> <?php echo ucwords($type); ?> </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-hover"  <?php if($isPDF){ echo " style=\"border:none;\" "; } ?>>
                        <thead>
                            <th>Objet</th>
                            <th><i class="fa fa-sort-desc" title="Min"></i></th>
                            <th><i class="fa fa-sort-asc" title="Max"></i></th>
                            <th><i class="fa fa-sliders"  title="Moyenne"></i></th>
                        </thead>
                        <tbody>
<?php
    foreach($tmpDevices as $tmpDeviceInc){
        //$txtAvg = Device::showStateGeneric($sum[$tmpDevice->id],$tmpDevice->data_type,$tmpDevice->unite);
        //$txtAvgLast = Device::showStateGeneric($sumLastPeriod[$tmpDevice->id],$tmpDevice->data_type,$tmpDevice->unite);
        //$percent = History::getPercent($sum[$tmpDevice->id], $sumLastPeriod[$tmpDevice->id]);

        $txtAvg = Device::showStateGeneric($sum[$tmpDeviceInc->id],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
        $txtAvgLast = Device::showStateGeneric($sumLastPeriod[$tmpDeviceInc->id],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
        $percent = History::getPercent($sum[$tmpDeviceInc->id], $sumLastPeriod[$tmpDeviceInc->id]);
        $minValue=History::getMinMaxForDevicesInc($tmpDeviceInc->id, $report->period, "min");
        $maxValue=History::getMinMaxForDevicesInc($tmpDeviceInc->id, $report->period, "max");
        $txtMin = Device::showStateGeneric($minValue['value'],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
        $txtMax = Device::showStateGeneric($maxValue['value'],$tmpDeviceInc->data_type,$tmpDeviceInc->unite);
?>
                            <tr>
                                <td><?php echo "#".$tmpDeviceInc->id." ".$tmpDeviceInc->name; ?></td>
                                <td>
                                    <span class="label label-sm label-info">
                                        <?php echo $txtMin; ?>
                                    </span><br/><span style="font-size:10px;"><?php echo $minValue['date']; ?></span>
                                </td>
                                <td>
                                    <span class="label label-sm label-warning">
                                        <?php echo $txtMax; ?>
                                    </span><br/><span style="font-size:10px;"><?php echo $maxValue['date']; ?></span>
                                </td>
                                <td>
                                    <span class="label label-sm label-success">
                                        <?php echo $txtAvg." (".$percent."%)"; ?>
                                    </span>
                                </td>
                            </tr>
<?php
    }
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--<div class="col-md-3">
        <div class="dashboard-stat red-intense">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number"> <?php echo $percent."%"; ?> </div>
                <div class="desc"> <?php echo $txtAvg; ?> </div>
            </div>
            <a class="more" href="#">
                <?php echo "#".$tmpDevice->id." ".$tmpDevice->name; ?> 
                <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>-->
<?php
}
echo "</div>";
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
</script>
