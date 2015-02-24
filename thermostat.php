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
    margin-left: 30px;
}
#advanced {
    width: 400px;
    height: 400px;

    background-image: -moz-radial-gradient(45px 45px 45deg, circle cover, #DC6812 0%, #DD491B 100%, red 95%);
    background-image: -webkit-radial-gradient(45px 45px, circle cover, #DC6812, #DD491B);
    background-image: radial-gradient(45px 45px 45deg, circle cover, #DC6812 0%, #DD491B 100%, red 95%);
    /*animation-name: spin; 
    animation-duration: 3s; 
    animation-iteration-count: infinite; 
    animation-timing-function: linear;*/
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
                <div class="circle" id="advanced"> <span class="degree" style="position: absolute; left: 180px; color: rgb(255, 255, 255); font-size: 100px; font-weight: 600; top: 120px;">22</span> </div>
                <input id="temp_salon" type="text" name="temp_salon" value="24"/>
            </div>
            <div class="col-md-6">
                <div class="circle" id="advanced"> </div>
                <input id="temp_sdb" type="text" name="temp_sdb" value="24"/>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $("#temp_salon").ionRangeSlider({
        min: 16,
        max: 26,
        from: 0,
        type: 'single',
        step: 1,
        keyboard: true,
        hasGrid: true,
        grid: true,
        grid_snap: true
    });
    $("#temp_sdb").ionRangeSlider({
        min: 16,
        max: 26,
        from: 0,
        type: 'single',
        step: 1,
        keyboard: true,
        hasGrid: true,
        grid: true,
        grid_snap: true
    });
});
</script>
<?php
include "modules/footer.php";
?>