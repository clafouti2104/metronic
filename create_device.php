<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include "models/Device.php";

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="editdevice"){
    $isPost=TRUE;
}

$idDevice=(isset($_GET["idDevice"])) ? $_GET["idDevice"] : $_POST["iddevice"];

$device = Device::getDevice($idDevice);
$types=Device::getTypes();
$models=Device::getModels();

$name= ($isPost) ? $_POST["name"] : $device->name;
$type= ($isPost) ? $_POST["type"] : $device->type;
$model= ($isPost) ? $_POST["model"] : $device->model;
$ipAddress= ($isPost) ? $_POST["ipaddress"] : $device->ip_address;

if($isPost){
    /*$sql="UPDATE device SET name='".$_POST["name"]."', type='".$_POST["type"]."', model='".$_POST["model"]."', ip_address='".$_POST["ipaddress"]."' WHERE id=".$_POST["iddevice"];
    $stmt = $GLOBALS["dbconnec"]->exec($sql);*/
}
?>
<!-- BEGIN PAGE -->
<div class="page-content">
    <div class="container-fluid">
    <div class="row-fluid">
            <div class="span12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    Device				
                    <small>Edition d'un device</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="index.php">Admin</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="admin_device.php">Device</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        Edition
                        <i class="icon-angle-right"></i>
                    </li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <form class="horizontal-form" method="POST" action="edit_device.php">
                <input type="hidden" name="formname" id="formname" value="editdevice" />
                <input type="hidden" name="iddevice" id="iddevice" value="<?php echo $device->id; ?>" />
                <div class="row-fluid">
                    <div class="span6 ">
                        <div class="control-group">
                            <label class="control-label" for="name">Nom</label>
                            <div class="controls">
                                <input id="name" name="name" class="m-wrap span12" value="<?php echo $name; ?>" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="span6 ">
                        <div class="control-group">
                            <label class="control-label" for="ipaddress">Adresse IP</label>
                            <div class="controls">
                                <input id="ipaddress" name="ipaddress" class="m-wrap span12" value="<?php echo $ipAddress; ?>" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 ">
                        <div class="control-group">
                            <label class="control-label" for="type">Type</label>
                            <div class="controls">
                                <select id="type" name="type" class="m-wrap span12">
                                    <option value="-1"></option>
<?php
foreach($types as $tmpType){
    $selected=(strtolower($tmpType) == strtolower($type)) ? " selected=\"selected\" " : "";
    echo "<option value=\"".$tmpType."\"$selected>".ucwords($tmpType)."</option>";
}
?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="span6 ">
                        <div class="control-group">
                            <label class="control-label" for="model">Modèle</label>
                            <div class="controls">
                                <select id="model" name="model" class="m-wrap span12">
                                    <option value="-1"></option>
<?php
foreach($models as $tmpModel){
    $selected=(strtolower($tmpModel) == strtolower($model)) ? " selected=\"selected\" " : "";
    echo "<option value=\"".$tmpModel."\"$selected>".ucwords($tmpModel)."</option>";
}
?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="form-actions">
                    <button class="btn blue" type="submit">
                        <i class="icon-ok"></i>Valider
                    </button>
                    <a href="admin_device.php"><button class="btn" type="button">Retourner</button></a>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
<?php
include "modules/footer.php";
?>