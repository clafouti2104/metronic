<?php
include '../../tools/config.php';
include_once "../../models/History.php";
include_once "../../models/Device.php";
$GLOBALS["dbconnec"] = connectDB();

//Récupération du device
$device=  Device::getDevice($_GET["deviceId"]);
$unite=" ".$device->unite;
//Récupération de l'historique
$dataDay=History::getCountForPeriod($device->id, '1');
//$dataDayLast=History::getCountForLastPeriod($device->id, '1');
$dataDayLastNow=History::getCountForLastPeriodUntilNow($device->id, '1');
$dataWeek=History::getCountForPeriod($device->id, '2');
//$dataWeekLast=History::getCountForLastPeriod($device->id, '2');
$dataWeekLastNow=History::getCountForLastPeriodUntilNow($device->id, '2');
$dataMonth=History::getCountForPeriod($device->id, '3');
//$dataMonthLast=History::getCountForLastPeriod($device->id, '3');
$dataMonthLastNow=History::getCountForLastPeriodUntilNow($device->id, '3');
$dataYear=History::getCountForPeriod($device->id, '4');
//$dataYearLast=History::getCountForLastPeriod($device->id, '4');
$dataYearLastNow=History::getCountForLastPeriodUntilNow($device->id, '4');

$percentDay=($dataDay/$dataDayLastNow)*100;
$percentWeek=($dataWeek/$dataWeekLastNow)*100;
$percentMonth=($dataMonth/$dataMonthLast)*100;
$percentYear=($dataYear/$dataYearLast)*100;
?>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-dashboard"></i>&nbsp;&nbsp;Tendances - <?php echo $device->name; ?></h4>
    </div>
    <div class="modal-body">
        <table class="table table-hover">
            <tr>
                <td></td>
                <th>Actuel</th>
                <th>Précédent</th>
                <td></td>
            </tr>
            <tr>
                <td>Jour</td>
                <td><?php echo $dataDay.$unite; ?></td>
                <td><?php echo $dataDayLastNow.$unite; ?></td>
                <td><?php echo number_format($percentDay,0)."%";?> &nbsp;&nbsp;<img src="assets/global/img/<?php if($percentDay > 100){ echo "in";}else{echo "de";} ?>crease.png" width="16" /></td>
            </tr>
            <tr>
                <td>Semaine</td>
                <td><?php echo $dataWeek.$unite; ?></td>
                <td><?php echo $dataWeekLastNow.$unite; ?></td>
                <td><?php echo number_format($percentWeek,0)."%";?> &nbsp;&nbsp;<img src="assets/global/img/<?php if($percentWeek > 100){ echo "in";}else{echo "de";} ?>crease.png" width="16" /></td>
            </tr>
            <tr>
                <td>Mois</td>
                <td><?php echo $dataMonth.$unite; ?></td>
                <td><?php echo $dataMonthLastNow.$unite; ?></td>
                <td><?php echo number_format($percentMonth,0)."%";?> &nbsp;&nbsp;<img src="assets/global/img/<?php if($percentMonth > 100){ echo "in";}else{echo "de";} ?>crease.png" width="16" /></td>
            </tr>
            <tr>
                <td>Année</td>
                <td><?php echo $dataMonth.$unite; ?></td>
                <td><?php echo $dataMonthLastNow.$unite; ?></td>
                <td><?php echo number_format($percentYear,0)."%";?> &nbsp;&nbsp;<img src="assets/global/img/<?php if($percentYear > 100){ echo "in";}else{echo "de";} ?>crease.png" width="16" /></td>
            </tr>
        </table>
        <div class="row">

        </div>
    </div>
    <!--<div class="modal-footer">
        <button type="button" class="btn btn-primary btnAddPageItem">Ajouter</button>
    </div>-->
</div>
            <!-- /.modal-content -->
<script src="assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    
});
</script>