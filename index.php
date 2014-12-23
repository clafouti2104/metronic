<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"]=connectDB();
include "models/Device.php";
include "models/Log.php";

//Récupération Devices
$devices = Device::getDevices();
//$devices = array();
$tmpDevices=array();
foreach($devices as $device){
    $tmpDevices[$device->id]=$device;
}
$pagesParent=Page::getPageParents();

//Récupération Logs
//$logs = Log::getLastLogs(20);

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
        <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
        <!-- BEGIN PAGE CONTAINER-->
        <div class="container-fluid">
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                                <h3 class="page-title">
                                        Dashboard				
                                        <small>domotic</small>
                                </h3>
                                <!-- END PAGE TITLE & BREADCRUMB-->
                        </div>
                </div>
                <!-- END PAGE HEADER-->
                <div id="dashboard">
                        <!-- BEGIN DASHBOARD STATS -->
                        <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 box-action" type="freebox">
                                        <div class="dashboard-stat blue-madison">
                                                <div class="visual">
                                                        <i class="fa fa-play-circle"></i>
                                                </div>
                                                <div class="details">
                                                        <div class="number">
                                                                Freebox
                                                        </div>
                                                        <div class="desc">									
                                                                Allume TV & Freebox
                                                        </div>
                                                </div>
                                                <a class="more" href="#">
                                                  Info <i class="m-icon-swapright m-icon-white"></i>
                                                </a>						
                                        </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 box-action" type="sce-cd">
                                        <div class="dashboard-stat yellow">
                                                <div class="visual">
                                                        <i class="fa fa-bullhorn"></i>
                                                </div>
                                                <div class="details">
                                                        <div class="number">Musique</div>
                                                        <div class="desc">Mode CD</div>
                                                </div>
                                                <a class="more" href="#">
                                                Info <i class="m-icon-swapright m-icon-white"></i>
                                                </a>						
                                        </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 box-action" >
                                        <div class="dashboard-stat purple">
                                                <div class="visual">
                                                        <i class="fa fa-picture-o"></i>
                                                </div>
                                                <div class="details">
                                                        <div class="number">Film</div>
                                                        <div class="desc">TV & Popcorn</div>
                                                </div>
                                                <a class="more" href="#">
                                                Info <i class="m-icon-swapright m-icon-white"></i>
                                                </a>						
                                        </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 box-action" type="extinction">
                                        <div class="dashboard-stat red">
                                                <div class="visual">
                                                        <i class="fa fa-power-off "></i>
                                                </div>
                                                <div class="details">
                                                        <div class="number">Extinction</div>
                                                        <div class="desc">Eteint les &eacute;quipements du salon</div>
                                                </div>
                                                <a class="more" href="#">
                                                Info <i class="m-icon-swapright m-icon-white"></i>
                                                </a>						
                                        </div>
                                </div>
