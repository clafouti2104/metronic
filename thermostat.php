<?php
$includeCSS = $includeJS = array();
$includeCSS[] = "/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css";
$includeCSS[] = "/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.Metronic.css";
$includeJS[] = "/assets/global/plugins/ion.rangeslider/js/ion-rangeSlider/ion.rangeSlider.min.js";
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["histoconnec"] = connectHistoDB();
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
                    <span class="degree stateDeviceId stateDeviceId-127" stateDeviceId="127"></span> 
                    <span class="current_temperature stateDeviceId stateDeviceId-123" stateDeviceId="123"></span> 
                </div>
                <div class="col-md-9">
                    <input id="slider-temp-127" class="slider_temp slider-temp-127" type="text" name="temp_salon" value="" elementid="101" style="width: 70%;display:none;"/>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $("#slider-temp-127").ionRangeSlider({
        min: 16,
        max: 26,
        from: 24,
        type: 'single',
        step: 1,
        grid: true,
        hasGrid: true,
        onFinish: function (data) {
            console.log(data.from);
            $.ajax({
                url: "ajax/action/execute.php",
                type: "POST",
                data: {
                   elementId: '101',
                   type:  encodeURIComponent('message'),
                   value:  data.from
                },
                error: function(datas){
                    toastr.error("Une erreur est survenue");
                },
                complete: function(datas){
                    toastr.success("Action exécutée");
                }
            });
        }
    });
    
    setTimeout("refreshStatus()", 2000);
});
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
                    if($('.slider-temp-'+index).size() >= 1){
                        var slider=$('.slider-temp-'+index).data("ionRangeSlider");
                        slider.update({from: value});
                    } 
                    if($('.stateDeviceId-'+index).size() >= 1) {
                        $('.stateDeviceId-'+index).text(value);
                    }
                }
            });
        }
    });

    if(device_ids.length > 0) setTimeout("refreshStatus()", 5000);
}
function utf8_decode(str_data) {

    var tmp_arr = [],
    i = 0,
    ac = 0,
    c1 = 0,
    c2 = 0,
    c3 = 0,
    c4 = 0;

    str_data += '';

    while (i < str_data.length) {
        c1 = str_data.charCodeAt(i);
        if (c1 <= 191) {
          tmp_arr[ac++] = String.fromCharCode(c1);
          i++;
        } else if (c1 <= 223) {
          c2 = str_data.charCodeAt(i + 1);
          tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
          i += 2;
        } else if (c1 <= 239) {
          // http://en.wikipedia.org/wiki/UTF-8#Codepage_layout
          c2 = str_data.charCodeAt(i + 1);
          c3 = str_data.charCodeAt(i + 2);
          tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
          i += 3;
        } else {
          c2 = str_data.charCodeAt(i + 1);
          c3 = str_data.charCodeAt(i + 2);
          c4 = str_data.charCodeAt(i + 3);
          c1 = ((c1 & 7) << 18) | ((c2 & 63) << 12) | ((c3 & 63) << 6) | (c4 & 63);
          c1 -= 0x10000;
          tmp_arr[ac++] = String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF));
          tmp_arr[ac++] = String.fromCharCode(0xDC00 | (c1 & 0x3FF));
          i += 4;
        }
    }

  return tmp_arr.join('');
}


</script>
<?php
include "modules/footer.php";
?>