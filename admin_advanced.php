<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();

$notifications=array();
$sqlNotifications = "SELECT * FROM config WHERE name='pushing_box'";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlNotifications);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $notifications[$row["comment"]] = $row["value"];
}

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminadvanced"){
    $isPost=TRUE;
}


$zwaveIpAddressBDD="";

$sql = "SELECT * FROM config WHERE name IN (";
$sql .= " 'zwave_ip_address')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'zwave_ip_address':
            $zwaveIpAddressBDD = $row["value"];
            break;
        default:
    }
}

$zwaveIpAddress= ($isPost) ? $_POST["zwave_ip_address"] : $zwaveIpAddressBDD;

if($isPost){
    $sql="UPDATE config SET value='".$zwaveIpAddress."' WHERE name='zwave_ip_address';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
            case 'zwave_ip_address':
                $value = $zwaveIpAddress;
                break;
            default:
        }
        $content .="\n\t".$title."=\"".$value."\"";
        
    }
    file_put_contents("/var/www/metronic/tools/parameters.ini", $content);
    
    $info="Modifications enregistrées avec succès";
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
                    Protocoles				
                    <small>Paramètres avancés</small>
                </h3>
                <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="tabbable-custom ">
        <ul class="nav nav-tabs ">
            <li class="active">
                <a href="admin_advanced.php#tab_protocol" data-toggle="tab">Protocoles</a>
            </li>
            <li>
                <a href="admin_advanced.php#tab_notification" data-toggle="tab">Pushing Box</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_protocol">
                <div class="form-body">
                <input type="hidden" name="formname" id="formname" value="adminadvanced" />
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="thumbnail">
                            <a href="admin_knx.php">
                                <img src="assets/img/knx_logo.png" title="Administration KNX" alt="Administration KNX" />
                            </a>
                        </div>
                        <div class="caption">
                            <h4>Administration KNX</h4>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="thumbnail">
                            <a href="admin_myfox.php">
                                <img src="assets/img/myfox_logo.png" title="Administration MyFox" alt="Administration MyFox" />
                            </a>
                        </div>
                        <div class="caption">
                            <h4>Administration MyFox</h4>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="thumbnail">
                            <a href="admin_netatmo.php">
                                <img src="assets/img/netatmo_logo.png" title="Administration Netatmo" alt="Administration Netatmo" />
                            </a>
                        </div>
                        <div class="caption">
                            <h4>Administration Netatmo</h4>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="thumbnail">
                            <a href="admin_calaos.php">
                                <img src="assets/img/calaos_logo.png" title="Administration Calaos" alt="Administration Calaos" />
                            </a>
                        </div>
                        <div class="caption">
                            <h4>Administration Calaos</h4>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="thumbnail">
                            <a href="admin_zibase.php">
                                <img src="assets/img/zibase_logo.jpg" title="Administration Zibase" alt="Administration Zibase" />
                            </a>
                        </div>
                        <div class="caption">
                            <h4>Administration Zibase</h4>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="thumbnail">
                            <a href="admin_zwave.php">
                                <img src="assets/img/zwave_logo.png" title="Administration ZWave" alt="Administration ZWave" />
                            </a>
                        </div>
                        <div class="caption">
                            <h4>Administration ZWave</h4>
                        </div>
                    </div>
                </div>

                </div>
            </div>
            <div class="tab-pane" id="tab_notification">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="form-section"><i class="fa fa-comment-o "></i>&nbsp;Notifications
                        &nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="modal" href="admin_advanced.php#addNotification" class="btn btn-primary btnAddNotification" type="button"><i class="fa fa-plus"></i>&nbsp;Ajouter une notification</a>
                        </h3>
        <?php 
    if(count($notifications)>0){
        ?>
                        <table class="table table-striped table-hover">
                            <tr>
                                <th>Nom</th>
                                <th>Device ID</th>
                                <th></th>
                            </tr>
        <?php
        foreach($notifications as $notificationName=>$notificationDeviceId){
                        echo "<tr id=\"line-".$notificationDeviceId."\">";
                        echo "<td>".$notificationName."</td>";
                        echo "<td>".$notificationDeviceId."</td>";
                        echo "<td><i class=\"fa fa-play btnPlayNotification\" title=\"Tester\" idNotification=\"".$notificationDeviceId."\" style=\"cursor:pointer;\"></i>&nbsp;&nbsp;";
                        echo "<a href=\"admin_advanced.php#deleteNotification\" data-toggle=\"modal\" ><i class=\"fa fa-trash-o btnDeleteidNotification\" title=\"Supprimer\" ididNotification=\"".$notificationDeviceId."\" style=\"cursor:pointer;color:black;\"></i></a></td>";
                        echo "</tr>";
        }
        ?>
                        </table>
        <?php 
    }
        ?>
                    </div>
                </div>      
            </div>
        </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteNotification" tabindex="-1" role="basic" aria-hidden="true">
    <input type="hidden" id="idnotification" name="idnotification" value="" />
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression de la notification?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelNotificationDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeleteNotificationConfirm" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addNotification" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idalert" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span>Ajout d'une notification</span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="notificationName">Nom</label>
                            <div class="col-md-9">
                                <input id="notificationName" name="notificationName" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="notificationDeviceID">Device Id</label>
                            <div class="col-md-9">
                                <input id="notificationDeviceID" name="notificationDeviceID" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelNotification" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitNotification" type="button">Valider</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/admin/pages/scripts/form-validation.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('.test-mail').bind( "click", function() {
        $.ajax({
           url: 'controllers/sendmail_test.php',
           type: 'POST',
           data: { mail : $('#general_email').val() },
           error: function(data){
                toastr.error("Une erreur est survenue");
            },
           complete: function(data){
              $.bootstrapGrowl("Action exécutée","info");
           }
        });
    });
    
    
    $('.btnAddNotification').bind('click',function(e){
        $('#notificationName').val('');
        $('#notificationDeviceID').val('');
    });
    
    $('.btnDeleteNotification').bind('click',function(e){
        var idNotification=$(this).attr('idNotification');
        $('#idnotificaiton').val(idNotification);
    });
    
    $('.btnSubmitNotification').bind('click',function(e){
        $.ajax({
            url: "ajax/notification_submit.php",
            type: "POST",
            data: {
                deviceId: $('#notificationDeviceID').val(),
                name: $('#notificationName').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "exist" || data == "exist"){
                    toastr.error("Device ID existe déjà");
                }
                if(data == "error"){
                    toastr.error("Une erreur est survenue");
                } 
                if(data != "error" && data != "exist") {
                    $('.btnCancelNotification').click();
                    toastr.info("Veuillez recharger","Message ajouté");
                    
                }
            }
        });
    });
    
    $('.btnPlayNotification').bind('click',function(e){
        $.ajax({
            url: "ajax/notification_play.php",
            type: "POST",
            data: {
                notificationId: $(this).attr('idNotification')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data != "error" && data != "exist") {
                    toastr.info("Notification envoyée");
                }
            }
        });
    });
    
    $('.btnDeleteNotificationConfirm').bind('click',function(e){
        var notificationId=$('#idnotification').val();
        $.ajax({
            url: "ajax/delete_notification.php",
            type: "POST",
            data: {
                notificationId:  notificationId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelNotificationDeletion').click();
                    $('#line-'+notificationId).fadeOut(300, function(){ 
                       $('#line-'+notificationId).remove(); 
                    });
                    toastr.info("Message supprimé");
                }
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