<?php
$includeJS=$includeCSS=array();
$includeJS[] = "/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js";   
$includeJS[] = "/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js";   
//$includeJS[] = "/assets/admin/pages/scripts/components-jqueryui-sliders.js";   

$includeJS[] = "/assets/js/wurfl.js";   
include "modules/header.php";
include "modules/sidebar.php";

include_once "models/Page.php";

$GLOBALS["dbconnec"] = connectDB();
if(!isset($_GET["pageId"])){
    die('Aucune page définie');
}

$page= Page::getPage($_GET["pageId"]);
$pageFilles = Page::getPageFilles($_GET["pageId"]);
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <input type="hidden" id="pageId" value="<?php echo $page->id; ?>" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    <?php echo $page->name; ?>&nbsp;
                    <input type="hidden" id="editMode" value="0" />
                    <small><?php echo $page->description; ?></small>
                    <?php if($deviceType == "computer"){ ?>
                    <a style="float:right;" class="btn btn-primary" href="ajax/user/itempage_add.php?pageId=<?php echo $page->id; ?>" data-target="#ajax" data-toggle="modal">
                        <i class="fa fa-plus"></i>&nbsp;Ajouter un objet
                    </a>
                    <a style="float:right;margin-right:5px;" title="Gestion de l'ordre des objets" class="btn default btnEditMode" href="#" >
                        <i class="fa fa-wrench "></i>&nbsp;
                    </a>
                    <?php } ?>
                </h3>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
        </div>
        <div class="row">
            <div class="packery">
    <?php 
    foreach($pageFilles as $pageFille){
        echo "<div class=\"col-lg-3 col-md-3 col-sm-6 col-xs-12\" onclick=\"location.href='page.php?pageId=".$pageFille->id."'; \">";
        echo "<div class=\"dashboard-stat ".$pageFille->color." \">";
        echo "<div class=\"visual\">";
        echo "<i class=\"fa ".$pageFille->icon."\" ></i>";
        echo "</div>";
        echo "<div class=\"details\">";
        echo "<div class=\"number\">".$pageFille->name."</div>";
        echo "<div class=\"desc\">".$pageFille->description."</div>";
        echo "</div>";
        echo "<a class=\"more\" href=\"#\">";
        echo "&nbsp;<i class=\"m-icon-swapright m-icon-white\"></i>";
        echo "</a>";
        echo "</div>";
        echo "</div>";
    }
    ?>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="deleteItemPage" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="iditempagetodelete" value="" />
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression de l'objet?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelPageItemDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeletePageItemConfirm" data-dismiss="modal" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="ajax" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>
        &nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
        </div>
    </div>
</div>
    <script type="text/javascript" src="//wurfl.io/wurfl.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('#editMode').val('0');
    
</script>
<?php
include "modules/footer.php";
?>
