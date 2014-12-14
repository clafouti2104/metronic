<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    Scénario				
                    <small></small>
                </h3>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
        </div>
        <div class="row-fluid">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 box" type="sce-cd">
                    <div class="dashboard-stat blue">
                            <div class="visual">
                                <i class="fa fa-eye"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    Nuit
                                </div>
                                <div class="desc">									
                                    Extinction globale
                                </div>
                            </div>
                            <a class="more" href="#">
                                Info <i class="m-icon-swapright m-icon-white"></i>
                            </a>						
                    </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 box" type="sce-cd">
                    <div class="dashboard-stat blue-hoki">
                            <div class="visual">
                                <i class="fa fa-eye"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    Surveillance
                                </div>
                                <div class="desc">									
                                    Envoie image caméra
                                </div>
                            </div>
                            <a class="more" href="#">
                                Info <i class="m-icon-swapright m-icon-white"></i>
                            </a>						
                    </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    
});
</script>
<?php
include "modules/footer.php";
?>