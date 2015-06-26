<?php
include '../../tools/config.php';
include_once "../../models/PageItem.php";
include_once "../../models/Scenario.php";
include_once "../../models/Device.php";
include_once "../../models/Tuile.php";
include_once "../../models/Chart.php";
include_once "../../models/Liste.php";

$GLOBALS["dbconnec"] = connectDB();
$scenarios= Scenario::getScenarios();
//$devices= Device::getDevices();
$sqlDevices = "SELECT * FROM device ";
$sqlDevices .= " WHERE incremental=1 ";
$sqlDevices .= " OR id IN (";
$sqlDevices .= " SELECT deviceId FROM messagedevice";
$sqlDevices .= " WHERE parameters LIKE '%slider%'";
$sqlDevices .= ") ";
$sqlDevices .= " OR id IN (";
$sqlDevices .= " SELECT deviceId FROM messagedevice";
$sqlDevices .= " WHERE active=1";
$sqlDevices .= ") ";
$sqlDevices .= " OR (type='door') ";
$sqlDevices .= " ORDER BY name ASC ";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlDevices);
$stmt->execute(array());
$devicesTab = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $devicesTab[$row["id"]]=array(
        "id"=>$row["id"],
        "name"=>$row["name"],
        "type"=>$row["type"],
        "parameters"=>$row["parameters"],
        "incremental"=>$row["incremental"]
    );
}

$tuiles= Tuile::getTuiles();
$charts= Chart::getCharts();
$listes= Liste::getListes();
$periods=Chart::getPeriods();
$items = PageItem::getPageItemsForPage($_GET["pageId"]);
//Récupère meteo
$plugins=array();
$sqlPlugins = "SELECT * FROM config ";
$sqlPlugins .= " WHERE name IN ('meteo','website','camera','gauge') ";
$sqlPlugins .= " ORDER BY name, value ";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $plugins[$row["name"]][$row["id"]]=$row["value"];
}
?>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-plus"></i>&nbsp;&nbsp;Ajout d'un objet</h4>
    </div>
    <div class="modal-body">
        <div id="alert" class=""></div>
        <div class="row">
            <div class="col-md-4">
                <p class="text-center">Scénario</p>
                <select name="selectScenario" id="selectScenario" style="width:100%;">
                    <option></option>
<?php
foreach($scenarios as $scenarioTmp){
    echo "<option value=\"".$scenarioTmp->id."\">".$scenarioTmp->name."</option>";
}
?>
                </select>
            </div>
            <div class="col-md-4">
                <p class="text-center">Tuile</p>
                <select name="selectTuile" id="selectTuile" style="width:100%;">
                    <option></option>
<?php
foreach($tuiles as $tuileTmp){
    if(!isset($devicesTab[$tuileTmp->deviceid])){
        continue;
    }
    $deviceTuile = $devicesTab[$tuileTmp->deviceid];
    $mode = ($deviceTuile["incremental"]=='1') ? "incremental" : "normal";
    echo "<option value=\"".$tuileTmp->id."\" mode=\"$mode\">".$tuileTmp->name."</option>";
}
?>
                </select>
            </div>
            <div class="col-md-4">
                <p class="text-center">Graphique</p>
                <select name="selectChart" id="selectChart" style="width:100%;">
                    <option></option>
<?php
foreach($charts as $chartTmp){
    echo "<option value=\"".$chartTmp->id."\">".$chartTmp->name."</option>";
}
?>
                </select>
            </div>
            <div class="col-md-4">
                <p class="text-center">Liste</p>
                <select name="selectListe" id="selectListe" style="width:100%;">
                    <option></option>
<?php
foreach($listes as $listeTmp){
    echo "<option value=\"".$listeTmp->id."\">".$listeTmp->name."</option>";
}
?>
                </select>
            </div>
            <div class="col-md-4">
                <p class="text-center">Objet</p>
                <select name="selectDevice" id="selectDevice" style="width:100%;">
                    <option></option>
