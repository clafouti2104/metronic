<?php
$includeJS=$includeCSS=array();
$includeJS[] = "/assets/global/plugins/jquery.blockui.min.js";   

include "modules/header.php";
include "modules/sidebar.php";
$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();
include_once "models/Device.php";
include_once "models/MessageDevice.php";
include_once "models/Product.php";

$error="";
$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="editdevice"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["iddevice"])){
    $name= $_POST["name"];
    $type= $_POST["type"];
    $product= $_POST["product"];
    $ipAddress= $_POST["ipaddress"];
    $idDevice=$_POST["iddevice"];
    $active=$_POST["active"];
    $unite=$_POST["unite"];
    $dataType=$_POST["dataType"];
    //$incremental=$_POST["incremental"];
    $incremental=(isset($_POST["incremental"]) && $_POST["incremental"] != "") ? $_POST["incremental"] : NULL;
    $alertMinute=(isset($_POST["alertMinute"]) && $_POST["alertMinute"] != "") ? $_POST["alertMinute"] : NULL;
    $collect=$_POST["collect"];
    $stateFormula=$_POST["stateFormula"];
    $chartFormula=$_POST["chartFormula"];
    $stateResults=$_POST["state_results"];
    $txtMode=($idDevice==0) ? "Création" : "Edition";
    $txtModeDesc=($idDevice==0) ? "Création d'un device" : "Edition d'un device";
    
    $_POST["param1"]= (isset($_POST["param1"])) ? $_POST["param1"] : NULL;
    $_POST["param2"]= (isset($_POST["param2"])) ? $_POST["param2"] : NULL;
    $_POST["param3"]= (isset($_POST["param3"])) ? $_POST["param3"] : NULL;
    $_POST["param4"]= (isset($_POST["param4"])) ? $_POST["param4"] : NULL;
    $_POST["param5"]= (isset($_POST["param5"])) ? $_POST["param5"] : NULL;
} else {
    $idDevice=0;
    $txtMode="Création";
    $txtModeDesc="Création d'un device";
    if(isset($_GET["idDevice"])){
        $idDevice=$_GET["idDevice"];
        $device = Device::getDevice($idDevice);
    } 
    
    $stateResults = (!isset($device) || !is_object($device)) ? NULL : $device->state_results;
    $stateParameters = (!isset($device) || !is_object($device)) ? NULL : $device->state_parameters;
    $stateFormula = NULL;
    if(!is_null($stateParameters)){
        $jsonParameters = json_decode($stateParameters);
        $stateFormula = (isset($jsonParameters->formula)) ? $jsonParameters->formula : $stateFormula;
    }
    $name= (!isset($device) || !is_object($device)) ? NULL : $device->name;
    $type= (!isset($device) || !is_object($device)) ? NULL : $device->type;
    $product= (!isset($device) || !is_object($device)) ? NULL : $device->product_id;
    $ipAddress= (!isset($device) || !is_object($device)) ? NULL : $device->ip_address;
    $active= (!isset($device) || !is_object($device)) ? TRUE : $device->active;
    $incremental= (!isset($device) || !is_object($device)) ? FALSE : $device->incremental;
    $alertMinute= (!isset($device) || !is_object($device)) ? NULL : $device->alert_lost_communication;
    $collect= (!isset($device) || !is_object($device)) ? NULL : $device->collect;
    $unite= (!isset($device) || !is_object($device)) ? NULL : $device->unite;
    $dataType= (!isset($device) || !is_object($device)) ? NULL : $device->data_type;
    $chartFormula= (!isset($device) || !is_object($device)) ? NULL : $device->chart_formula;
    $_POST["param1"]= (!isset($device) || !is_object($device)) ? NULL : $device->param1;
    $_POST["param2"]= (!isset($device) || !is_object($device)) ? NULL : $device->param2;
    $_POST["param3"]= (!isset($device) || !is_object($device)) ? NULL : $device->param3;
    $_POST["param4"]= (!isset($device) || !is_object($device)) ? NULL : $device->param4;
    $_POST["param5"]= (!isset($device) || !is_object($device)) ? NULL : $device->param5;
}

