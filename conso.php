<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js";
$includeJS[] = "/assets/global/plugins/jquery-knob/js/jquery.knob.js";
$includeJS[] = "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js";
$includeJS[] = "/assets/admin/pages/scripts/components-pickers.js";
$includeCSS[] = "/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css";

include "modules/header.php";
include "modules/sidebar.php";

include_once "models/History.php";
include_once "models/Device.php";
$GLOBALS["dbconnec"] = connectDB();

$months=array(
    1=>"Janvier",
    2=>"Février",
    3=>"Mars",
    4=>"Avril",
    5=>"Mai",
    6=>"Juin",
    7=>"Juillet",
    8=>"Août",
    9=>"Septembre",
    10=>"Octobre",
    11=>"Novembre",
    12=>"Décembre"
);

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="formconso"){
    $isPost=TRUE;
}

$_POST["period"] = ($isPost) ? $_POST["period"] : NULL;
$_POST["month"] = ($isPost) ? $_POST["month"] : date('n');
$_POST["year"] = ($isPost) ? $_POST["year"] : date('Y');

if($isPost){ 
    $title="Journalier"; 
    $typeNumber=""; 
    switch($_POST["period"]){
        case "day":
            $title="Journalier";
            $typeNumber='1';
            break;
        case "week":
            $title="Hebdomadaire";
            $typeNumber='2';
            break;
        case "month":
            $title="Mensuel";
            $typeNumber='3';
            break;
        case "year":
            $title="Annuel";
            $typeNumber='4';
            break;
        default:
    }
}

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