<?php
foreach($devicesTab as $deviceTmp){
    //Check if slider
    $pos = strpos($deviceTmp["parameters"], "slider");
    $type = ($pos !== false) ? "slider" : "";
    $type = ($deviceTmp["incremental"] == "1") ? "incremental" : $type;
    $typeTxt = ($deviceTmp["type"] != "") ? " - ".$deviceTmp["type"] : "";
    echo "<option value=\"".$deviceTmp["id"]."\" type=\"".$type."\">".$deviceTmp["name"].$typeTxt."</option>";
}
?>
                </select>
            </div>
            <div class="col-md-4">
                <p class="text-center">Plugins</p>
                <select name="selectPlugins" id="selectPlugins" style="width:100%;">
                    <option></option>
<?php
foreach($plugins as $type=>$params){
    echo "<optgroup label=\"".ucwords($type)."\">";
    foreach($params as $pluginId=>$pluginName){
        if($type == "meteo"){
            $pluginName=explode(",",$pluginName);
            $pluginName=ucwords($pluginName[0]);
        } elseif($type == "gauge"){
            $gaugeDevice=Device::getDevice($pluginName);
            $typeTmp=($gaugeDevice->type != "") ? " - ".$gaugeDevice->type : ""; 
            $pluginName=$gaugeDevice->name.$typeTmp;
        }
        echo "<option value=\"".$pluginId."\" type=\"".strtolower($type)."\">".$pluginName."</option>";
    }
    echo "</optgroup>";
}
?>
                </select>
            </div>
        </div>
        <div class="row optionTuile" style="display:none;border-top: 1px solid #e5e5e5;margin-top: 15px;padding: 19px 20px 20px;text-align: right;">
            <div class="col-md-12 " style="text-align: left;">
                <h4><i class="fa fa-gears"></i>Options</h4>
            </div>
            <div class="col-md-4 optionColor">
                <p class="text-center">Couleur</p>
                <select id="selectColor" style="width: 100%;">
<?php
                    foreach($colors as $colorType=>$array){
                        echo "<optgroup label=\"".ucwords($colorType)."\">";
                        foreach($array as $key=>$colorTmp){
                           echo "<option value=\"".$key."\" >".ucwords($colorTmp)."</option>";
                        }
                        echo "</optgroup>";
                    }
?>
                </select>
            </div>
            <div class="col-md-4 optionPeriod" style="display:none;">
                <p class="text-center">Période</p>
                <select id="selectPeriod" style="width: 100%;">
                    <option></option>
<?php
                    foreach($periods as $periodId=>$periodName){
                        echo "<option value=\"".$periodId."\" >".ucwords($periodName)."</option>";
                    }
?>
                </select>
            </div>
            <div class="col-md-4 optionSliderColor" style="display:none;">
                <p class="text-center">Couleur Slider</p>
                <select id="selectSliderColor" style="width: 100%;">
                    <option></option>
<?php
$colorSlider=array(
    "yellow" => "jaune",
    "red" => "rouge",
    "blue" => "bleu",
    "purple" => "violet",
    "green" => "vert",
    "grey" => "gris"
);
                    foreach($colorSlider as $colorSliderId=>$colorSliderName){
                        echo "<option value=\"".$colorSliderId."\" >".ucwords($colorSliderName)."</option>";
                    }
?>
                </select>
            </div>
            <div class="col-md-4 optionDescription" style="display:none;">
                <p class="text-center">Description</p>
                <input type="text" id="inputDescription" style="width: 100%;">
            </div>
            <div class="col-md-4 optionLargeur">
                <p class="text-center">Largeur</p>
                <select id="selectWidth" style="width: 100%;">
<?php
                for($width=2;$width<=12;$width++){
                        echo "<option value=\"".$width."\" >".$width."</option>";
                }
?>
                </select>
            </div>
            <div class="col-md-4 optionHeight" style="display:none;">
                <p class="text-center">Hauteur</p>
                <input type="text" id="inputHeight" style="width: 100%;">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btnAddPageItem">Ajouter</button>
    </div>
