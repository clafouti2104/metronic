<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Tuile.php";
include_once "models/Device.php";

$devices=  Device::getDevices();

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="edittuile"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idTuile"])){
    $name= $_POST["name"];
    $deviceid= $_POST["deviceid"];
    $color= $_POST["color"];
    $idTuile=$_POST["idTuile"];
} else {
    $idTuile=0;
    $txtMode="Création";
    $txtModeDesc="Création d'une tuile";
    if(isset($_GET["idTuile"])){
        $idTuile=$_GET["idTuile"];
        $tuile = Tuile::getTuile($idTuile);
    } 
    $name= (isset($tuile) && !is_object($tuile)) ? NULL : $tuile->name;
    $deviceid= (isset($tuile) && !is_object($tuile)) ? NULL : $tuile->deviceid;
    $color= (isset($tuile) && !is_object($tuile)) ? NULL : $tuile->color;
}

if($isPost){
    //Controle
    if($_POST["name"] == ""){
        $error="Veuillez renseigner le nom";
    }
    
    if($error == ""){
        if($_POST["idTuile"]>0){
            $_POST["active"] = ($_POST["active"] == "") ? 0 : $_POST["active"];
            $sql="UPDATE tuile SET name='".$_POST["name"]."', deviceid='".$_POST["deviceid"]."', color='".$_POST["color"]."'";
            $sql.=" WHERE id=".$_POST["idTuile"];
            //echo $sql;
            $stmt = $GLOBALS["dbconnec"]->exec($sql);
            $info="La tuile a été modifiée";
        } else {
            $tuile=Tuile::createTuile($_POST["name"], $_POST["deviceid"], $_POST["color"]);
            $idTuile=$tuile->id;
            $info="Le tuile a été créée";
        }
    }
}

if(isset($idTuile) && $idTuile > 0){
    $txtMode="Edition";
    $txtModeDesc="Edition d'une tuile";
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
                Tuile				
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_tuile.php';"><i class="fa fa-plus"></i>Ajouter une tuile</button>
                </li>
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_tuile.php">Liste des tuiles</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?php echo $txtMode; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
            </ul>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <?php if($error!=""){echo "<div class=\"alert alert-danger\">".$error."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" action="edit_tuile.php">
                <div class="form-body form">
                <input type="hidden" name="formname" id="formname" value="edittuile" />
                <input type="hidden" name="idTuile" id="idTuile" value="<?php echo $idTuile; ?>" />
                <div class="row">
                        <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Paramètres</h3>
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
                                    <label class="control-label col-md-3" for="deviceid">Device</label>
                                    <div class="col-md-9">
                                        <select name="deviceid" id="deviceid" class="form-control">
<?php
                                        foreach($devices as $deviceTmp){
                                            $selected=($deviceTmp->id == $deviceid) ? " selected=\"selected\" " : "";
                                            $deviceType=($deviceTmp->type != "") ? " - ".$deviceTmp->type : "";
                                            echo "<option value=\"".$deviceTmp->id."\" $selected>".$deviceTmp->name.$deviceType."</option>";
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
                </div>
                <div class="form-actions">
                    <button class="btn blue" deviceid="submit">
                        <i class="icon-ok"></i>Valider
                    </button>
                    <a href="admin_tuile.php"><button class="btn" deviceid="button">Retourner</button></a>
                </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
<script deviceid="text/javascript">
$(document).ready(function () {
    $('.box').bind('click',function(e){
        $.ajax({
            url: "ajax/action.php",
            deviceid: "POST",
            data: {
                deviceid:  encodeURIComponent($(this).attr('deviceid')),
                tuileId: $(this).attr('tuileId'),
                action: $(this).attr('action')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                toastr.success("Action exécutée");
            }
        });
    });
    $('.btnPlayMessage').bind('click',function(e){
        $.ajax({
            url: "ajax/execute.php",
            deviceid: "POST",
            data: {
                deviceid:  'message',
                messageId: $(this).attr('idMessage')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    toastr.success("Action exécutée");
                }
            }
        });
    });
});
</script>
<?php
include "modules/footer.php";
?>