$types=Device::getTypes();
$models=Device::getModels();
$dataTypes=Device::getDataTypes();
$products=Product::getProducts();
$_POST["incremental"]=$incremental;
//Recupere notification pushing box
$notifications=array();
$sqlNotifications = "SELECT * FROM config WHERE name='pushing_box'";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlNotifications);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $notifications[] = array(
        "id"=>$row["id"],
        "name"=>$row["comment"],
        "deviceId"=>$row["value"]
    );
}

if($isPost){
    //Controle
    if($_POST["name"] == ""){
        $error="Veuillez renseigner le nom";
    }
    
    if($error==""){
        $_POST["active"] = ($_POST["active"] == "") ? 0 : $_POST["active"];
        $_POST["incremental"] = (!isset($_POST["incremental"]) || $_POST["incremental"] == "") ? 0 : $_POST["incremental"];
        $_POST["collect"] = ($_POST["collect"] == "") ? 0 : $_POST["collect"];
        $_POST["type"] = ($_POST["type"] == "-1") ? NULL : $_POST["type"];
        $alertMinute = ($alertMinute == "") ? 0 : $alertMinute;
        
        $stateParameters = NULL;
        if($stateFormula != "" && !is_null($stateFormula)){
            $stateParameters = json_encode(array(
                "formula"=>$stateFormula
            ));
        }
        
        //$_POST["alert_lost_communication"] = (!isset($_POST["alert_lost_communication"]) || $_POST["alert_lost_communication"] == "") ? NULL : $_POST["alert_lost_communication"];
        if($_POST["iddevice"]>0){
            $sql="UPDATE device SET name='".$_POST["name"]."', type='".$_POST["type"]."', product_id=".$_POST["product"].",ip_address='".$_POST["ipaddress"]."', active=".$_POST["active"].", incremental=".$_POST["incremental"];
            $sql.=", alert_lost_communication='".$alertMinute."', param1='".$_POST["param1"]."', param2='".$_POST["param2"]."', param3='".$_POST["param3"]."', param4='".$_POST["param4"]."', param5='".$_POST["param5"]."'";
            $sql.=", collect='".$_POST["collect"]."', unite='".utf8_encode($_POST["unite"])."', data_type=".$_POST["dataType"].", state_parameters='".$stateParameters."', state_results='".addslashes($stateResults)."' ";
            $sql.=", chart_formula='".$_POST["chartFormula"]."'";
            $sql.=" WHERE id=".$_POST["iddevice"];
            //echo $sql;
            $stmt = $GLOBALS["dbconnec"]->exec($sql);
            $info="Le device a été modifié";
        } else {
            $device=Device::createDevice($_POST["name"], $_POST["type"], NULL, NULL, NULL, $_POST["ipaddress"], NULL, $_POST["active"],NULL,$alertMinute,NULL,$_POST["product"],$_POST["param1"],$_POST["param2"],$_POST["param3"],$_POST["param4"],$_POST["param5"],$_POST["collect"], $_POST["incremental"],$_POST["unite"],$_POST["dataType"],$stateParameters, $stateResults,$_POST["chartFormula"]);
            $idDevice=$device->id;
            $info="Le device a été modifié";
        }
        if($_POST["incremental"] > 0){
            $sqlTable = "CREATE TABLE IF NOT EXISTS releve_".date('Y_m')."_d".$row["id"]." (id MEDIUMINT NOT NULL AUTO_INCREMENT, value FLOAT, date DATETIME,PRIMARY KEY (id))";
            $stmt = $GLOBALS["dbconnec"]->exec($sqlTable);
        }   
        if($_POST["collect"] > 0){
            $sqlInsert="CREATE TABLE IF NOT EXISTS temperature".date('Y_m')."_d".$row["id"]."(
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `value` double NOT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `calaosid` int(11) DEFAULT NULL) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
            $stmt = $GLOBALS["dbconnec"]->exec($sqlInsert);
        }   
    }
}

if($idDevice > 0){
    //Recuperation des messages du device
    $messages=MessageDevice::getMessageDevicesForDevice($idDevice);
    $txtMode="Edition";
    $txtModeDesc="Edition d'un device";
}

