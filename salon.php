<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js";   
$includeCSS[] = "/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css";   


include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"]=connectDB();
include "models/Device.php";
include "models/Log.php";

$devices=Device::getDevices();
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
        <!-- BEGIN PAGE CONTAINER-->
        <div class="container-fluid">
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                        <div class="col-md-12"> 	
                                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                                <h3 class="page-title">
                                        Salon				
                                        <small>Gestion des équipements du salon</small>
                                </h3>
                                <!-- END PAGE TITLE & BREADCRUMB-->
                        </div>
                </div>
                <!-- END PAGE HEADER-->
                <div id="dashboard">
                        <!-- BEGIN DASHBOARD STATS -->
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="portlet box blue-hoki">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-order"></i>Multimédia
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="tabbable portlet-tabs">
                                                <ul class="nav nav-tabs" style="top:-50px;">
                                                    <li>
                                                        <a data-toggle="tab" href="#portlet_tab3">Popcorn</a>
                                                    </li>
                                                    <li>
                                                        <a data-toggle="tab" href="#portlet_tab2">TV</a>
                                                    </li>
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#portlet_tab1">Freebox</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-content" style="margin-top: -40px;">
                                                <div id="portlet_tab1" class="tab-pane active">
                                                    <table class="table table-striped table-hover">
                                                    <?php
                                                    $cmds = array();
                                                    $cmds["power"]="free-power";
                                                    $cmds["enter"]="free-enter";
                                                    $cmds["home"]="free-home";
                                                    $cmds["mute"]="free-mute";
                                                    $cmds["vol +"]="free-volumeup";
                                                    $cmds["vol -"]="free-volumedown";
                                                    $cmds["prog +"]="free-programup";
                                                    $cmds["prog -"]="free-programdown";
                                                    foreach($cmds as $name=>$cmd){
                                                        echo "<tr><td class=\"action\" type=\"".$cmd."\">".ucwords($name)."</td></tr>";
                                                    }
                                                    ?>
                                                    </table>
                                                </div>
                                                <div id="portlet_tab2" class="tab-pane">
                                                    <table class="table table-striped table-hover">
                                                    <?php
                                                    $cmds = array();
                                                    $cmds["on"]="tv-on";
                                                    $cmds["off"]="tv-off";
                                                    $cmds["freebox"]="tv-free";
                                                    $cmds["popcorn"]="tv-popcorn";
                                                    $cmds["ps3"]="tv-ps3";
                                                    foreach($cmds as $name=>$cmd){
                                                        echo "<tr><td class=\"action\" type=\"".$cmd."\">".ucwords($name)."</td></tr>";
                                                    }
                                                    ?>
                                                    </table>
                                                </div>
                                                <div id="portlet_tab3" class="tab-pane">
                                                    <table class="table table-striped table-hover">
                                                    <?php
                                                    $cmds = array();
                                                    $cmds["power"]="power";
                                                    $cmds["home"]="home";
                                                    $cmds["vol +"]="volup";
                                                    $cmds["vol -"]="voldown";
                                                    $cmds["mute"]="mute";
                                                    foreach($cmds as $name=>$cmd){
                                                        echo "<tr><td class=\"action\"  type=\"popcorn\" action=\"".$cmd."\">".ucwords($name)."</td></tr>";
                                                    }
                                                    ?>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="portlet box red-sunglo tabbable">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-order"></i>Audio
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="tabbable portlet-tabs">
                                                <ul class="nav nav-tabs">
                                                    <li>
                                                        <a data-toggle="tab" href="#portlet_audio2">CD</a>
                                                    </li>
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#portlet_audio1">Ampli</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-content" style="margin-top: -40px;">
                                                <div id="portlet_audio1" class="tab-pane active">
                                                    <table class="table table-striped table-hover">
                                                    <?php
                                                    $cmds = array();
                                                    $cmds["on"]="amp-on";
                                                    $cmds["off"]="amp-off";
                                                    $cmds["mute"]="KEY_MUTE";
                                                    $cmds["vol +"]="KEY_VOLUMEUP";
                                                    $cmds["vol -"]="KEY_VOLUMEDOWN";
                                                    $cmds["aux"]="KEY_AUX";
                                                    $cmds["tuner"]="KEY_TUNER";
                                                    $cmds["dvd"]="KEY_DVD";
                                                    foreach($cmds as $name=>$cmd){
                                                        $txtCmd=($name == "on" || $name == "off") ? " type==\"".$cmd."\" " : " type=\"amp\" action=\"".$cmd."\" ";
                                                        echo "<tr><td class=\"action\" $txtCmd>".ucwords($name)."</td></tr>";
                                                    }
                                                    ?>
                                                    </table>
                                                </div>
                                                <div id="portlet_audio2" class="tab-pane">
                                                    <table class="table table-striped table-hover">
                                                    <?php
                                                    $cmds = array();
                                                    $cmds["power"]="KEY_POWER";
                                                    $cmds["play"]="KEY_PLAY";
                                                    $cmds["pause"]="KEY_PAUSE";
                                                    $cmds["stop"]="KEY_STOP";
                                                    $cmds["next"]="KEY_NEXTSONG";
                                                    $cmds["previous"]="KEY_PREVIOUSSONG";
                                                    $cmds["cd"]="KEY_NEXT";
                                                    $cmds["eject"]="KEY_EJECTCD";
                                                    foreach($cmds as $name=>$cmd){
                                                        echo "<tr><td class=\"action\"  type=\"cd\" action=\"".$cmd."\">".ucwords($name)."</td></tr>";
                                                    }
                                                    ?>
                                                    </table>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="portlet box yellow tabbable">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-order"></i>Lumières
                                        </div>
                                    </div>
                                
                                    <div class="portlet-body">
                                    <div class="tabbable portlet-tabs">
                                        <ul class="nav nav-tabs">
                                            <li>
                                                <a data-toggle="tab" href="#portlet_light2">Groupes</a>
                                            </li>
                                            <li class="active">
                                                <a data-toggle="tab" href="#portlet_light1">Lumières</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content" style="margin-top: -40px;">
                                        <div id="portlet_light1" class="tab-pane active">
                                            <table class="table table-striped table-hover">
                                            <?php
                                            foreach($devices as $device){
                                                if(strtolower($device->type)!="light"){
                                                    continue;
                                                }
                                                if($device->ip_address == ""){
                                                    continue;
                                                }
                                                $checked=(strtolower($device->state)=="on") ? " checked " : "";
                                                //<button class='btn red action-light' deviceId='".$device->ip_address."' action='off' style='float:right;' type='button'>Off</button>
                                                //    <button class='btn green action-light' deviceId='".$device->ip_address."' action='on' style='float:right;' type='button'>On</button>
                                                    
                                                //$txtCmd=($name == "on" || $name == "off") ? " type==\"".$cmd."\" " : " type=\"amp\" action=\"".$cmd."\" ";
                                                echo "<tr><td >".ucwords($device->name)."
                                                    <input type=\"checkbox\" class=\"make-switch action-light-".$device->id."\" $checked data-on-color=\"success\" data-off-color=\"danger\" deviceId='".$device->ip_address."' style=\"float:right;\">
                                                    </td></tr>";
                                            }
                                            ?>
                                            </table>
                                        </div>
                                        <div id="portlet_light2" class="tab-pane">
                                            null
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                </div>
        </div>
        <!-- END PAGE CONTAINER-->	
