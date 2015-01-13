<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js";
$includeJS[] = "/assets/global/plugins/jquery-knob/js/jquery.knob.js";

include "modules/header.php";
include "modules/sidebar.php";

include_once "models/History.php";
include_once "models/Device.php";
$GLOBALS["dbconnec"] = connectDB();

$sql="SELECT id,name,chart_formula, unite FROM device WHERE type='electricy'";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
$devicesTab = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $devicesTab[$row["id"]]=array(
        "name"=>$row["name"],
        "unity"=>$row["unite"],
        "chart_formula"=>$row["chart_formula"]
    );
}
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
$totalActual=$totalLast=0;
$totalMoneyActual=$totalMoneyLast=0;
$i=0;
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDay=History::getCountForPeriod($deviceId, '1');
    $totalActual +=$dataDay;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    echo $newLine;
    if(count($devicesTab) > 1) echo $deviceInfo["name"].": ";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$dataDay."</span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyActual += $money;
        }
        if(isset($money)){
            echo " soit ".  number_format($money, 2, ",", " ")."€";
        }
        //soit 2,54€";
    }
}
if(count($devicesTab) > 1){
    //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$totalActual." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
}
?>
                            
                            
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Hier</h4>
<?php 
$i=0;
$txt="";
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '1');
    $totalLast +=$dataDayLastNow;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    
    $txt.= $newLine;
    if(count($devicesTab) > 1) $txt.= $deviceInfo["name"].": ";
    $txt.=(count($devicesTab) <= 1) ? "<span style=\"font-variant:small-caps;font-size: larger;\">" : "";
    $txt.= $dataDayLastNow;
    $txt.=(count($devicesTab) <= 1) ? " </span> <span style=\"font-size:8px;\">" : "";
    $txt .= $deviceInfo["unity"];
    $txt.=(count($devicesTab) <= 1) ? " </span> " : "";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyLast += $money;
        }
        if(isset($money)){
            $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
        }
    }
    $i++;
}

$percent=($totalActual/$totalLast)*100;
$diffConso = ($percent > 100) ? ($percent-100) : (100-$percent); 
$diffConso = ($percent == 100) ? "0" : $diffConso;

$signConso = ($percent > 100) ? "+" : "-";
$signConso = ($percent == 100) ? "" : $signConso;

$colorConso = ($percent > 100) ? "red" : "blue";
$colorConso = ($percent == 100) ? "yellow" : $colorConso;

if(count($devicesTab) > 1){
    echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Hier\" style=\"font-variant:small-caps;font-size: larger;\">".$totalLast." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
} else {
    echo $txt;
}
?>
                        </div>
                        <div class="col-md-12">
                            <?php
                            if(count($devicesTab) > 1){
                                //echo "<button class=\"btn popovers\" data-trigger=\"hover\" data-placement=\"top\" data-content=\"".addslashes($txt)."\" data-original-title=\"Détails\">Détails</button>";
                            }
                            ?>
                            <div class="easy-pie-chart">
                                <div class="number transactions <?php echo $colorConso; ?>" data-percent="<?php echo round($diffConso,0); ?>" style="width:100px;height: 100px;line-height: 100px;">
                                    <span> <?php echo $signConso.round($diffConso,0); ?> %</span>
                                </div>
                            </div>
                        </div>
                    </div>
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
                            <h4 style="font-variant: small-caps;">Actuel</h4>
<?php 
$totalActual=$totalLast=0;
$totalMoneyActual=$totalMoneyLast=0;
$i=0;
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDay=History::getCountForPeriod($deviceId, '2');
    $totalActual +=$dataDay;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    echo $newLine;
    if(count($devicesTab) > 1) echo $deviceInfo["name"].": ";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$dataDay."</span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyActual += $money;
        }
        if(isset($money)){
            echo " soit ".  number_format($money, 2, ",", " ")."€";
        }
        //soit 2,54€";
    }
}
if(count($devicesTab) > 1){
    echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
}
?>
                            
                            
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Précédent</h4>
<?php 
$i=0;
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '2');
    $totalLast +=$dataDayLastNow;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    echo $newLine;
    if(count($devicesTab) > 1) echo $deviceInfo["name"].": ";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$dataDayLastNow." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyLast += $money;
        }
        if(isset($money)){
            echo " soit ".  number_format($money, 2, ",", " ")."€";
        }
    }
    $i++;
}

$percent=($totalActual/$totalLast)*100;
$diffConso = ($percent > 100) ? ($percent-100) : (100-$percent); 
$diffConso = ($percent == 100) ? "0" : $diffConso;

$signConso = ($percent > 100) ? "+" : "-";
$signConso = ($percent == 100) ? "" : $signConso;

$colorConso = ($percent > 100) ? "red" : "blue";
$colorConso = ($percent == 100) ? "yellow" : $colorConso;

