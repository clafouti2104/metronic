<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/jquery-nestable/jquery.nestable.js"; 
$includeCSS[] = "/assets/global/plugins/jquery-nestable/jquery.nestable.css";
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
                    <small>Gestion de l'ordre des pages</small>
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
                        <a href="#">Gestion de l'ordre des pages</a>
                    </li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row">
        <div class="alert alert-info">Veuillez déplacer les pages pour modifier l'ordre</div>
        <div class="col-md-12">
            <div class="dd" id="nestable_list_2">
                <ol class="dd-list">
<?php
foreach($pages as $page){
    echo "<li class=\"dd-item\" data-id=\"".$page->id."\" >";
    echo "<div class=\"dd-handle\" pageId=\"".$page->id."\">".$page->name."</div>";
    echo "</li>";
}
?>
                </ol>
        </div>

        </div>
    </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {	
        $('#nestable_list_2').nestable().on('change', function() {
            var params="";
            $('#nestable_list_2 div.dd-handle').each(function( index ) {
                var pageId=$(this).attr('pageId');
                if(pageId != "" && typeof(pageId) !== "undefined"){
                    if(params != ""){
                        params = params + "~";
                    }
                    params = params + index + ":" + pageId;
                }
            });
            
            $.ajax({
                url: "ajax/page_update_order.php",
                method: "POST",
                data: {
                    pages: params
                },
                error: function(data){
                    toastr.error("Une erreur est survenue");
                },
                complete: function(data){
                    if(data.responseText == "error"){
                        toastr.error("Une erreur est survenue");
                    }
                }
            });
        });
        
    });
</script>
<?php
include "modules/footer.php";
?>