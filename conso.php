<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js";
$includeJS[] = "/assets/global/plugins/jquery-knob/js/jquery.knob.js";

include "modules/header.php";
include "modules/sidebar.php";

include_once "models/History.php";
include_once "models/Device.php";
$GLOBALS["dbconnec"] = connectDB();

$sql="SELECT id,name FROM device WHERE type='electricy'";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
$devicesTab = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $devicesTab[$row["id"]]=$row["name"];
}
print_r($devicesTab);
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                Suivi des consommations
                <small>Electriques</small>
            </h3>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-share font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase" style="text-transform:uppercase !important;"> Journalier</span>
                        <span class="caption-helper"></span>
                    </div>
                    <div class="actions"></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-6">
                            <!--<p class="text-center"> Aujourd'hui </p>-->
                            <h4 style="font-variant: small-caps;">Aujourd'hui</h4>
<?php 
foreach($devicesTab as $deviceId => $deviceName){
    //Récupération de l'historique
    $dataDay=History::getCountForPeriod($deviceId, '1');
    $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '1');
}
?>
                            <span style="font-variant:small-caps;font-size: larger;"><?php echo $dataDay; ?></span> Wh<br/>
                            soit 2,54€
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Hier</h4>
                            <span style="font-variant:small-caps;font-size: larger;"><?php echo $dataDayLastNow; ?></span> Wh<br/>
                            soit 2,54€
                        </div>
                    </div>
                    <!--<div class="easy-pie-chart">
                        <div class="number transactions" data-percent="75" style="width:100px;height: 100px;line-height: 100px;">
                            <span> +75 %</span>
                        </div>
                    </div>-->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-share font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase" style="text-transform:uppercase !important;"> Hebdomadaire</span>
                        <span class="caption-helper"></span>
                    </div>
                    <div class="actions"></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-6">
                            <!--<p class="text-center"> Aujourd'hui </p>-->
                            <h4 style="font-variant: small-caps;">Actuel</h4>
<?php 
foreach($devicesTab as $deviceId => $deviceName){
    //Récupération de l'historique
    $dataDay=History::getCountForPeriod($deviceId, '2');
    $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '2');
}
?>
                            <?php echo $dataDay; ?> Wh<br/>
                            soit 2,54€
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Précédent</h4>
                            <?php echo $dataDayLastNow; ?> Wh<br/>
                            soit 2,54€
                        </div>
                    </div>
                    <!--<div class="easy-pie-chart">
                        <div class="number transactions" data-percent="75" style="width:100px;height: 100px;line-height: 100px;">
                            <span> +75 %</span>
                        </div>
                    </div>-->
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <input type="text" value="75" class="dial">
        </div>
    </div>
</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.easy-pie-chart .number.transactions').easyPieChart({
        animate: 1000,
        size: 100,
        lineWidth: 4,
        barColor: Metronic.getBrandColor('blue')
    });
    $(".dial").knob();
    // general knob
            $(".knob").knob({
                'dynamicDraw': true,
                'thickness': 0.2,
                'tickColorizeValues': true,
                'skin': 'tron'
            });  
    /*$('.easy-pie-chart .number').each(function () {
        var newValue = Math.floor(100 * Math.random());
        $(this).data('easyPieChart').update(newValue);
        $('span', this).text(newValue);
    });*/
});
</script>
<?php
include "modules/footer.php";
?>