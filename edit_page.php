<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Page.php";

$pages = Page::getPageNonFilles();

$error="";
$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="editpage"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idpage"])){
    $name= $_POST["name"];
    $description= $_POST["description"];
    $active=$_POST["active"];
    $icon=$_POST["icon"];
    $parent=$_POST["parent"];
    $color=$_POST["color"];
    $idPage=$_POST["idpage"];
} else {
    $idPage=0;
    $txtMode="Création";
    $txtModeDesc="Création d'une page";
    if(isset($_GET["idPage"])){
        $idPage=$_GET["idPage"];
        $page = Page::getPage($idPage);
    } 
    $name= (!isset($page) || !is_object($page)) ? NULL : $page->name;
    $description= (!isset($page) || !is_object($page)) ? NULL : $page->description;
    $icon= (!isset($page) || !is_object($page)) ? NULL : $page->icon;
    $active= (!isset($page) || !is_object($page)) ? TRUE : $page->active;
    $parent= (!isset($page) || !is_object($page)) ? TRUE : $page->parent;
    $color= (!isset($page) || !is_object($page)) ? TRUE : $page->color;
}

if($isPost){
    //Controle
    if($_POST["name"] == ""){
        $error="Veuillez renseigner le nom";
    }
    
    if($error == ""){
        $_POST["icon"]=str_replace("fa ","",$_POST["icon"]);
        $_POST["parent"]=($_POST["parent"]=="-1") ? 'NULL' : $_POST["parent"];
        if($_POST["idpage"]>0){
            $_POST["active"] = ($_POST["active"] == "") ? 0 : $_POST["active"];
            $sql="UPDATE page SET name='".$_POST["name"]."', description='".$_POST["description"]."'";
            $sql.=", active=".$_POST["active"].", icon='".$_POST["icon"]."', parent=".$_POST["parent"].", color='".$_POST["color"]."'";
            $sql.=" WHERE id=".$_POST["idpage"];
            //echo $sql;
            $stmt = $GLOBALS["dbconnec"]->exec($sql);
            $info="La page a été modifiée";
        } else {
            $page=Page::createPage($_POST["name"], $_POST["description"], $_POST["active"],$_POST["icon"],$_POST["parent"],$_POST["color"]);
            $idPage=$page->id;
            $info="Le page a été créée";
        }
    }
}

if(isset($idPage) && $idPage > 0){
    $txtMode="Edition";
    $txtModeDesc="Edition d'une page";
}
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
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <!--<li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_page.php';"><i class="fa fa-plus"></i>Ajouter une page</button>
                </li>-->
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_page.php">Liste des pages</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?php echo $txtMode; ?></a>
                    <i class="icon-angle-right"></i>
                </li>
            </ul>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <?php if($error!=""){echo "<div class=\"alert alert-danger\">".$error."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" action="edit_page.php">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gears"></i>
                            Gestion
                        </div>
                        <div class="actions btn-set">
                            <a href="admin_page.php" class="btn default">
                                <i class="fa fa-angle-left"></i>
                                Retour
                            </a>
                            <button class="btn green" type="submit">
                                <i class="fa fa-check"></i>
                                Valider
                            </button>
                            <a href="edit_page.php" class="btn blue">
                                <i class="fa fa-plus"></i>
                                Ajouter
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form-body form">
                <input type="hidden" name="formname" id="formname" value="editpage" />
                <input type="hidden" name="idpage" id="idpage" value="<?php echo $idPage; ?>" />
                <div class="row">
                        <!--<h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Paramètres</h3>-->
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="name">Nom</label>
                                    <div class="col-md-9">
                                        <input id="name" name="name" class="form-control" value="<?php echo $name; ?>" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="description">Description</label>
                                    <div class="col-md-9">
                                        <input id="description" name="description" class="form-control" value="<?php echo $description; ?>" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="parent">Parent</label>
                                    <div class="col-md-9">
                                        <select id="parent" name="parent" class="form-control">
                                            <option value="-1"></option>
                                            <?php 
                                            foreach($pages as $pageTmp){
                                                $selected = ($pageTmp->id == $parent) ? ' selected="selected" ' : '';
                                                echo '<option value="'.$pageTmp->id.'" '.$selected.'>'.ucwords($pageTmp->name).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="icon">Icône</label>
                                    <div class="col-md-4">
                                        <input id="icon" name="icon" class="form-control" value="<?php echo $icon; ?>" type="text">
                                    </div>
                                    <div class="col-md-4">
                                        <a class="btn btn-info"  data-toggle="modal" href="edit_page.php#basic">Icônes</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="color">Couleur</label>
                                    <div class="col-md-9">
                                        <select name="color" id="color" class="form-control">

<?php
                                        foreach($colors as $colorType=>$array){
                                            echo "<optgroup label=\"".ucwords($colorType)."\">";
                                            foreach($array as $key=>$colorTmp){
                                                $selected=($key==$color) ? " selected=\"selected\" " : "";
                                                echo "<option value=\"".$key."\" $selected>".ucwords($colorTmp)."</option>";
                                            }
                                            echo "</optgroup>";
                                        }
?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="active">Actif</label>
                                    <div class="col-md-9">
                                        <input id="active" name="active" class="form-control" value="1" <?php if($active){echo " checked=\"checked\"";} ?> type="checkbox">
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
    
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modalClose" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Sélection d'une icône</h3>
            </div>
            <div class="modal-body">
                <div class="tab-content">
                    <?php include "icons.php"; ?>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script description="text/javascript">
$(document).ready(function () {
    $('.box').bind('click',function(e){
        $.ajax({
            url: "ajax/action.php",
            description: "POST",
            data: {
                description:  encodeURIComponent($(this).attr('description')),
                pageId: $(this).attr('pageId'),
                action: $(this).attr('action')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                $.bootstrapGrowl("Action exécutée","info");
            }
        });
    });
    $('.btnPlayMessage').bind('click',function(e){
        $.ajax({
            url: "ajax/execute.php",
            description: "POST",
            data: {
                description:  'message',
                messageId: $(this).attr('idMessage')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data == "error"){
                    $.bootstrapGrowl("Une erreur est survenue",{"description":"danger"});
                } else {
                    $.bootstrapGrowl("Action exécutée","info");
                }
            }
        });
    });
    $('.fa').bind('click',function(e){
        var classes=$(this).attr('class');
        classes.replace('','fa ');
        console.debug(classes);
        $('.modalClose').click();
        $('#icon').val(classes);
    });
});
</script>
<?php
include "modules/footer.php";
?>