</div>
<!-- END PAGE -->
<script type="text/javascript">
    $(document).ready(function () {
        $('.action').bind('click',function(e){
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
                  //$.notify("Action exécutée","info"); 
                  toastr.success("Action exécutée");
                }
            });
        });
        
        /*$('.action-light').bind('click',function(e){
            $.ajax({
                url: "ajax/action.php",
                type: "POST",
                data: {
                   type:  'light',
                   deviceId: $(this).attr('deviceId'),
                   action: $(this).attr('action')
                },
                complete: function(data){
                  //$.notify("Action exécutée","info"); 
                  toastr.success("Action exécutée");
                }
            });
        });*/
        
        $('.make-switch').on('switchChange.bootstrapSwitch', function () {
            console.debug('ok');
            if($(this).is(':checked')){
                var action='on';
            }else{
                var action='off';
            }
            $.ajax({
                url: "ajax/action.php",
                type: "POST",
                data: {
                   type:  'light',
                   deviceId: $(this).attr('deviceId'),
                   action: action
                },
                error: function(data){
                    toastr.error("Une erreur est survenue");
                },
                complete: function(data){
                  //$.notify("Action exécutée","info"); 
                  toastr.success("Action exécutée");
                }
            });
        });
        
        //$( "#portlet-tabs" ).tabs();
    });
    var ui="toastr";
</script>
<?php
include "modules/footer.php";
?>
