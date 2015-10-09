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

$myfoxLoginBDD=$myfoxPasswordBDD=$myfoxSiteidBDD="";
$netatmoClientIdBDD=$netatmoClientSecretBDD=$netatmoLoginBDD=$netatmoPasswordBDD="";
$calaosIpAddressBDD=$calaosLoginBDD=$calaosPasswordBDD="";
$zibaseLoginBDD=$zibasePasswordBDD="";
$zwaveIpAddress=

$sql = "SELECT * FROM config WHERE name IN ('myfox_login','myfox_password', 'myfox_siteid', ";
$sql .= " 'netatmo_client_id', 'netatmo_client_secret', 'netatmo_login', 'netatmo_password', ";
$sql .= " 'calaos_ip_address', 'calaos_login', 'calaos_password', 'zibase_login', 'zibase_password', 'zwave_ip_address')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'myfox_login':
            $myfoxLoginBDD = $row["value"];
            break;
        case 'myfox_password':
            $myfoxPasswordBDD = $row["value"];
            break;
        case 'myfox_siteid':
            $myfoxSiteidBDD = $row["value"];
            break;
        case 'netatmo_client_id':
            $netatmoClientIdBDD= $row["value"];
            break;
        case 'netatmo_client_secret':
            $netatmoClientSecretBDD= $row["value"];
            break;
        case 'netatmo_login':
            $netatmoLoginBDD = $row["value"];
            break;
        case 'netatmo_password':
            $netatmoPasswordBDD = $row["value"];
            break;
        case 'calaos_ip_address':
            $calaosIpAddressBDD = $row["value"];
            break;
        case 'calaos_login':
            $calaosLoginBDD = $row["value"];
            break;
        case 'calaos_password':
            $calaosPasswordBDD = $row["value"];
            break;
        case 'zibase_login':
            $zibaseLoginBDD = $row["value"];
            break;
        case 'zibase_password':
            $zibasePasswordBDD = $row["value"];
            break;
        case 'zwave_ip_address':
            $zwaveIpAddressBDD = $row["value"];
            break;
        default:
    }
}

$myfoxLogin= ($isPost) ? $_POST["myfox_login"] : $myfoxLoginBDD;
$myfoxPassword= ($isPost) ? $_POST["myfox_password"] : $myfoxPasswordBDD;
$myfoxSiteid= ($isPost) ? $_POST["myfox_siteid"] : $myfoxSiteidBDD;
$netatmoClientId= ($isPost) ? $_POST["netatmo_client_id"] : $netatmoClientIdBDD;
$netatmoClientSecret= ($isPost) ? $_POST["netatmo_client_secret"] : $netatmoClientSecretBDD;
$netatmoLogin= ($isPost) ? $_POST["netatmo_login"] : $netatmoLoginBDD;
$netatmoPassword= ($isPost) ? $_POST["netatmo_password"] : $netatmoPasswordBDD;
$calaosIpAddress= ($isPost) ? $_POST["calaos_ip_address"] : $calaosIpAddressBDD;
$calaosLogin= ($isPost) ? $_POST["calaos_login"] : $calaosLoginBDD;
$calaosPassword= ($isPost) ? $_POST["calaos_password"] : $calaosPasswordBDD;
$zibaseLogin= ($isPost) ? $_POST["zibase_login"] : $zibaseLoginBDD;
$zibasePassword= ($isPost) ? $_POST["zibase_password"] : $zibasePasswordBDD;
$zwaveIpAddress= ($isPost) ? $_POST["zwave_ip_address"] : $zwaveIpAddressBDD;