//Chargement des paramètres du produit
if($product != ""){
    $productObj = Product::getProduct($product);
    if(is_object($productObj) && $productObj->configuration != ""){
        $params=json_decode($productObj->configuration);
    }
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
                Device				
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <!--<li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_device.php';"><i class="fa fa-plus"></i>Ajouter un device</button>
                </li>-->
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_device.php">Liste des devices</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?php echo $txtMode; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
            </ul>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <?php if($error!=""){echo "<div class=\"alert alert-danger\">".$error."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <form class="form-horizontal" method="POST" action="edit_device.php">
    <div class="portlet">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gears"></i>
                Gestion
            </div>
            <div class="actions btn-set">
                <a href="admin_device.php" class="btn default">
                    <i class="fa fa-angle-left"></i>
                    Retour
                </a>
                <button class="btn green" type="submit">
                    <i class="fa fa-check"></i>
                    Valider
                </button>
                <a href="edit_device.php" class="btn blue">
                    <i class="fa fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
    </div>
    <div class="tabbable-custom ">
        <ul class="nav nav-tabs ">
            <li class="active">
                <a href="edit_device.php#tab_general" data-toggle="tab">Général</a>
            </li>
            <?php if($idDevice != "" && $idDevice > 0){ ?>
            <li>
                <a href="edit_device.php#tab_command" data-toggle="tab">Commandes</a>
            </li>
            <li>
                <a href="edit_device.php#tab_affichage" data-toggle="tab">Affichage</a>
            </li>
            <li>
                <a href="edit_device.php#tab_alert" class="btnTabAlerts" data-toggle="tab">Alertes</a>
            </li>
            <li>
                <a href="edit_device.php#tab_historique" class="btnTabLogs" type="historique" data-toggle="tab">Historique</a>
            </li>
            <?php if($_POST["incremental"] != "0" && $_POST["incremental"] != ""){ ?>
            <li>
                <a href="edit_device.php#tab_releve" class="btnTabLogs" type="consommation" data-toggle="tab">Relevé</a>
            </li>
            <?php } ?>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_general">
                    <div class="form-body form">
                        <input type="hidden" name="formname" id="formname" value="editdevice" />
                        <input type="hidden" name="iddevice" id="iddevice" value="<?php echo $idDevice; ?>" />
                        <div class="row">
                            <div class="col-md-7 ">
                                <!--<h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Paramètres</h3>-->
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
                                            <label class="control-label col-md-3" for="ipaddress">Adresse IP</label>
                                            <div class="col-md-9">
                                                <input id="ipaddress" name="ipaddress" class="form-control" value="<?php echo $ipAddress; ?>" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="collect">Historiser</label>
                                            <div class="col-md-9">
                                                <input id="collect" name="collect" class="form-control" value="<?php echo $collect; ?>" type="text">
                                                <span class="help-block">toutes les x minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="active">Actif</label>
                                            <div class="col-md-9">
                                                <input id="active" name="active" class="form-control" value="1" <?php if($active){echo " checked=\"checked\"";} ?> type="checkbox">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="alertMinute">Alerte après </label>
                                            <div class="col-md-9">
                                                <input id="alertMinute" name="alertMinute" class="form-control" value="<?php echo $alertMinute; ?>" type="text">
                                                <span class="help-block"> minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="incremental">Incremental</label>
                                            <div class="col-md-9">
                                                <input id="incremental" name="incremental" class="form-control" value="1" <?php if($incremental){echo " checked=\"checked\"";} ?> type="checkbox">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if(isset($params)){
                                    $i=1;
                                    foreach($params as $key=>$param){
                                        echo "<div class=\"row\">";
                                        echo "<div class=\"col-md-12\">";
                                        echo "<div class=\"form-group\">";
                                        echo "<label class=\"control-label col-md-3\" for=\"".$key."\">".ucwords(str_replace("_"," ",$param))."</label>";
                                        echo "<div class=\"col-md-9\">";
                                        echo "<input id=\"".$key."\" name=\"".$key."\" class=\"form-control\" value=\"".$_POST["param".$i]."\" type=\"text\" />";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        $i++;
                                    }
                                }
                                ?>
                            </div>
                            <div class="col-md-5 ">
                                <!--<h3 class="form-section"><i class="fa fa-info-circle"></i>&nbsp;Informations</h3>-->
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="product">Equipement</label>
                                            <div class="col-md-9">
                                                <select id="product" name="product" class="form-control">
                                                    <option value="-1"></option>
                <?php
                foreach($products as $tmpProduct){
                    $selected=($tmpProduct->id == $product) ? " selected=\"selected\" " : "";
                    echo "<option value=\"".$tmpProduct->id."\"$selected>".ucwords(str_replace("_", " ", $tmpProduct->name))."</option>";
                }
                ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="type">Type</label>
                                            <div class="col-md-9">
                                                <select id="type" name="type" class="form-control">
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
                                </div>
                                    <?php if(isset($device) && is_object($device)){ ?>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Dernière Communication</label>
                                            <div class="col-md-9">
                                                <label class="label label-info"><?php $lastUpdate="";if(!is_null($device->last_update)){$lastUpdate=new DateTime($device->last_update);$lastUpdate=$lastUpdate->format('d-m-Y H:i:s');}echo $lastUpdate; ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <?php } ?>
                                <?php if(isset($device) && is_object($device)){ ?>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Etat</label>
                                            <div class="col-md-9">
                                                <label class="label label-info"><?php echo $device->state; ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <?php } ?>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="tab-pane" id="tab_command">
                <div class="form-body form">
                        <div class="row">
                            <div class="col-md-12">
                            <h3 class="form-section"><i class="fa fa-cogs"></i>&nbsp;Commandes
                            &nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="modal" href="edit_device.php#editMessageDevice" class="btn btn-primary btnAddMessage" type="button"><i class="fa fa-plus"></i>&nbsp;Ajouter une commande</a>
                            </h3>
        <?php 
        if(isset($messages)){
        ?>
                                <table class="table table-striped table-hover">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Commande</th>
                                        <th>Type</th>
                                        <th></th>
                                    </tr>
        <?php
        foreach($messages as $messageTmp){
            echo "<tr id=\"line-".$messageTmp->id."\">";
            echo "<td>#".$messageTmp->id."</td>";
            echo "<td>".ucwords($messageTmp->name)."</td>";
            echo "<td>".$messageTmp->command."</td>";
            echo "<td>".$messageTmp->type."</td>";
            echo "<td><i class=\"fa fa-play btnPlayMessage\" title=\"Tester\" idMessage=\"".$messageTmp->id."\" style=\"cursor:pointer;\"></i>&nbsp;&nbsp;";
            echo "<a href=\"edit_device.php#editMessageDevice\" data-toggle=\"modal\" ><i class=\"fa fa-edit btnEditMessage\" title=\"Editer\" idMessage=\"".$messageTmp->id."\" style=\"cursor:pointer;color:black;\"></i></a>&nbsp;&nbsp;";
            echo "<a href=\"edit_device.php#deleteMessageDevice\" data-toggle=\"modal\" ><i class=\"fa fa-trash-o btnDeleteMessage\" title=\"Supprimer\" idMessage=\"".$messageTmp->id."\" style=\"cursor:pointer;color:black;\"></i></a></td>";
            echo "</tr>";
        }
        ?>
                                </table>
        <?php
        }
        ?>
                            </div>
                        </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_affichage">
                <div class="form-body form">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="form-section"><i class="fa fa-rocket"></i>&nbsp;Paramètre d'affichage</h3>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="unite">Unité</label>
                                        <div class="col-md-9">
                                            <input id="unite" name="unite" class="form-control" value="<?php echo utf8_decode($unite); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                                <!--<div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="usage">Usage</label>
                                        <div class="col-md-9">
                                            <input id="usage" name="usage" class="form-control" value="" type="text" disabled="">
                                        </div>
                                    </div>
                                </div>-->
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="dataType">Type de données</label>
                                        <div class="col-md-9">
                                            <select id="dataType" name="dataType" class="form-control">
                                                <?php
                                                foreach($dataTypes as $tmpDataType=>$labelDataType){
                                                    $selected=(strtolower($tmpDataType) == strtolower($dataType)) ? " selected=\"selected\" " : "";
                                                    echo "<option value=\"".$tmpDataType."\"$selected>".ucwords($labelDataType)."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="form-section"><i class="fa fa-superscript "></i>&nbsp;Traitement des résultats</h3>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="stateFormula">Formule</label>
                                        <div class="col-md-9">
                                            <input id="stateFormula" name="stateFormula" class="form-control" value="<?php echo $stateFormula; ?>" type="text">
                                            <span class="help-block">Exemple: x + 12</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="chartFormula">Formule Graphique</label>
                                        <div class="col-md-9">
                                            <input id="chartFormula" name="chartFormula" class="form-control" value="<?php echo $chartFormula; ?>" type="text">
                                            <span class="help-block">Exemple: x + 12 (valeur calculée dans les graphiques)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="state_results">
                                            Etats Virtuels 
                                            <button class="btn btn-sm default btnAddStateResult" data-dismiss="modal" type="button"><i class="fa fa-plus"></i></button>
                                        </label>
                                        <div class="col-md-9">
                                            <input id="state_results" name="state_results" class="form-control" value="<?php echo $stateResults; ?>" type="hidden">
                                            <table class="table table-condensed table-hover tableStateResult">
                                                <tr>
                                                    <th>Valeur Brute</th>
                                                    <th>Affectée</th>
                                                </tr>
                                                <?php
                                                if(is_null($stateResults) || $stateResults == ""){
                                                ?>
                                                <tr>
                                                    <td><input type="text" id="stateResultBrute1" class="form-control stateResultInput stateResultBrute" value="" /></td>
                                                    <td><input type="text" id="stateResultAffecte1" class="form-control stateResultInput stateResultAffecte" value="" /></td>
                                                </tr>
                                                <?php
                                                } else {
                                                    $jsonParams = json_decode($stateResults);
                                                    $i=1;
                                                    foreach($jsonParams as $key=>$jsonParam){
                                                        echo "<tr>";
                                                        echo '<td><input type="text" id="stateResultBrute'.$i.'" class="form-control stateResultInput stateResultBrute" value="'.$key.'" /></td>';
                                                        echo '<td><input type="text" id="stateResultAffecte'.$i.'" class="form-control stateResultInput stateResultAffecte" value="'.$jsonParam.'" /></td>';
                                                        echo "</tr>";
                                                        $i++;
                                                    }
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_alert">
                <div class="form-body form">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="form-section"><i class="fa fa-rocket"></i>&nbsp;Alertes
                            &nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="modal" href="edit_device.php#editAlert" class="btn btn-primary btnAddAlert" type="button"><i class="fa fa-plus"></i>&nbsp;Ajouter une alerte</a>
                            </h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="tableAlert" class="table table-striped table-hover">
                                        <tr></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_historique">
                <div class="form-body form">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="form-section"><i class="fa fa-clock-o"></i>&nbsp;Historique</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="tableHistorique" class="table table-striped table-hover">
                                        <tr>
                                            <th>Date</th>
                                            <th>Etat</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_releve">
                <div class="form-body form">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="form-section"><i class="fa fa-dashboard"></i>&nbsp;Relevé Consommations</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="tableConsommation" class="table table-striped table-hover">
                                        
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    </div>
</div>
<div class="modal fade" id="editAlert" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idalert" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span id="labelEditMessageDevice">Ajout d'une alerte</span>
            </div>
            <div class="modal-body">
                <div id="alertError" style="display:none;" class="alert alert-danger"></div>
                <div class="row">
                    <h5 class="form-section"><i class="fa fa-dashboard"></i>&nbsp;Conditions</h3>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="alertOperator">Si</label>
                            <div class="col-md-9">
                                <select id="alertOperator" name="alertOperator" class="form-control">
                                    <option value="<">inférieur à </option>
                                    <option value=">">supérieur à </option>
                                    <option value="=">égal à</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:10px;">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="alertValue">à</label>
                            <div class="col-md-9">
                                <input id="alertValue" name="alertValue" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                    <h5 class="form-section"><i class="fa fa-envelope"></i>&nbsp;Notification</h3>
                    <div class="col-md-12" style="margin-top:10px;">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="alertPushingbox">Pushing Box</label>
                            <div class="col-md-9">
                                <select id="alertPushingbox" name="alertPushingbox" class="form-control">
                                    <option value="-1"></option>
                                    <?php 
                                    foreach($notifications as $notification){
                                        echo "<option value=\"".$notification["id"]."\">".$notification["name"]."-".$notification["deviceId"]."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelAlert" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitAlert" type="button">Valider</button>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="editMessageDevice" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idmessagedevice" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span id="labelEditMessageDevice"></span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="msgName">Nom</label>
                            <div class="col-md-9">
                                <input id="msgName" name="msgName" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="msgCommand">Commande</label>
                            <div class="col-md-9">
                                <input id="msgCommand" name="msgCommand" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="msgType">Type</label>
                            <div class="col-md-9">
                                <input id="msgType" name="msgType" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="msgAction">Action</label>
                            <div class="col-md-9">
                                <input id="msgAction" name="msgAction" class="form-control" value="1" type="checkbox">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="msgSlider">Variation</label>
                            <div class="col-md-9">
                                <input id="msgSlider" name="msgSlider" class="form-control" value="" type="checkbox">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelMessageDevice" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitMessageDevice" type="button">Valider</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteMessageDevice" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression de la commande?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelMessageDeviceDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeleteMessageDeviceConfirm" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteAlert" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression d'alerte?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelAlertDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeleteAlertConfirm" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-toastr/toastr.min.css"/>
    <script src="assets/global/plugins/bootstrap-toastr/toastr.min.js"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script src="assets/admin/pages/scripts/ui-toastr.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('.box').bind('click',function(e){
        $.ajax({
            url: "ajax/action.php",
            type: "POST",
            data: {
                type:  encodeURIComponent($(this).attr('type')),
                deviceId: $(this).attr('deviceId'),
                action: $(this).attr('action')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                toastr.success("Action exécutée");
            }
        });
    });
    $('.btnPlayMessage').bind('click',function(e){
        $.ajax({
            url: "ajax/action/execute.php",
            type: "POST",
            data: {
                type:  'message',
                elementId: $(this).attr('idMessage')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data == "error"){
                    toastr.error("Une erreur est survenue","Erreur");
                } else {
                    toastr.success("Action exécutée");
                }
            }
        });
    });
    
    /*
     * STATE RESULTS
     */
    $('.btnAddStateResult').bind('click',function(e){
        var count=$('.stateResultBrute').size();
        count=count + 1;
        $('.tableStateResult').append('<tr><td><input type="text" id="stateResultBrute'+count+'" class="form-control stateResultInput stateResultBrute" value="" onKeyUp="generateInputResultState();" /></td><td><input type="text" id="stateResultAffecte'+count+'" class="form-control stateResultInput stateResultAffecte" value="" onKeyUp="generateInputResultState();" /></td></tr>');
    });
    
    $('.stateResultBrute').keyup(function(){
        generateInputResultState();
    });
    $('.stateResultAffecte').keyup(function(){
        generateInputResultState();
    });
    /*
     * END STATE RESULTS
     */
    
    $('.btnAddAlert').bind('click',function(e){
        $('#labelEditAlert').text("Ajout d'une alerte");
        $('#idalert').val('');
        $('#alertName').val('');
        $('#alertCommand').val('');
        $('#alertType').val('');    
    });
    
    $('.btnAddMessage').bind('click',function(e){
        $('#labelEditMessageDevice').text("Ajout d'une commande");
        $('#idmessagedevice').val('');
        $('#msgName').val('');
        $('#msgCommand').val('');
        $('#msgType').val('');
        $('#msgActif').prop('checked',false);
    });
    
    $('.btnDeleteMessage').bind('click',function(e){
        var idMessage=$(this).attr('idMessage');
        $('#idmessagedevice').val(idMessage);
    });
    
    $('.btnDeleteAlert').bind('click',function(e){
        var idAlert=$(this).attr('idAlert');
        $('#idalert').val(idAlert);
    });
    
    $('.btnEditMessage').bind('click',function(e){
        $('#labelEditMessageDevice').text("Edition d'une commande");
        var idMessage=$(this).attr('idMessage');
        $('#idmessagedevice').val(idMessage);
        $.ajax({
            url: "ajax/message_device_load.php",
            type: "POST",
            data: {
                messageId: idMessage
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    eval(data);
                    eval(data.responseText);
                }
            }
        });
    });
    
    $('.btnSubmitAlert').bind('click',function(e){
        if($('#alertValue').val() == ""){
            $('#alertError').text('Veuillez renseigner une valeur');
            $('#alertError').show();
            return false;
        }
        if($('#alertPushingbox').val() == "-1"){
            $('#alertError').text('Veuillez renseigner une notification');
            $('#alertError').show();
            return false;
        }
        $('#alertError').text('');
        $('#alertError').hide();
        
        $.ajax({
            url: "ajax/alert_submit.php",
            type: "POST",
            data: {
                alertId:$('#idalert').val(),
                deviceId: $('#iddevice').val(),
                operator: $('#alertOperator').val(),
                value: $('#alertValue').val(),
                pushingbox: $('#alertPushingbox').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('.btnCancelAlert').click();
                    toastr.info("Veuillez recharger","Alerte ajoutée");  
                }
            }
        });
    });
    
    $('.btnDeleteAlertConfirm').bind('click',function(e){
        var alertId=$('#idalert').val();
        $.ajax({
            url: "ajax/delete_alert.php",
            type: "POST",
            data: {
                alertId:  alertId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelAlertDeletion').click();
                    $('#line-alert-'+alertId).fadeOut(300, function(){ 
                       $('#line-alert-'+alertId).remove(); 
                    });
                    toastr.info("Alerte supprimée");
                }
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
    });
    
    $('.btnSubmitMessageDevice').bind('click',function(e){
        
        $.ajax({
            url: "ajax/message_device_submit.php",
            type: "POST",
            data: {
                messageId: $('#idmessagedevice').val(),
                deviceId: $('#iddevice').val(),
                name: $('#msgName').val(),
                command: $('#msgCommand').val(),
                slider: $('#msgSlider').is(':checked'),
                action: $('#msgAction').is(':checked'),
                type: $('#msgType').val()
                //active: $('#msgActif').is(':checked')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('.btnCancelMessageDevice').click();
                    toastr.info("Veuillez recharger","Message ajouté");
                    
                }
            }
        });
    });
    
    $('.btnDeleteMessageDeviceConfirm').bind('click',function(e){
        var messageId=$('#idmessagedevice').val();
        $.ajax({
            url: "ajax/delete_message_device.php",
            type: "POST",
            data: {
                messageId:  messageId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelMessageDeviceDeletion').click();
                    $('#line-'+messageId).fadeOut(300, function(){ 
                       $('#line-'+messageId).remove(); 
                    });
                    toastr.info("Message supprimé");
                }
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
    });
    $('.btnTabLogs').bind('click',function(e){
        $.ajax({
            url: "ajax/get_last_logs.php",
            type: "POST",
            data: {
                deviceId:  $('#iddevice').val(),
                type:  $(this).attr('type')
            },
            beforeSend: function(data){
                Metronic.blockUI({boxed: true});
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
                Metronic.unblockUI();
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
                eval(data.responseText);
                Metronic.unblockUI();
            }
        });
    });
    $('.btnTabAlerts').bind('click',function(e){
        $.ajax({
            url: "ajax/get_alerts.php",
            type: "POST",
            data: {
                deviceId:  $('#iddevice').val()
            },
            beforeSend: function(data){
                Metronic.blockUI({boxed: true});
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
                Metronic.unblockUI();
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
                eval(data.responseText);
                Metronic.unblockUI();
            }
        });
    });
});


function generateInputResultState(){
    var count = $('.stateResultBrute').size();
    if(count <= 0){
        ('#state_results').val('');
        return false;
    }
    if($('.stateResultBrute1').val() == ""){
        ('#state_results').val('');
        return false;
    }
    
    var i=1;
    var params="{";
    $('.stateResultBrute').each(function(){
        if(params != "{"){
            params += ",";
        }
        params += '"'+$('#stateResultBrute'+i).val()+'":"'+$('#stateResultAffecte'+i).val()+'"';
        i=i+1;
    });
    params += "}";
    $('#state_results').val(params);
    //console.debug(params);
}

function editAlert(idAlert){
    $.ajax({
        url: "ajax/alert_load.php",
        type: "POST",
        data: {
            alertId:  idAlert
        },
        beforeSend: function(data){
            Metronic.blockUI({boxed: true});
        },
        error: function(data){
            toastr.error("Une erreur est survenue");
            Metronic.unblockUI();
        },
        complete: function(data){
            if(data.responseText == "error"){
                toastr.error("Une erreur est survenue");
                return true;
            }
            if(data == "error"){
                toastr.error("Une erreur est survenue");
                return true;
            }
            eval(data.responseText);
            Metronic.unblockUI();
        }
    });
}

</script>
<?php
include "modules/footer.php";
?>