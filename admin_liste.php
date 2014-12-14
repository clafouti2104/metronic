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

include "models/Liste.php";
include "models/ListeMessage.php";
$listes = Liste::getListes();
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                Liste				
                <small>Liste des listes</small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_liste.php';"><i class="fa fa-plus"></i>Ajouter un liste</button>
                </li>
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="#">Liste des listes</a>
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
                    <th>Nbr Objets</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($listes as $liste){
    $listeMessages=ListeMessage::getListeMessagesForListe($liste->id);
    echo "<tr class=\"odd gradeX lineliste-".$liste->id."\" >";
    echo "<td>".$liste->name."</td>";
    echo "<td>".count($listeMessages)."</td>";
    echo "<td><a href=\"edit_liste.php?idListe=".$liste->id."\"><i class=\"fa fa-edit\"  style=\"color:black;\" ></i></a>";
    echo "<a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-minus-circle btnDeleteListe\" listeId=\"".$liste->id."\"  style=\"color:black;\"></i></a></td>";
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
        $('.btnDeleteListe').bind('click',function(e){
            var listeId=$(this).attr('listeId');
            if(confirm("Etes vous sur de vouloir supprimer le liste?")){
                $.ajax({
                    url: "ajax/liste_delete.php",
                    type: "POST",
                    data: {
                        listeId: listeId
                    },
                    error: function(data){
                        toastr.error("Une erreur est survenue");
                    },
                    complete: function(data){
                        if(data = "done"){
                            toastr.info("Liste supprimée");
                            $('.lineliste-'+listeId).toggle('hide');
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