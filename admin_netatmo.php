<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminnetatmo"){
    $isPost=TRUE;
}

$netatmoClientIdBDD=$netatmoClientSecretBDD=$netatmoLoginBDD=$netatmoPasswordBDD="";

$sql = "SELECT * FROM config WHERE name IN ('netatmo_client_id', 'netatmo_client_secret', 'netatmo_login', 'netatmo_password')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'netatmo_client_id':
            $netatmoClientIdBDD = $row["value"];
            break;
        case 'netatmo_client_secret':
            $netatmoClientSecretBDD = $row["value"];
            break;
        case 'netatmo_login':
            $netatmoLoginBDD = $row["value"];
            break;
        case 'netatmo_password':
            $netatmoPasswordBDD = $row["value"];
            break;
        default:
    }
}

$netatmoClientId= ($isPost) ? $_POST["netatmo_client_id"] : $netatmoClientIdBDD;
$netatmoClientSecret= ($isPost) ? $_POST["netatmo_client_secret"] : $netatmoClientSecretBDD;
$netatmoLogin= ($isPost) ? $_POST["netatmo_login"] : $netatmoLoginBDD;
$netatmoPassword= ($isPost) ? $_POST["netatmo_password"] : $netatmoPasswordBDD;

if($isPost){
    $sql="UPDATE config SET value='".$netatmoClientId."' WHERE name='netatmo_client_id';";
    $sql.="UPDATE config SET value='".$netatmoClientSecret."' WHERE name='netatmo_client_secret';";
    $sql.="UPDATE config SET value='".$netatmoLogin."' WHERE name='netatmo_login';";
    $sql.="UPDATE config SET value='".$netatmoPassword."' WHERE name='netatmo_password';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
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
                        Netatmo				
                        <small>Configuration Netatmo</small>
                    </h3>
                    <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <form class="form-horizontal form" method="POST" action="admin_netatmo.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="adminnetatmo" />
            <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Général</h3>
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
            <div class="form-actions">
                <button class="btn blue" type="submit">
                    <i class="icon-ok"></i>Valider
                </button>
                <a href="admin_advanced.php"><button class="btn" type="button">Retourner</button></a>
            </div>
        </form>
    </div>
</div>
<?php
include "modules/footer.php";
?>