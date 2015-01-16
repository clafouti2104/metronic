<?php
$includeCSS = $includeJS = array();
$includeCSS[] = "/assets/admin/pages/css/error.css";
$includeJS[] = "/assets/global/plugins/jquery.blockui.min.js";

include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();

$distribInfos = exec('cat /etc/*-release',$output);
foreach($output as $distribInfo){
    $distribInfo = explode("=",$distribInfo);
    if(isset($distribInfo[0]) && isset($distribInfo[1]) && strtolower($distribInfo[0]) == "pretty_name"){
        $systemDistrib=$distribInfo[1];
    }
}
print_r($distribInfos);
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                Maintenance				
                <small>Gestion des sauvegardes et autres</small>
            </h3>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <?php if($error!=""){echo "<div class=\"alert alert-danger\">".$error."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="tabbable-custom ">
        <ul class="nav nav-tabs ">
            <li class="active">
                <a href="admin_maintenance.php#tab_information" data-toggle="tab">Informations</a>
            </li>
            <li>
                <a href="admin_maintenance.php#tab_save" data-toggle="tab">Sauvegardes</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_information">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="portlet yellow-crusta box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>
                                    Système
                                </div>
                            </div>
                            <div class="portlet-body">
                                <?php if(isset($systemDistrib)){ ?>
                                <div class="row static-info">
                                    <div class="col-md-5 name"> Distribution: </div>
                                    <div class="col-md-7 value"> <?php echo $systemDistrib; ?> </div>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_save">
                <div class="row">
                    <div class="col-md-9 col-sm-12">
                        <div class="portlet blue-hoki box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-save"></i>
                                    Fichiers
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Taille</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php 
$directory = '/var/www/save';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));
//print_r($scanned_directory);
foreach($scanned_directory as $file){
    $size = filesize($directory."/".$file)/1000000;
    echo "<tr>";
    echo "<td>".$file."</td>";
    echo "<td>". number_format($size,2) ." Mo</td>";
    echo "<td><a href=\"save/$file\"><i class=\"fa fa-download\"></i></a></td>";
    echo "</tr>";
}
?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div class="portlet yellow-crusta box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>
                                    Opérations
                                </div>
                            </div>
                            <div class="portlet-body">
                                <a class="btn default btnSave" href="#">
                                    Sauvegarder
                                    <i class="fa fa-save"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $('.btnSave').bind('click',function(e){
        $.ajax({
            url: "ajax/system/save.php",
            type: "POST",
            data: {
                action: "save"
            },
            beforeSend: function(data){
                Metronic.blockUI({boxed: true});
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
                Metronic.unblockUI();
            },
            success: function(data){
                toastr.success("Sauvegarde effectuée");
                Metronic.unblockUI();
            }
        });
    });
});
</script>
<?php
include "modules/footer.php";
?>