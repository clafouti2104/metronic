<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/select2/select2.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/jquery.dataTables.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/DT_bootstrap.js";
$includeCSS[] = "/assets/global/plugins/select2/select2.css";
$includeCSS[] = "/assets/global/plugins/data-tables/DT_bootstrap.css";
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();

include_once "models/Page.php";
$pages = Page::getPages(FALSE);

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    Page				
                    <small>Liste des pages</small>
                </h3>
                <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
                <ul class="page-breadcrumb breadcrumb">
                    <li class="btn-group">
                        <button type="button" class="btn blue dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                        <span>Actions</span><i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <a href="edit_page.php"><i class="fa fa-plus"></i>Ajouter une page</a>
                            </li>
                            <li>
                                <a href="admin_page_order.php">Gestion de l'ordre</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="index.php">Admin</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>   
                        <a href="#">Liste des pages</a>
                    </li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover" id="datatable">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Actif</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($pages as $page){
    $actif=($page->active) ? "fa fa-check-square" : "fa fa-square";
    
    echo "<tr class=\"odd gradeX linepage-".$page->id."\" >";
    echo "<td><a class=\"black\" href=\"edit_page.php?idPage=".$page->id."\">".$page->name."</a></td>";
    echo "<td><i class=\"".$actif."\"></i></td>";
    echo "<td><a href=\"edit_page.php?idPage=".$page->id."\"><i class=\"fa fa-edit\" style=\"color:black;\"></i></a>";
    echo "<a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-minus-circle btnDeletePage\" style=\"color:black;\" pageId=\"".$page->id."\"></i></a></td>";
    echo "</tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {			
        $('.btnDeletePage').bind('click',function(e){
            var pageId=$(this).attr('pageId');
            if(confirm("Etes vous sur de vouloir supprimer le page?")){
                $.ajax({
                    url: "ajax/page_delete.php",
                    type: "POST",
                    data: {
                        pageId: pageId
                    },
                    error: function(data){
                        toastr.error("Une erreur est survenue");
                    },
                    complete: function(data){
                        if(data = "done"){
                            toastr.info("Page supprimée");
                            $('.linepage-'+pageId).toggle('hide');
                        } else {
                            toastr.error("Une erreur est survenue");
                        }
                    }
                });
            }
        });
        
        // begin first table
        $('#datatable').dataTable({
            "aoColumns": [
              { "bSortable": true },
              { "bSortable": false},
              { "bSortable": false}
            ],
            "aLengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "iDisplayLength": 20,
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "_MENU_ entrées",
                "oPaginate": {
                    "sPrevious": "Préc.",
                    "sNext": "Suiv."
                }
            },
            "aoColumnDefs": [
            ]
        });
        
    });
</script>
<?php
include "modules/footer.php";
?>