$sql="SELECT id,name,chart_formula, unite FROM device WHERE type='eau'";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
$devicesEau = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $devicesEau[$row["id"]]=array(
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
                <?php
                if(count($devicesEau) == 0){
                    echo "<small>Electriques</small>";
                }
                ?>
                <i class="fa fa-search btnSearch" style="cursor:pointer;"></i>
            </h3>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row divRecherche">
        <form class="form-horizontal" id="formConso" method="POST" action="conso.php">
            <input type="hidden" name="formname" id="formname" value="formconso" />
            <div class="form-body">
                <h4 class="form-section">Recherche</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3">Période</label>
                            <div class="col-md-9">
                                <select class="form-control" id="period" name="period">
                                    <option></option>
                                    <option value="day" <?php if($_POST["period"] == "day")echo " selected=\"selected\""; ?>>Jour</option>
                                    <option value="week" <?php if($_POST["period"] == "week")echo " selected=\"selected\""; ?>>Semaine</option>
                                    <option value="month" <?php if($_POST["period"] == "month")echo " selected=\"selected\""; ?>>Mois</option>
                                    <option value="year" <?php if($_POST["period"] == "year")echo " selected=\"selected\""; ?>>Année</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 divDay" style="<?php if($_POST["period"] != "day" && $_POST["period"] != "week")echo "display:none;"; ?>">
                        <div class="form-group">
                            <label class="control-label col-md-3">Jour</label>
                            <div class="col-md-9">
                                <input class="form-control form-control-inline input-medium date-picker" id="day" name="day" type="text" value="" size="16">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 divMonth" style="<?php if($_POST["period"] != "month")echo "display:none;"; ?>">
                        <div class="form-group">
                            <label class="control-label col-md-3">Mois</label>
                            <div class="col-md-9">
                                <select class="form-control" id="month" name="month">
                                    <?php 
                                    foreach($months as $idMonth=>$month){
                                        $selected = ($idMonth == $_POST["month"]) ? " selected=\"selected\" " : "";
                                        echo "<option value=\"".$idMonth."\" $selected>".$month."</option>";
                                    }
                                    ?>
                                </select>
                                <select class="form-control" id="year" name="year">
                                    <?php 
                                    $year=date('Y');
                                    $beginYear=$year - 5;
                                    while($beginYear<=$year){
                                        $selected = ($beginYear == $_POST["year"]) ? " selected=\"selected\" " : "";
                                        echo "<option value=\"".$beginYear."\" $selected>".$beginYear."</option>";
                                        $beginYear++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn green" type="submit">Rechercher</button>
                        </div>
                    </div>
                    <div class="col-md-6"> </div>
                </div>
            </div>
        </form>
    </div>
    <?php
    if(count($devicesEau) > 0){
    ?>
    <div class="tabbable-custom ">
    <?php
    }
        if(count($devicesEau) > 0){
        ?>
            <ul class="nav nav-tabs ">
                <li class="active">
                    <a href="conso.php#tab_electricite" data-toggle="tab">Electricité</a>
                </li>
                <li>
                    <a href="conso.php#tab_eau" data-toggle="tab">Eau</a>
                </li>
            </ul>
        <?php
        }
        ?>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_electricite">
                <div class="row">
                    <?php if($isPost){ ?>
                    <div class="col-md-12">
                        <div class="portlet light bg-inverse">
                            <div class="portlet-title">
                                <div class="caption font-red-sunglo">
                                    <i class="icon-share font-red-sunglo"></i>
                                    <span class="caption-subject bold uppercase" style="text-transform:uppercase !important;"> <?php echo $title; ?></span>
                                    <span class="caption-helper"></span>
                                </div>
                                <div class="actions"></div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Courant</h4>
<?php
$totalActual=$totalLast=0;
$totalMoneyActual=$totalMoneyLast=0;
$i=0;
//print_r($_POST["day"]);
foreach($devicesTab as $deviceId => $deviceInfo){
    $monthTmp=$yearTmp=NULL;
    if($_POST["period"] == "day" || $_POST["period"] == "week"){
        $monthTmp=$_POST["day"];
        $explTmp=explode("/",$_POST["day"]);
        $monthTmp=$explTmp[2]."-".$explTmp[0]."-".$explTmp[1];
    }
    if($_POST["period"] == "month"){
        $monthTmp=$_POST["month"];
        $yearTmp=$_POST["year"];
    }
    //Récupération de l'historique
    $dataDay=History::getCountForPeriodDate($deviceId, $typeNumber, $monthTmp, $yearTmp);
    $totalActual +=$dataDay;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;
    $txt.= $newLine;
    if(count($devicesTab) > 1) $txt.= $deviceInfo["name"].": ";
    $txt.= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
    if($deviceInfo["chart_formula"] != ""){
        $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
        @eval('$stateTemp='.$fonction.';');
        if(isset($stateTemp)){
            $money = $stateTemp."";
            $totalMoneyActual += $money;
        }
        if(isset($money)){
            $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
        }
    }
}

if(count($devicesTab) > 1){
    //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
    echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Aujourd'hui\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
} else {
    echo $txt;
}
?>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Précédent</h4>
                                        
<?php
$i=0;
$txt="";
foreach($devicesTab as $deviceId => $deviceInfo){
    $monthTmp=$yearTmp=NULL;
    if($_POST["period"] == "day" || $_POST["period"] == "week"){
        $monthTmp=$_POST["day"];
        $explTmp=explode("/",$_POST["day"]);
        $monthTmp=$explTmp[2]."-".$explTmp[0]."-".$explTmp[1];
    }
    if($_POST["period"] == "month"){
        $monthTmp=$_POST["month"];
        $yearTmp=$_POST["year"];
    }
    //Récupération de l'historique
    $dataDayLastNow=History::getCountForPeriodDateLast($deviceId, $typeNumber, $monthTmp, $yearTmp);
    $totalLast +=$dataDayLastNow;
    $newLine=($i>0) ? "<br/>" : "";
    $i++;

    $txt.= $newLine;
    if(count($devicesTab) > 1) $txt.= $deviceInfo["name"].": ";
    //$txt.=(count($devicesTab) <= 1) ? "<span style=\"font-variant:small-caps;font-size: larger;\">" : "";
    $txt.="<span style=\"font-variant:small-caps;font-size: larger;\">";
    $txt.= $dataDayLastNow;
    //$txt.=(count($devicesTab) <= 1) ? " </span> <span style=\"font-size:8px;\">" : "";
    $txt.=" </span> <span style=\"font-size:8px;\">";
    $txt .= $deviceInfo["unity"];
    //$txt.=(count($devicesTab) <= 1) ? " </span> " : "";
    $txt.=" </span> ";
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
?>
                                    </div>
<?php 
$percent=($totalActual/$totalLast)*100;
$diffConso = ($percent > 100) ? ($percent-100) : (100-$percent); 
$diffConso = ($percent == 100) ? "0" : $diffConso;

$signConso = ($percent > 100) ? "+" : "-";
$signConso = ($percent == 100) ? "" : $signConso;

$colorConso = ($percent > 100) ? "red" : "blue";
$colorConso = ($percent == 100) ? "yellow" : $colorConso;
?>
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
                    </div>                   
<?php                        } 
                    ?>
                    <?php if(!$isPost){ ?>
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
                $txt.= $newLine;
                if(count($devicesTab) > 1) $txt.= $deviceInfo["name"].": ";
                $txt.= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesTab) > 1){
                //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Aujourd'hui\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
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
                //$txt.=(count($devicesTab) <= 1) ? "<span style=\"font-variant:small-caps;font-size: larger;\">" : "";
                $txt.="<span style=\"font-variant:small-caps;font-size: larger;\">";
                $txt.= $dataDayLastNow;
                //$txt.=(count($devicesTab) <= 1) ? " </span> <span style=\"font-size:8px;\">" : "";
                $txt.=" </span> <span style=\"font-size:8px;\">";
                $txt .= $deviceInfo["unity"];
                //$txt.=(count($devicesTab) <= 1) ? " </span> " : "";
                $txt.=" </span> ";
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
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Hier\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
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
            $txt="";
            foreach($devicesTab as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDay=History::getCountForPeriod($deviceId, '2');
                $totalActual +=$dataDay;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt.= $newLine;
                if(count($devicesTab) > 1) $txt.= $deviceInfo["name"].": ";
                $txt.= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesTab) > 1){
                //echo $totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Actuel\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
            }
            ?>


                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Précédent</h4>
            <?php 
            $i=0;
            $txt="";
            foreach($devicesTab as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDayLast=History::getCountForLastPeriod($deviceId, '2');
                $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '2');
                $totalLast +=$dataDayLastNow;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesTab) > 1) $txt .= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDayLastNow,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyLast += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
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
            //    echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Précédent\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
            } else {
                echo $txt;
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
            $txt ="";
            foreach($devicesTab as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDay=History::getCountForPeriod($deviceId, '3');
                $totalActual +=$dataDay;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt.= $newLine;
                if(count($devicesTab) > 1) $txt.= $deviceInfo["name"].": ";
                $txt.= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesTab) > 1){
                //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Actuel\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
            }
            ?>


                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Précédent</h4>
            <?php 
            $i=0;
            $txt="";
            foreach($devicesTab as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '3');
                $totalLast +=$dataDayLastNow;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesTab) > 1) $txt .= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDayLastNow,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyLast += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
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
                //echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Précédent\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
            } else {
                echo $txt;
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
            $txt="";
            foreach($devicesTab as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDay=History::getCountForPeriod($deviceId, '4');
                $totalActual +=$dataDay;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesTab) > 1) $txt.= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesTab) > 1){
                //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Actuel\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
            }
            ?>    
                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Précédent</h4>
            <?php 
            $i=0;
            $txt="";
            foreach($devicesTab as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '4');
                $totalLast +=$dataDayLastNow;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesTab) > 1) $txt .= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDayLastNow,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyLast += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
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
                //echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Précédent\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
            } else {
                echo $txt;
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
                    <?php } ?>
                </div><!-- DIV ROW-->
            </div><!-- DIV TAB ELECTRICITE-->
            
            <div class="tab-pane" id="tab_eau">
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
            $txt="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDay=History::getCountForPeriod($deviceId, '1');
                $totalActual +=$dataDay;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt.= $newLine;
                if(count($devicesEau) > 1) $txt.= $deviceInfo["name"].": ";
                $txt.= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesEau) > 1){
                //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Aujourd'hui\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
            }
            ?>


                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Hier</h4>
            <?php 
            $i=0;
            $txt="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '1');
                $totalLast +=$dataDayLastNow;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;

                $txt.= $newLine;
                if(count($devicesEau) > 1) $txt.= $deviceInfo["name"].": ";
                //$txt.=(count($devicesTab) <= 1) ? "<span style=\"font-variant:small-caps;font-size: larger;\">" : "";
                $txt.="<span style=\"font-variant:small-caps;font-size: larger;\">";
                $txt.= $dataDayLastNow;
                //$txt.=(count($devicesTab) <= 1) ? " </span> <span style=\"font-size:8px;\">" : "";
                $txt.=" </span> <span style=\"font-size:8px;\">";
                $txt .= $deviceInfo["unity"];
                //$txt.=(count($devicesTab) <= 1) ? " </span> " : "";
                $txt.=" </span> ";
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

            if(count($devicesEau) > 1){
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Hier\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
            } else {
                echo $txt;
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
            $txt="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDay=History::getCountForPeriod($deviceId, '2');
                $totalActual +=$dataDay;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt.= $newLine;
                if(count($devicesEau) > 1) $txt.= $deviceInfo["name"].": ";
                $txt.= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesEau) > 1){
                //echo $totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Actuel\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
            }
            ?>


                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Précédent</h4>
            <?php 
            $i=0;
            $txt="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDayLast=History::getCountForLastPeriod($deviceId, '2');
                $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '2');
                $totalLast +=$dataDayLastNow;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesEau) > 1) $txt .= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDayLastNow,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyLast += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
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

            if(count($devicesEau) > 1){
            //    echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Précédent\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
            } else {
                echo $txt;
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
            $txt ="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDay=History::getCountForPeriod($deviceId, '3');
                $totalActual +=$dataDay;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt.= $newLine;
                if(count($devicesEau) > 1) $txt.= $deviceInfo["name"].": ";
                $txt.= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt.= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesEau) > 1){
                //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Actuel\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
            }
            ?>


                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Précédent</h4>
            <?php 
            $i=0;
            $txt="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '3');
                $totalLast +=$dataDayLastNow;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesEau) > 1) $txt .= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDayLastNow,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyLast += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
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

            if(count($devicesEau) > 1){
                //echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Précédent\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
            } else {
                echo $txt;
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
            $txt="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDay=History::getCountForPeriod($deviceId, '4');
                $totalActual +=$dataDay;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesEau) > 1) $txt.= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDay,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDay, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyActual += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
                    }
                    //soit 2,54€";
                }
            }
            if(count($devicesEau) > 1){
                //echo "<br/>Total: ".$totalActual.$deviceInfo["unity"]." soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Actuel\"  style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalActual,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyActual, 2, ",", " ")."€";
            } else {
                echo $txt;
            }
            ?>    
                                    </div>
                                    <div class="col-md-6">
                                        <h4 style="font-variant: small-caps;">Précédent</h4>
            <?php 
            $i=0;
            $txt="";
            foreach($devicesEau as $deviceId => $deviceInfo){
                //Récupération de l'historique
                $dataDayLastNow=History::getCountForLastPeriodUntilNow($deviceId, '4');
                $totalLast +=$dataDayLastNow;
                $newLine=($i>0) ? "<br/>" : "";
                $i++;
                $txt .= $newLine;
                if(count($devicesEau) > 1) $txt .= $deviceInfo["name"].": ";
                $txt .= "<span style=\"font-variant:small-caps;font-size: larger;\">".number_format($dataDayLastNow,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span>";
                if($deviceInfo["chart_formula"] != ""){
                    $fonction = str_replace("x", $dataDayLastNow, $deviceInfo["chart_formula"]);
                    @eval('$stateTemp='.$fonction.';');
                    if(isset($stateTemp)){
                        $money = $stateTemp."";
                        $totalMoneyLast += $money;
                    }
                    if(isset($money)){
                        $txt .= " soit ".  number_format($money, 2, ",", " ")."€";
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

            if(count($devicesEau) > 1){
                //echo "<br/>Total: ".$totalLast.$deviceInfo["unity"]." soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
                echo "<span class=\"popovers\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-content='".  addslashes($txt)."' data-original-title=\"Précédent\" style=\"cursor:pointer;font-variant:small-caps;font-size: larger;\">".number_format($totalLast,0,","," ")." </span> <span style=\"font-size:8px;\">".$deviceInfo["unity"]."</span> soit ".number_format($totalMoneyLast, 2, ",", " ")."€";
            } else {
                echo $txt;
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
                </div><!-- DIV ROW-->
            </div><!-- DIV TAB EAU-->
        </div><!-- DIV CONTENT-->
    <?php
    if(count($devicesEau) > 0){
    ?>
    </div><!-- DIV TABBABLE CUSTOM -->
    <?php
    }
    ?>
</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.divRecherche').hide();
    //$('.divDay').hide();
    //$('.divMonth').hide();
    $('.btnSearch').click(function(){
        $('.divRecherche').toggle();
    });
    $('#period').change(function(){
        if($(this).val() == "day") {
            $('.divDay').show();
            $('.divMonth').hide();
        }
        if($(this).val() == "week") {
            $('.divDay').show();
            $('.divMonth').hide();
        }
        if($(this).val() == "month"){
            $('.divMonth').show();
            $('.divDay').hide();
        } 
    });
    
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