if($isPost){
    
    $sql="UPDATE config SET value='".$myfoxLogin."' WHERE name='myfox_login';";
    $sql.="UPDATE config SET value='".$myfoxPassword."' WHERE name='myfox_password';";
    $sql.="UPDATE config SET value='".$myfoxSiteid."' WHERE name='myfox_siteid';";
    $sql.="UPDATE config SET value='".$netatmoClientId."' WHERE name='netatmo_client_id';";
    $sql.="UPDATE config SET value='".$netatmoClientSecret."' WHERE name='netatmo_client_secret';";
    $sql.="UPDATE config SET value='".$netatmoLogin."' WHERE name='netatmo_login';";
    $sql.="UPDATE config SET value='".$netatmoPassword."' WHERE name='netatmo_password';";
    $sql.="UPDATE config SET value='".$calaosIpAddress."' WHERE name='calaos_ip_address';";
    $sql.="UPDATE config SET value='".$calaosLogin."' WHERE name='calaos_login';";
    $sql.="UPDATE config SET value='".$calaosPassword."' WHERE name='calaos_password';";
    $sql.="UPDATE config SET value='".$zibaseLogin."' WHERE name='zibase_login';";
    $sql.="UPDATE config SET value='".$zibasePassword."' WHERE name='zibase_password';";
    $sql.="UPDATE config SET value='".$zwaveIpAddress."' WHERE name='zwave_ip_address';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
            case "myfox_login":
                $value = $myfoxLogin;
                break;
            case "myfox_password":
                $value = $myfoxPassword;
                break;
            case "myfox_siteid":
                $value = $myfoxSiteid;
                break;
            case 'netatmo_client_id':
                $value = $netatmoClientId;
                break;
            case 'netatmo_client_secret':
                $value = $netatmoClientSecret;
                break;
            case 'netatmo_login':
                $value = $netatmoLogin;
                break;
            case 'netatmo_password':
                $value = $netatmoPassword;
                break;
            case 'calaos_ip_address':
                $value = $calaosIpAddress;
                break;
            case 'calaos_login':
                $value = $calaosLogin;
                break;
            case 'calaos_password':
                $value = $calaosPassword;
                break;
            case 'zibase_login':
                $value = $zibaseLogin;
                break;
            case 'zibase_password':
                $value = $zibasePassword;
                break;
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
            <form class="form-horizontal form" method="POST" action="admin_advanced.php">
                <div class="form-body">
                <input type="hidden" name="formname" id="formname" value="adminadvanced" />
                <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;MyFox</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="myfox_login">Login</label>
                            <div class="col-md-9">
                                <input class="form-control"name="myfox_login" id="myfox_login" type="text" value="<?php echo $myfoxLogin; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="myfox_password">Mot de passe</label>
                            <div class="col-md-9">
                                <input class="form-control"name="myfox_password" id="myfox_password" type="password" value="<?php echo $myfoxPassword; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="myfox_siteid">Code Site</label>
                            <div class="col-md-9">
                                <input class="form-control"name="myfox_siteid" id="myfox_siteid" type="text" value="<?php echo $myfoxSiteid; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Netatmo</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="netatmo_client_id">Client Id</label>
                            <div class="col-md-9">
                                <input class="form-control"name="netatmo_client_id" id="netatmo_client_id" type="text" value="<?php echo $netatmoClientId; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="netatmo_client_secret">Client Secret</label>
                            <div class="col-md-9">
                                <input class="form-control"name="netatmo_client_secret" id="netatmo_client_secret" type="text" value="<?php echo $netatmoClientSecret; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="netatmo_login">Login</label>
                            <div class="col-md-9">
                                <input class="form-control"name="netatmo_login" id="netatmo_login" type="text" value="<?php echo $netatmoLogin; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="netatmo_password">Mot de passe</label>
                            <div class="col-md-9">
                                <input class="form-control"name="netatmo_password" id="netatmo_password" type="password" value="<?php echo $netatmoPassword; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Calaos</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="calaos_login">Login</label>
                            <div class="col-md-9">
                                <input class="form-control"name="calaos_login" id="calaos_login" type="text" value="<?php echo $calaosLogin; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="calaos_password">Mot de passe</label>
                            <div class="col-md-9">
                                <input class="form-control"name="calaos_password" id="calaos_password" type="password" value="<?php echo $calaosPassword; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="calaos_ip_address">Adresse IP</label>
                            <div class="col-md-9">
                                <input class="form-control"name="calaos_ip_address" id="calaos_ip_address" type="text" value="<?php echo $calaosIpAddress; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Zibase</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="zibase_login">Login</label>
                            <div class="col-md-9">
                                <input class="form-control"name="zibase_login" id="zibase_login" type="text" value="<?php echo $zibaseLogin; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="zibase_password">Mot de passe</label>
                            <div class="col-md-9">
                                <input class="form-control"name="zibase_password" id="zibase_password" type="password" value="<?php echo $zibasePassword; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;ZWave</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="zwave_ip_address">Adresse IP Razberry</label>
                            <div class="col-md-9">
                                <input class="form-control"name="zwave_ip_address" id="zwave_ip_address" type="text" value="<?php echo $zwaveIpAddress; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn blue" type="submit">
                        <i class="icon-ok"></i>Valider
                    </button>
                    <a href="admin_device.php"><button class="btn" type="button">Retourner</button></a>
                </div>
                </div>
            </form>
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