<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminmyfox"){
    $isPost=TRUE;
}

$myfoxLoginBDD=$myfoxPasswordBDD=$myfoxSiteidBDD=$myfoxClientidBDD=$myfoxClientSecretBDD="";

$sql = "SELECT * FROM config WHERE name IN ('myfox_login','myfox_password', 'myfox_siteid', 'myfox_client_id', 'myfox_client_secret')";
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
        case 'myfox_client_id':
            $myfoxClientidBDD = $row["value"];
            break;
        case 'myfox_client_secret':
            $myfoxClientSecretBDD = $row["value"];
            break;
        default:
    }
}

$myfoxLogin= ($isPost) ? $_POST["myfox_login"] : $myfoxLoginBDD;
$myfoxPassword= ($isPost) ? $_POST["myfox_password"] : $myfoxPasswordBDD;
$myfoxSiteid= ($isPost) ? $_POST["myfox_siteid"] : $myfoxSiteidBDD;
$myfoxClientid= ($isPost) ? $_POST["myfox_client_id"] : $myfoxClientidBDD;
$myfoxClientSecret= ($isPost) ? $_POST["myfox_client_secret"] : $myfoxClientSecretBDD;

if($isPost){
    $sql="UPDATE config SET value='".$myfoxLogin."' WHERE name='myfox_login';";
    $sql.="UPDATE config SET value='".$myfoxPassword."' WHERE name='myfox_password';";
    $sql.="UPDATE config SET value='".$myfoxSiteid."' WHERE name='myfox_siteid';";
    $sql.="UPDATE config SET value='".$myfoxClientid."' WHERE name='myfox_client_id';";
    $sql.="UPDATE config SET value='".$myfoxClientSecret."' WHERE name='myfox_client_secret';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);

    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
            case 'myfox_login':
                $value = $myfoxLogin;
                break;
            case 'myfox_password':
                $value = $myfoxPassword;
                break;
            case 'myfox_siteid':
                $value = $myfoxSiteid;
                break;
            case 'myfox_client_id':
                $value = $myfoxClientid;
                break;
            case 'myfox_client_secret':
                $value = $myfoxClientSecret;
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
                        MyFOX				
                        <small>Configuration MyFOX</small>
                    </h3>
                    <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <form class="form-horizontal form" method="POST" action="admin_myfox.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="adminmyfox" />
            <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Général</h3>
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="myfox_client_id">Client ID</label>
                        <div class="col-md-9">
                            <input class="form-control"name="myfox_client_id" id="myfox_client_id" type="text" value="<?php echo $myfoxClientid; ?>">
                            <span class="help-inline">Correspond à une application créée sur <a href="http://api.myfox.me">http://api.myfox.me</a></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="myfox_client_secret">Client Secret</label>
                        <div class="col-md-9">
                            <input class="form-control"name="myfox_client_secret" id="myfox_client_secret" type="text" value="<?php echo $myfoxClientSecret; ?>">
                            <span class="help-inline">Correspond à une application créée sur <a href="http://api.myfox.me">http://api.myfox.me</a></span>
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