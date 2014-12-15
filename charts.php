<?php
$includeCSS = $includeJS = array();
$includeCSS[] = "/assets/global/plugins/clockface/css/clockface.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-datetimepicker/css/datetimepicker.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css";
$includeCSS[] = "/assets/admin/layout/css/themes/default.css";
$includeCSS[] = "";
$includeJS[] = "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js";
$includeJS[] = "/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js";
$includeJS[] = "/assets/global/plugins/clockface/js/clockface.js";
$includeJS[] = "/assets/global/plugins/bootstrap-daterangepicker/moment.min.js";
$includeJS[] = "/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js";
$includeJS[] = "/assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js";
$includeJS[] = "/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
$includeJS[] = "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js";
$includeJS[] = "/assets/admin/pages/scripts/components-pickers.js";

include "modules/header.php";
include "modules/sidebar.php";
include "models/Device.php";

$types=array(
    //"column"=>"colonne",
    "time"=>"temps",
    "line"=>"ligne"
);

$GLOBALS["dbconnec"]=connectDB();

//Récupération Devices
$devices = Device::getDevices();
$deviceTab=array();
foreach($devices as $device){
    $deviceTab[$device->id]=$device;
}
//print_r($deviceTab);
$error="";
$isPost=FALSE;
$series=$seriesName=array();
$dateBegin = (isset($_POST["dateBegin"])) ? $_POST["dateBegin"] : '01-'.date('m-Y');
$dateEnd = (isset($_POST["dateEnd"])) ? $_POST["dateEnd"] : date('d-m-Y');
$formDevices = (isset($_POST["formDevices"])) ? $_POST["formDevices"] : NULL;
$type = (isset($_POST["chartType"])) ? $_POST["chartType"] : "time";
$scaleMin = (isset($_POST["chartScaleMin"])) ? $_POST["chartScaleMin"] : NULL;
$scaleMax = (isset($_POST["chartScaleMax"])) ? $_POST["chartScaleMax"] : NULL;

