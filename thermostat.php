<?php
$includeCSS = $includeJS = array();
$includeCSS[] = "/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css";
$includeCSS[] = "/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.Metronic.css";
$includeJS[] = "/assets/global/plugins/ion.rangeslider/js/ion-rangeSlider/ion.rangeSlider.min.js";
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
?>
<style type="text/css" media="screen">
#circle2 {
    background: none repeat scroll 0 0 red;
    height: 125px;
    width: 125px;
}
.circle {
    border-radius: 50% !important;
    display: inline-block;
    margin-left: 0px;
}
#advanced {
    width: 280px;
    height: 280px;

    background-image: -moz-radial-gradient(45px 45px 45deg, circle cover, #E89359 0%, #DD491B 100%, red 95%);
    background-image: -webkit-radial-gradient(45px 45px, circle cover, #E89359, #DD491B);
    background-image: radial-gradient(45px 45px 45deg, circle cover, #E89359 0%, #DD491B 100%, red 95%);
}
.title_thermostat {
    position: absolute; 
    left: 125px; 
    color: rgb(255, 255, 255); 
    font-size: 25px; 
    top: 45px;
    font-variant: small-caps;
}
.degree {
    position: absolute; 
    left: 100px; 
    color: rgb(255, 255, 255); 
    font-size: 100px; 
    font-weight: 600; 
    top: 65px;
}
.current_temperature {
    position: absolute; 
    left: 140px; 
    color: rgb(255, 255, 255); 
    font-size: 35px; 
    opacity: 0.5;
    top: 190px;
}
</style>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                        Thermostat
                        <small>Gestion du chauffage</small>
                    </h3>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="circle" id="advanced"> 
                    <span class="title_thermostat">Salon</span> 
                    <span class="degree stateDeviceId" stateDeviceId="127"></span> 
                    <span class="current_temperature stateDeviceId" stateDeviceId="123"></span> 
                </div>
                <div class="col-md-9">
                    <input id="temp_salon" type="text" name="temp_salon" value="24" style="width: 70%;"/>
                </div>
            </div>
            <!--<div class="col-md-6">
                <div class="circle" id="advanced"> 
                    <span class="title_thermostat">SdB</span> 
                    <span class="degree">24</span> 
                    <span class="current_temperature">20</span> 
                </div>
                <input id="temp_sdb" type="text" name="temp_sdb" value="24"/>
            </div>-->
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $("#temp_salon").ionRangeSlider({
        min: 16,
        max: 26,
        from: 24,
        type: 'single',
        step: 1,
        keyboard: true,
        hasGrid: true,
        onFinish: function (data) {
            console.log("onFinish");
        }
    });
    /*$("#temp_sdb").ionRangeSlider({
        min: 16,
        max: 26,
        from: 18,
        type: 'single',
        step: 1,
        keyboard: true,
        hasGrid: true,
        grid: true,
        grid_snap: true,
        onFinish: function (data) {
            console.log("onFinish");
        }
    });*/
        
setTimeout("refreshStatus()", 3000);
function refreshStatus(){
    var device_ids = new Array();
    //Status
    //$('.stateDeviceId:visible').each(function() {
    $('.stateDeviceId').each(function() {
        if ($(this).attr('stateDeviceId') != 0) {
            if ($.inArray($(this).attr('stateDeviceId'),device_ids) == -1) {
                device_ids.push($(this).attr('stateDeviceId'));
            }
        }
    });

    $.ajax({
        type: "POST",
        url: "scripts/status.php",
        dataType:"json",
        data: {ids: device_ids.join(',')},
        timeout: 2000,
        success: function(data){
            $.each(data, function(index, value) {
                value = utf8_decode(value);
                if(value.toLowerCase() != "on" && value.toLowerCase() != "off"){
                    if($('.slider-basic-'+index).size() == 1){
                        $('.slider-basic-'+index).slider({'value':value});
                    } else {
                        $('.stateDeviceId-'+index).text(value);
                    }
                }
            });
        }
    });

    if(device_ids.length > 0) setTimeout("refreshStatus()", 5000);
}
});
</script>
<?php
include "modules/footer.php";
?>