<?php
foreach($pagesParent as $pageParent){
?>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 pageParent" idPageParent="<?php echo $pageParent->id; ?>">
                                        <div class="dashboard-stat <?php echo $pageParent->color; ?>">
                                                <div class="visual">
                                                        <i class="fa <?php echo $pageParent->icon; ?> "></i>
                                                </div>
                                                <div class="details">
                                                        <div class="number"><?php echo $pageParent->name; ?></div>
                                                        <div class="desc"><?php echo $pageParent->description; ?></div>
                                                </div>
                                                <a class="more" href="#">
                                                    &nbsp;<i class="m-icon-swapright m-icon-white"></i>
                                                </a>						
                                        </div>
                                </div>
                            
<?php
}
?>
                                <!--</div>-->
                            </div>
                        </div>
                        <div class="tiles">
                            <!--<img src="http://192.168.1.24:8080/video"/>-->
                                <?php
                                foreach($devices as $device){
                                    if(strtolower($device->type) != "sensor" && strtolower($device->type) != "sensor_humidity"){
                                        continue;
                                    }
                                    
                                    /*if($device->model == "ds18b20"){
                                        $temp = number_format(substr($device->state,0,strlen($device->state)-1)/100,2,",","");
                                    } else {
                                    }*/
                                    $temp = number_format($device->state,2,",","");
                                    
                                    $type=(strtolower($device->type) == "sensor") ? "°C" : "%";
                                    $bgColor=(strtolower($device->type) == "sensor") ? "green" : "blue-hoki";
                                    
                                    echo '<div class="tile bg-'.$bgColor.'" onclick="location.href=\'charts.php\';">';
                                    echo '<div class="tile-body">';
                                    echo '<i class="" style="margin-top:30px;font-size: 40px;">'.$temp.'</i>';
                                    echo '</div>';
                                    echo '<div class="tile-object">';
                                    echo '<div class="name">'.ucwords($device->name).'</div>';
                                    echo '<div class="number">'.$type.'</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                ?>
                        </div>
                        <!-- END DASHBOARD STATS -->
                        <div class="clearfix"></div>
                        <div class="row">
                                <!--<div class="col-md-6">
                                    <div class="portlet box blue-hoki">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-bell"></i>Objets
                                            </div> 
                                        </div>
                                        <div class="portlet-body">
                                            <div class="scroller" data-height="290px" data-always-visible="1" data-rail-visible1="1">
                                                <table class="table table-striped table-bordered table-advance table-hover">
                                                    <tr>
                                                        <th colspan="2">Nom</th>
                                                        <th>Type</th>
                                                        <th colspan="2">Etat</th>
                                                    </tr>
                                                    <?php
                                                        /*foreach($devices as $device){
                                                            switch(strtolower($device->type)){
                                                                case "presence":
                                                                    $icon="eye";
                                                                    break;
                                                                case "light":
                                                                    $icon="bolt";
                                                                    break;
                                                                case "sensor":
                                                                    $icon="tint";
                                                                    break;
                                                                default:
                                                                    $icon="bell";
                                                            }
                                                            switch(strtolower($device->state)){
                                                                case "on":
                                                                    $color="success";
                                                                    $state="On";
                                                                    break;
                                                                case "off":
                                                                    $color="danger";
                                                                    $state="Off";
                                                                    break;
                                                                default:
                                                                    $color="info";
                                                                    if($device->model == "ds18b20"){
                                                                        $state = number_format(substr($device->state,0,strlen($device->state)-1)/100,2,",","");
                                                                    } else {
                                                                        $state = number_format($device->state,2,",","");
                                                                    }
                                                                    //$state= number_format(substr($device->state,0,strlen($device->state)-1)/100,2,",","");
                                                            }


                                                                echo "<tr>";
                                                                echo "<td><i class=\"fa fa-".$icon."\"></i></td>";
                                                                echo "<td>".$device->name."</td></td>";
                                                                echo "<td>".$device->type."</td>";
                                                                echo "<td><span class=\"label label-".$color." label-mini\">".$state."</span></td>";
                                                                echo "<td><div class=\"date\">".$device->last_update."</div></td>";
                                                                echo "</tr>";
                                                        }*/
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END PORTLET-->
                                <!--<div class="col-md-6">
                                        <div class="portlet box blue-hoki">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-bell"></i>Logs
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                
                                                        <div class="scroller" data-height="290px" data-always-visible="1" data-rail-visible1="1">
                                                            <ul class="feeds">
                                                                <?php
                                                                    //print_r($logs);
                                                                    /*foreach($logs as $log){
                                                                        $device = ($log->deviceid != '' && isset($tmpDevices[$log->deviceid])) ? $tmpDevices[$log->deviceid]->name : $log->rfid;
                                                                        $tmpInfo="";
                                                                        switch(strtolower($log->value)){
                                                                            default:
                                                                                $bgColor="";
                                                                            case "on":
                                                                                $label="success";
                                                                                $icon="bolt";
                                                                                break;
                                                                            case "off":
                                                                                $label="danger";
                                                                                $icon="bolt";
                                                                                break;
                                                                            case "armed":
                                                                                $label="danger";
                                                                                $icon="bullhorn";
                                                                                break;
                                                                            case "disarmed":
                                                                                $label="danger";
                                                                                $icon="bullhorn";
                                                                                break;
                                                                            case "partial":
                                                                                $label="warning";
                                                                                $icon="bullhorn";
                                                                                break;
                                                                            case "lost_communication":
                                                                                $label="danger";
                                                                                $icon="ban-circle";
                                                                                $tmpInfo="Perte Communication: ";
                                                                                break;
                                                                            default:
                                                                                $label="info";
                                                                                $icon="info-circle";
                                                                        }
                                                                        echo "<li>";
                                                                        echo "<div class=\"col1\">";
                                                                        echo "<div class=\"cont\">";
                                                                        echo "<div class=\"cont-col1\">";
                                                                        echo "<div class=\"label label-".$label."\"><i class=\"fa fa-$icon\"></i></div>";
                                                                        echo "</div>";
                                                                        echo "<div class=\"cont-col2\">";
                                                                        echo "<div class=\"desc\">".$device." - ".$log->value."</div>";
                                                                        echo "</div>";
                                                                        echo "</div>";
                                                                        echo "</div>";
                                                                        echo "<div class=\"col2\">";
                                                                        echo "<div class=\"date\">".$log->date."</div>";
                                                                        echo "</div>";
                                                                        echo "</li>";
                                                                    }*/
                                                                ?>
                                                            </ul>
                                                        </div>
                                            </div>
                                        </div>
                                        <!-- END PORTLET-->
                                </div>
                        </div>
                        <div class="clearfix"></div>
                </div>
        </div>
        <!-- END PAGE CONTAINER-->		
</div>
<!-- END PAGE -->
<script type="text/javascript">
$(document).ready(function () {
    $('.box-action').bind('click',function(e){
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
            complete: function(data){
                toastr.success("Action exécutée");
            }
        });
    });

    $('.pageParent').bind('click',function(e){
        $.ajax({
            url: "ajax/user/load_pages_filles.php",
            type: "POST",
            data: {
                idPageParent: $(this).attr('idPageParent')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                eval(data);
            }
        });
    });
            
    $('.dashboard-tile').bind( "click", function() {
    });
});
    var ui="toastr";
</script>
<?php
include "modules/footer.php";
?>