if(isset($_POST["formName"]) && $_POST["formName"] == "charts"){
    $isPost=TRUE;
    if($_POST["chartType"] == ""){
        $error.="Le type n'est pas renseigné";
    }
    if($_POST["dateBegin"] == ""){
        $error.="La date de début n'est pas renseignée";
    }
    if($_POST["dateEnd"] == ""){
        $error.= ($error == "") ? "" :"<br/>";
        $error.="La date de fin n'est pas renseignée";
    }
    if(count($_POST["formDevices"]) == 0){
        $error.= ($error == "") ? "" :"<br/>";
        $error.="Veuillez sélectionner au moins une sonde";
    }
    
    if($error == ""){
        $type=$_POST["chartType"];
        $dateBeginTmp=explode("-",$_POST["dateBegin"]);
        $dateEndTmp=explode("-",$_POST["dateEnd"]);
        
        $names=array();
        /*foreach($devices as $device){
            if(in_array($device->id, $_POST["formDevices"])){
                $names[]=$device->name;
            }
        }*/
        //foreach($names as $name){
        foreach($_POST["formDevices"] as $deviceId){
            $sql = "SELECT date,value FROM temperature WHERE deviceid='".$deviceId."' AND ";
            $sql .= "date BETWEEN '".$dateBeginTmp[2]."-".$dateBeginTmp[1]."-".$dateBeginTmp[0]." 00:00:00' AND '".$dateEndTmp[2]."-".$dateEndTmp[1]."-".$dateEndTmp[0]." 23:59:59' ORDER BY date ASC ";
            $stmt = $GLOBALS["dbconnec"]->prepare($sql);
            $stmt->execute();
            $values = array();
            $jsSerie="";
            while($row=$stmt->fetch()){
                //echo "<br/>TYPE = ".$deviceTab[$deviceId]->type;
                //$values[$row["date"]] = ($deviceTab[$deviceId]->type == "sensor_humidity") ? $row["value"] : $row["value"]/1000;
                $values[$row["date"]] = $row["value"];
                $date = new DateTime($row["date"]);
                $jsSerie .= ($jsSerie == "") ? ""  : ",";
                //$value = ($deviceTab[$deviceId]->type == "sensor_humidity") ? $row["value"] : $row['value']/1000;
                $value = ($deviceTab[$deviceId]->type == "sensor_humidity") ? $row["value"] : $row['value'];
                $month = (substr($date->format('m'), 0, 1) == '0') ? substr($date->format('m'),1,1) : $date->format('m');
                $month--;
                $day = (substr($date->format('d'), 0, 1) == '0') ? substr($date->format('d'),1,1) : $date->format('d');
                $hour = (substr($date->format('H'), 0, 1) == '0') ? substr($date->format('H'),1,1) : $date->format('H');
                $minute = (substr($date->format('i'), 0, 1) == '0') ? substr($date->format('i'),1,1) : $date->format('i');
                $jsSerie .= "[Date.UTC(".$date->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
            }
            $series[]=$jsSerie;
            $seriesName[]=$deviceTab[$deviceId]->name;
        }
    }
    
} else {
    
    $sevenDaysAgo=new Datetime('NOW');
    $interval=new DateInterval('P7D');
    $interval->invert=1;
    $sevenDaysAgo->add($interval);
    
    $lastDate=new DateTime("now");
    $resultats=$GLOBALS["dbconnec"]->query("SELECT value FROM config WHERE name='chart_default_devices'");
    $resultats->setFetchMode(PDO::FETCH_OBJ);
    while( $resultat = $resultats->fetch() )
    {
        $chartDefaultDevices=$resultat->value;
    }
    $formDevices = explode("~",$chartDefaultDevices);
    
    if(isset($chartDefaultDevices)){
        $defaultDevices = explode("~",$chartDefaultDevices);
        foreach($defaultDevices as $defaultDevice){
            $deviceTmp = $deviceTab[$defaultDevice];
            $sql = "SELECT date,value FROM temperature WHERE deviceid=".$deviceTmp->id." AND ";
            $sql .= "date BETWEEN '".$sevenDaysAgo->format('Y-m-d H:i:s')."' AND '".date('Y-m-d H:i:s')."' ORDER BY date ASC ";
            
            $stmt = $GLOBALS["dbconnec"]->prepare($sql);
            $stmt->execute();
            $values = array();
            $jsSerie="";
            while($row=$stmt->fetch()){
                //$values[$row["date"]] = $row["value"]/1000;  
                $values[$row["date"]] = $row["value"];  
                $date = new DateTime($row["date"]);
                $jsSerie .= ($jsSerie == "") ? ""  : ",";
                //$value = $row['value'];
                //$value = $row['value']/1000;
                $value = $row['value'];
                $month = (substr($date->format('m'), 0, 1) == '0') ? substr($date->format('m'),1,1) : $date->format('m');
                $month--;
                $day = (substr($date->format('d'), 0, 1) == '0') ? substr($date->format('d'),1,1) : $date->format('d');
                $hour = (substr($date->format('H'), 0, 1) == '0') ? substr($date->format('H'),1,1) : $date->format('H');
                $minute = (substr($date->format('i'), 0, 1) == '0') ? substr($date->format('i'),1,1) : $date->format('i');
                $jsSerie .= "[Date.UTC(".$date->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
            }
            $series[]=$jsSerie;
            $seriesName[]=$deviceTmp->name;
            
        }
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
                        Températures				
                        <small>Supervision des températures</small>
                </h3>
                <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <form class="horizontal-form" method="POST" action="./charts.php">
        <div class="portlet" style="margin-bottom:0px;">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i>Recherche
                </div>
                <div class="actions">
                    <div class="btn-group">
                        <!--<a class="btn default yellow-stripe" data-toggle="dropdown" href="ecommerce_products.html#">
                            <i class="fa fa-share"></i>
                            Tools
                            <i class="fa fa-angle-down"></i>
                        <a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="ecommerce_products.html#"> Export to Excel </a>
                            </li>
                        </ul>-->
                        <button class="btn yellow" type="submit" style="background: none repeat scroll 0% 0% rgb(255, 184, 72);">
                        <i class="fa fa-search"></i>
                        Rechercher
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        if($error != ""){
            echo "<div class=\"alert alert-error\">".$error."</div>";
        } 
        ?>
        
            <div class="form-body">
            <input type="hidden" id="formName" name="formName" value="charts" />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="formDevices">Sondes</label>
                            <select id="formDevices" name="formDevices[]" class="form-control" multiple="multiple" placeholder="Objets">
                                <?php
                                foreach($devices as $device){
                                    if(strtolower($device->type) != "sensor" && strtolower($device->type) != "sensor_humidity" && strtolower($device->type) != "raspberry"){
                                        continue;
                                    }
                                    $selected = (in_array($device->id,$formDevices)) ? " selected=\"selected\" " : "";
                                    echo "<option value=\"".$device->id."\" $selected>".$device->name."</option>";
                                } 
                                ?>
                            </select>
                    </div>
                </div>
                <div class="col-md-6 ">
                    <div class="form-group">
                        <label class="control-label" for="dateBegin">Date</label>
                        <div class="input-group input-large date-picker input-daterange" data-date-format="dd-mm-yyyy" data-date="10/11/2012">
                            <input id="dateBegin" name="dateBegin" class="form-control" type="text" name="from"  value="<?php echo $dateBegin; ?>">
                            <span class="input-group-addon"> au </span>
                            <input id="dateEnd" name="dateEnd" class="form-control" type="text" name="to" value="<?php echo $dateEnd; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 ">
                    <div class="form-group">
                        <label class="control-label" for="chartType">Type</label>
                        <select id="chartType" class="form-control" name="chartType">
<?php 
                        foreach($types as $typeId=>$typeTmp){
                            $selected = ($type==$typeId) ? " selected=\"selected\" " : "";
                            echo "<option value=\"".$typeId."\" $selected>".ucwords($typeTmp)."</option>";
                        }
?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 ">
                    <div class="form-group">
                        <label class="control-label" for="chartScaleMin">Echelle Min</label>
                        <div class="input-group" >
                                <input id="chartScaleMin" name="chartScaleMin" class="form-control" type="text"  value="<?php echo $scaleMin; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 ">
                    <div class="form-group">
                        <label class="control-label" for="chartScaleMax">Max</label>
                        <div class="input-group" >
                                <input id="chartScaleMax" name="chartScaleMax" class="form-control" type="text"  value="<?php echo $scaleMax    ; ?>">
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </form>
    </div>
    <div id="container">
        
    </div>
    </div>
        <?php //print_r($temps); ?>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
        $('#container').highcharts({
            <?php
            echo "chart: {";
                if($type=="time"){
                    echo " zoomType: 'x' ";
                }elseif($type=="line"){
                    echo " type: 'line' ";
                }
            echo "},";
            ?>
            title: {
                text: 'Supervision Temperature'
            },
            subtitle: {
                text: 'Salon & Exterieur'
            },
            xAxis: {
                type: 'datetime',
                title: {
                    text: 'Date'
                }
            },
            yAxis: {
<?php
                if(!is_null($scaleMin) && $scaleMin!= ""){
                    echo " min: $scaleMin,";
                }
                if(!is_null($scaleMax) && $scaleMax!= ""){
                    echo " max: $scaleMax,";
                }
?>
                title: {
                    text: 'Temperature (C)'
                }
            },
            series: [
                <?php 
                $i=0;
                foreach($series as $serie){
                  if($i>0){
                      echo ",";
                  }
                  echo "{";  
                  if($type=="time"){
                      echo "type:'area',";
                  }
                  echo "name:'".$seriesName[$i]."',";  
                  echo "data:[".$serie."]";  
                  echo "}";
                  $i++;
                }
                ?>
                ]
        });
        });
</script>
<!-- END PAGE -->

<?php
include "modules/footer.php";
?>