</div>
            <!-- /.modal-content -->
<script src="assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('#selectTuile').bind('change',function(e){
        $('.optionTuile').show();
        $('.optionLargeur').show();
        $('.optionColor').show();
        
        if($('#selectTuile option:selected').attr('mode') == "incremental"){
            //$('.optionTuile').show();
            $('.optionPeriod').show();
            $('.optionDescription').show();
        } else {
            //$('.optionTuile').hide();
            $('.optionPeriod').hide();
            $('.optionDescription').hide();
        }
    });
    $('#selectDevice').bind('change',function(e){
        if($('#selectDevice option:selected').attr('type') == "slider"){
            $('.optionTuile').show();
            $('.optionSliderColor').show();
        } else {
            $('.optionTuile').hide();
            $('.optionSliderColor').hide();
        }
    });
    
    $('#selectPlugins').bind('change',function(e){
        if($('#selectPlugins option:selected').attr('type') == "website"){
            $('.optionTuile').show();
            $('.optionHeight').show();
        } else {
            if($('#selectPlugins option:selected').attr('type') == "meteo"){
                $('.optionTuile').show();
                
            } else {
                $('.optionTuile').hide();
                $('.optionHeight').hide();
            }
        }
    });
    
    $('.btnAddPageItem').bind('click',function(e){
        $('#alert').removeClass('alert alert-danger');
        $('#alert').text('');
        //Control
        if($('#selectScenario').val() == "" && $('#selectTuile').val() == "" && $('#selectChart').val() == "" && $('#selectListe').val() == "" && $('#selectDevice').val() == "" && $('#selectPlugins').val() == "" && $('#selectWidth').val() == ""){
            $('#alert').addClass('alert alert-danger');
            $('#alert').text('Veuillez sélectionner un objet');
            return true;
        }
        var check=0;
        if($('#selectScenario').val() != ""){
            var check=check+1;
        }
        if($('#selectTuile').val() != ""){
            var check=check+1;
        }
        if($('#selectChart').val() != ""){
            var check=check+1;
        }
        if($('#selectListe').val() != ""){
            var check=check+1;
        }
        if($('#selectDevice').val() != ""){
            var check=check+1;
        }
        if($('#selectPlugins').val() != ""){
            var check=check+1;
        }
        
        if(check > 1){
            $('#alert').addClass('alert alert-danger');
            $('#alert').text('Veuillez sélectionner un SEUL objet');
            return true;
        }
        
        var incremental='false';
        if($('#selectTuile option:selected').attr('mode') == "incremental"){
            var incremental='true';
        }
        var slider='false';
        if($('#selectDevice option:selected').attr('type') == "slider"){
            var slider='true';
        }
        var pluginType='';
        if($('#selectPlugins option:selected').attr('type') != ""){
            var pluginType=$('#selectPlugins option:selected').attr('type');
        }
        
        $.ajax({
            url: "ajax/add_page_item.php",
            type: "POST",
            data: {
                pageid:  $('#pageId').val(),
                scenarioid:  $('#selectScenario').val(),
                tuileid:  $('#selectTuile').val(),
                listeid:  $('#selectListe').val(),
                chartid:  $('#selectChart').val(),
                deviceid:  $('#selectDevice').val(),
                plugin:  $('#selectPlugins').val(),
                pluginType:  pluginType,
                incremental:  incremental,
                description:  $('#inputDescription').val(),
                period:  $('#selectPeriod').val(),
                width:  $('#selectWidth').val(),
                height:  $('#inputHeight').val(),
                color:  $('#selectColor').val(),
                slider:  slider,
                colorSlider:  $('#selectSliderColor').val()
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('#alert').text("Objet ajouté, veuillez recharger la page pour voir les modifications");
                    $('#alert').addClass('alert alert-success');
                } else {
                    $('#alert').text(data.responseText);
                    $('#alert').addClass('alert alert-danger');
                }
            }
        });
    });
});
</script>