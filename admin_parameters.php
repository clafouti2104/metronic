<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include "models/Device.php";
include "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminparameters"){
    $isPost=TRUE;
}

$devices=Device::getDevices();
$levels=Log::getLevels();
$themes=array(
    "default"=>"Noir",
    "darkblue"=>"Bleu Foncé",
    "blue"=>"Bleu",
    "grey"=>"Gris",
    "light"=>"Light",
    "light2"=>"Blanc",
);

$logLevelBDD = 10;
$alertRecallBDD=$emailBDD=$chartDefaultDevicesBDD=$loginGmailBDD=$passwordGmailBDD=$themeBDD="";

$sql = "SELECT * FROM config WHERE name IN ('log_level','chart_default_devices', 'general_email', 'login_gmail', 'password_gmail', 'theme', 'alert_recall')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'log_level':
            $logLevelBDD = $row["value"];
            break;
        case 'general_email':
            $emailBDD = $row["value"];
            break;
        case 'chart_default_devices':
            $chartDefaultDevicesBDD = explode("~",$row["value"]);
            break;
        case 'login_gmail':
            $loginGmailBDD = $row["value"];
            break;
        case 'password_gmail':
            $passwordGmailBDD = $row["value"];
            break;
        case 'theme':
            $themeBDD = $row["value"];
            break;
        case 'alert_recall':
            $alertRecallBDD = $row["value"];
            break;
        default:
    }
}

$logLevel= ($isPost) ? $_POST["log_level"] : $logLevelBDD;
$chartDefaultDevices= ($isPost) ? $_POST["chart_default_devices"] : $chartDefaultDevicesBDD;
$email= ($isPost) ? $_POST["general_email"] : $emailBDD;
$login_gmail= ($isPost) ? $_POST["login_gmail"] : $loginGmailBDD;
$password_gmail= ($isPost) ? $_POST["password_gmail"] : $passwordGmailBDD;
$theme= ($isPost) ? $_POST["theme"] : $themeBDD;
$alertRecall= ($isPost) ? $_POST["alert_recall"] : $alertRecallBDD;

if($isPost){
    if(isset($_POST["chart_default_devices"])){
        $chartDefaultDevices = implode("~", $chartDefaultDevices);
    }
    
    $sql="UPDATE config SET value='".$logLevel."' WHERE name='log_level'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    $sql="UPDATE config SET value='".$chartDefaultDevices."' WHERE name='chart_default_devices'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    $sql="UPDATE config SET value='".$email."' WHERE name='general_email'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    $sql="UPDATE config SET value='".$login_gmail."' WHERE name='login_gmail'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    $sql="UPDATE config SET value='".$password_gmail."' WHERE name='password_gmail'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    $sql="UPDATE config SET value='".$theme."' WHERE name='theme'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    $sql="UPDATE config SET value='".$alertRecall."' WHERE name='alert_recall'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
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
                    Paramètres				
                    <small>Personnaliser le fonctionnement</small>
                </h3>
                <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
            <form class="form-horizontal form" method="POST" action="admin_parameters.php">
                <div class="form-body">
                <input type="hidden" name="formname" id="formname" value="adminparameters" />
                <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Général</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="log_level">Niveau de log</label>
                            <div class="col-md-9">
                                <select id="type" name="log_level" class="form-control">
                                    <option value="-1"></option>
<?php
foreach($levels as $tmpLevel=>$labelLevel){
    $selected=(strtolower($tmpLevel) == strtolower($logLevel)) ? " selected=\"selected\" " : "";
    echo "<option value=\"".$tmpLevel."\"$selected>".ucwords($labelLevel)."</option>";
}
?>
                                </select>
                                <span class="help-inline">Détermine les logs à afficher</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="general_theme">Thème</label>
                            <div class="col-md-9">
                                <select name="theme" id="theme" class="form-control">
<?php
foreach($themes as $color=>$name){
    $selected=(strtolower($color) == strtolower($themeBDD)) ? " selected=\"selected\" " : "";
    echo '<option value="'.$color.'" '.$selected.'>'.$name.'</option>';
}
?>
                                </select>
                                <span class="help-inline">Thème de l'application</span>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="form-section"><i class="fa fa-envelope"></i>&nbsp;Mail</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="login_gmail">Login Gmail</label>
                            <div class="col-md-9">
                                <input class="form-control"name="login_gmail" placeholder="login@gmail.com" id="login_gmail" type="text" value="<?php echo $login_gmail; ?>">
                                <span class="help-block">Login du compte expéditeur</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="password_gmail">Mot de passe Gmail</label>
                            <div class="col-md-9">
                                <input class="form-control" name="password_gmail" id="password_gmail" type="password" value="<?php echo $password_gmail; ?>">
                                <span class="help-block">Mot de passe du compte expéditeur</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="general_email">Adresse Mail destinataire</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                    <input class="form-control "  name="general_email" id="general_email" type="text" value="<?php echo $email; ?>">
                                </div>
                                <button class="btn test-mail" type="button">Test</button>
                                <span class="help-block">En cas de perte de communication d'un équipement...</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="alert_recall">Rappel d'alerte</label>
                            <div class="col-md-9">
                                <select id="alert_recall" name="alert_recall" class="form-control">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <span class="help-block">Nombre de jours pour un rappel d'alerte</span>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="form-section"><i class="fa fa-bar-chart-o"></i>&nbsp;Graphique</h3>
                <div class="form-group">
                    <label class="control-label col-md-3" for="chart_default_devices">Device par défaut</label>
                    <div class="col-md-9">
                        <select id="type" name="chart_default_devices[]" class="form-control" multiple="multiple">
<?php
foreach($devices as $tmpDevice){
    //$selected=(strtolower($tmpLevel) == strtolower($logLevel)) ? " selected=\"selected\" " : "";
    $selected=(in_array($tmpDevice->id, $chartDefaultDevicesBDD)) ? " selected=\"selected\" " : "";
    echo "<option value=\"".$tmpDevice->id."\"$selected>".ucwords($tmpDevice->name)." - ".$tmpDevice->type."</option>";
}
?>
                        </select>
                        <span class="help-inline">Détermine les valeurs du graph à afficher par défaut</span>
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
});
</script>
<?php
include "modules/footer.php";
?>