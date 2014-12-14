<?php
$includeCSS = $includeJS = array();
$includeJS[]="/assets/admin/pages/scripts/components-dropdowns.js";
$includeJS[]="/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js";
$includeJS[]="/assets/global/plugins/select2/select2.min.js";
$includeJS[]="/assets/global/plugins/bootstrap-select/bootstrap-select.min.js";
$includeJS[] = "/assets/global/plugins/fuelux/js/spinner.min.js"; 

$includeCSS[] = "/assets/global/plugins/bootstrap-select/bootstrap-select.min.css";
$includeCSS[] = "/assets/global/plugins/select2/select2.css";
$includeCSS[] = "/assets/global/plugins/jquery-multi-select/css/multi-select.css";

include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Chart.php";
include_once "models/ChartDevice.php";
include_once "models/Device.php";

$devices=  Device::getDevicesByType();
$deviceTab=array();
foreach($devices as $device){
    $deviceTab[$device->type][]=$device;
}

$types=Chart::getTypes();
$periods=Chart::getPeriods();

$isPost=FALSE;
$chart=null;
if(isset($_POST["formname"]) && $_POST["formname"]=="editchart"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idchart"])){
    $name= $_POST["name"];
    $description= $_POST["description"];
    $type= $_POST["type"];
    $period= $_POST["period"];
    $from= $_POST["from"];
    $size= $_POST["size"];
    $abs= $_POST["abs"];
    $ord= $_POST["ord"];
    $idchart=$_POST["idchart"];
} else {
    $idchart=0;
    $txtMode="Création";
    $txtModeDesc="Création d'un graphique";
    if(isset($_GET["idChart"])){
        $idchart=$_GET["idChart"];
        $chart = Chart::getChart($idchart);
        $chartdevices = ChartDevice::getChartDeviceForChart($idchart);
        
        $_POST["my_multi_select2"]=array();
        foreach($chartdevices as $chartdevice){
            $_POST["my_multi_select2"][] =$chartdevice->deviceid;
        }
    } 
    $name= (!is_object($chart)) ? NULL : $chart->name;
    $description= (!is_object($chart)) ? NULL : $chart->description;
    $type= (!is_object($chart)) ? NULL : $chart->type;
    $period= (!is_object($chart)) ? NULL : $chart->period;
    $from= (!is_object($chart)) ? NULL : $chart->from;
    $size= (!is_object($chart)) ? NULL : $chart->size;
    $abs= (!is_object($chart)) ? NULL : $chart->abscisse;
    $ord= (!is_object($chart)) ? NULL : $chart->ordonne;
}
$from = ($from != "") ? str_replace("P", "", $from) : $from;
$from = ($from != "") ? str_replace("D", "", $from) : $from;

if($isPost){
    $_POST["active"] = ($_POST["active"] == "") ? 0 : $_POST["active"];
    $_POST["from"]=($_POST["from"] != "") ? "P".$_POST["from"]."D" : "";
    if($_POST["idchart"]>0){
        $sql="UPDATE chart SET name='".$_POST["name"]."', description='".$_POST["description"]."', type='".$_POST["type"]."', period='".$_POST["period"]."', froms='".$_POST["from"]."', size=".$_POST["size"].", abs='".$_POST["abs"]."', ord='".$_POST["ord"]."'";
        $sql.=" WHERE id=".$_POST["idchart"];
        $stmt = $GLOBALS["dbconnec"]->exec($sql);
        
        //Suppression des device associés au chart
        ChartDevice::deleteForChart($_POST["idchart"]);
        foreach($_POST["my_multi_select2"] as $deviceTmp){
            ChartDevice::createChartDevice($_POST["idchart"], $deviceTmp);
        }
        
        $info="La graphique a été modifié";
    } else {
        $chart=Chart::createChart($_POST["name"], $_POST["description"], $_POST["type"], $_POST["period"], $_POST["from"],$_POST["size"], $_POST["abs"],$_POST["ord"]);
        $idchart=$chart->id;
        foreach($_POST["my_multi_select2"] as $deviceTmp){
            ChartDevice::createChartDevice($idchart, $deviceTmp);
        }
        $info="Le graphique a été créé";
    }
}