if(count($devicesTab) > 1){
    echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
}
?>
                            
                        </div>
                        <div class="col-md-12">
                            <div class="easy-pie-chart">
                                <div class="number transactions <?php echo $colorConso; ?>" data-percent="<?php echo round($diffConso,0); ?>" style="width:100px;height: 100px;line-height: 100px;">
                                    <span> <?php echo $signConso.round($diffConso,0); ?> %</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- END PORTLET -->
    
        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-share font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase" style="text-transform:uppercase !important;"> Mensuel</span>
                        <span class="caption-helper"></span>
                    </div>
                    <div class="actions"></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Actuel</h4>
<?php 
$totalActual=$totalLast=0;
$totalMoneyActual=$totalMoneyLast=0;
$i=0;
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDay=History::getCountForPeriod($deviceId, '3');
    $totalActual +=$dataDay;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    echo $newLine;
    if(count($devicesTab) > 1) echo $deviceInfo["name"].": ";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$dataDay."</span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyActual += $money;
        }
        if(isset($money)){
            echo " soit ".  number_format($money, 2, ",", " ")."€";
        }
        //soit 2,54€";
    }
}
if(count($devicesTab) > 1){
    echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
}
?>
                            
                            
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Précédent</h4>
<?php 
$i=0;
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '3');
    $totalLast +=$dataDayLastNow;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    echo $newLine;
    if(count($devicesTab) > 1) echo $deviceInfo["name"].": ";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$dataDayLastNow." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyLast += $money;
        }
        if(isset($money)){
            echo " soit ".  number_format($money, 2, ",", " ")."€";
        }
    }
    $i++;
}

$percent=($totalActual/$totalLast)*100;
$diffConso = ($percent > 100) ? ($percent-100) : (100-$percent); 
$diffConso = ($percent == 100) ? "0" : $diffConso;

$signConso = ($percent > 100) ? "+" : "-";
$signConso = ($percent == 100) ? "" : $signConso;

$colorConso = ($percent > 100) ? "red" : "blue";
$colorConso = ($percent == 100) ? "yellow" : $colorConso;

if(count($devicesTab) > 1){
    echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
}
?>
                        </div>
                        <div class="col-md-12">
                            <div class="easy-pie-chart">
                                <div class="number transactions <?php echo $colorConso; ?>" data-percent="<?php echo round($diffConso,0); ?>" style="width:100px;height: 100px;line-height: 100px;">
                                    <span> <?php echo $signConso.round($diffConso,0); ?> %</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- END PORTLET -->
        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-share font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase" style="text-transform:uppercase !important;"> Annuel</span>
                        <span class="caption-helper"></span>
                    </div>
                    <div class="actions"></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Actuel</h4>
<?php 
$totalActual=$totalLast=0;
$totalMoneyActual=$totalMoneyLast=0;
$i=0;
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDay=History::getCountForPeriod($deviceId, '4');
    $totalActual +=$dataDay;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    echo $newLine;
    if(count($devicesTab) > 1) echo $deviceInfo["name"].": ";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$dataDay."</span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyActual += $money;
        }
        if(isset($money)){
            echo " soit ".  number_format($money, 2, ",", " ")."€";
        }
        //soit 2,54€";
    }
}
if(count($devicesTab) > 1){
    echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
}
?>
                            
                            
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-variant: small-caps;">Précédent</h4>
<?php 
$i=0;
foreach($devicesTab as $deviceId => $deviceInfo){
    //Récupération de l'historique
    $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '4');
    $totalLast +=$dataDayLastNow;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    echo $newLine;
    if(count($devicesTab) > 1) echo $deviceInfo["name"].": ";
    echo "<span style=\"font-variant:small-caps;font-size: larger;\">".$dataDayLastNow." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyLast += $money;
        }
        if(isset($money)){
            echo " soit ".  number_format($money, 2, ",", " ")."€";
        }
    }
    $i++;
}

$percent=($totalActual/$totalLast)*100;
$diffConso = ($percent > 100) ? ($percent-100) : (100-$percent); 
$diffConso = ($percent == 100) ? "0" : $diffConso;

$signConso = ($percent > 100) ? "+" : "-";
$signConso = ($percent == 100) ? "" : $signConso;

$colorConso = ($percent > 100) ? "red" : "blue";
$colorConso = ($percent == 100) ? "yellow" : $colorConso;

if(count($devicesTab) > 1){
    echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
}
?>
                        </div>
                        <div class="col-md-12">
                            <div class="easy-pie-chart">
                                <div class="number transactions <?php echo $colorConso; ?>" data-percent="<?php echo round($diffConso,0); ?>" style="width:100px;height: 100px;line-height: 100px;">
                                    <span> <?php echo $signConso.round($diffConso,0); ?> %</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- END PORTLET -->
    </div>
</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.easy-pie-chart .number.transactions.blue').easyPieChart({
        animate: 1000,
        size: 100,
        lineWidth: 4,
        barColor: Metronic.getBrandColor('blue')
    });
    $('.easy-pie-chart .number.transactions.red').easyPieChart({
        animate: 1000,
        size: 100,
        lineWidth: 4,
        barColor: Metronic.getBrandColor('red')
    });
    $('.easy-pie-chart .number.transactions.yellow').easyPieChart({
        animate: 1000,
        size: 100,
        lineWidth: 4,
        barColor: Metronic.getBrandColor('yellow')
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