if(isset($idchart) && $idchart > 0){
    $chartdevices = ChartDevice::getChartDeviceForChart($idchart);
    $txtMode="Edition";
    $txtModeDesc="Edition d'un graphique";
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
                Graphique				
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_chart.php';"><i class="fa fa-plus"></i>Ajouter un graphique</button>
                </li>
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_chart.php">Liste des graphiques</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?php echo $txtMode; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
            </ul>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" action="edit_chart.php">
                <div class="form-body form">
                <input type="hidden" name="formname" id="formname" value="editchart" />
                <input type="hidden" name="idchart" id="idchart" value="<?php echo $idchart; ?>" />
                <div class="row">
                        <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Paramètres</h3>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="name">Nom</label>
                                    <div class="col-md-9">
                                        <input id="name" name="name" class="form-control" value="<?php echo $name; ?>" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="description">Description</label>
                                    <div class="col-md-9">
                                        <input id="description" name="description" class="form-control" value="<?php echo $description; ?>" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="type">Type</label>
                                    <div class="col-md-9">
                                        <select name="type" id="type" class="form-control">
<?php
                                        foreach($types as $typeTmp){
                                            $selected = ($typeTmp==$type) ? " selected=\"selected\" " : "";
                                            echo "<option value=\"".$typeTmp."\" $selected>".ucwords($typeTmp)."</option>";
                                        }
?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="period">Période</label>
                                    <div class="col-md-9">
                                        <select name="period" id="period" class="form-control">
<?php
                                        foreach($periods as $key=>$periodTmp){
                                            $selected = ($key==$period) ? " selected=\"selected\" " : "";
                                            echo "<option value=\"".$key."\" $selected>".ucwords($periodTmp)."</option>";
                                        }
?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="from">Depuis</label>
                                    <div class="col-md-9">
                                        <input id="from" name="from" class="form-control" value="<?php echo $from; ?>" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="size">Taille</label>
                                    <div class="col-md-9">
                                        <div id="spinner1">
                                                <div class="input-group input-small">
                                                        <input type="text" name="size" id="spinner1" class="spinner-input form-control" maxlength="2" value="<?php echo $size; ?>" readonly>
                                                        <div class="spinner-buttons input-group-btn btn-group-vertical">
                                                                <button type="button" class="btn spinner-up btn-xs blue">
                                                                <i class="fa fa-angle-up"></i>
                                                                </button>
                                                                <button type="button" class="btn spinner-down btn-xs blue">
                                                                <i class="fa fa-angle-down"></i>
                                                                </button>
                                                        </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="abs">Abscisse</label>
                                    <div class="col-md-9">
                                        <input id="abs" name="abs" class="form-control" value="<?php echo $abs; ?>" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="ord">Ordonnée</label>
                                    <div class="col-md-9">
                                        <input id="ord" name="ord" class="form-control" value="<?php echo $ord; ?>" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group last">
                                        <label class="control-label col-md-3">Device</label>
                                        <div class="col-md-9">
                                                <select multiple="multiple" class="multi-select" id="my_multi_select2" name="my_multi_select2[]">
<?php
foreach($deviceTab as $type=>$deviceType){
    echo "<optgroup label=\"".ucwords($type)."\">";
    foreach($deviceType as $deviceTmp){
        $selected = (in_array($deviceTmp->id, $_POST["my_multi_select2"])) ? " selected=\"selected\" " : "";
        echo "<option value=\"".$deviceTmp->id."\" $selected>".ucwords($deviceTmp->name)."</option>";
    }
    echo "</optgroup>";
}
?>
                                                </select>
                                        </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="form-actions">
                    <button class="btn blue" deviceid="submit">
                        <i class="icon-ok"></i>Valider
                    </button>
                    <a href="admin_chart.php"><button class="btn" deviceid="button">Retourner</button></a>
                </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
<script deviceid="text/javascript">
    var ui="dropdown";
$(document).ready(function () {
    $('#spinner1').spinner({min: 3, max: 12});
});
</script>
<?php
include "modules/footer.php";
?>