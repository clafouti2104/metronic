<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminzwave"){
    $isPost=TRUE;
}

$zwaveIpAddressBDD=$zwaveLoginBDD=$zwavePasswordBDD="";

$sql = "SELECT * FROM config WHERE name IN ('zwave_ip_address', 'zwave_login', 'zwave_password')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'zwave_ip_address':
            $zwaveIpAddressBDD = $row["value"];
            break;
        case 'zwave_login':
            $zwaveLoginBDD = $row["value"];
            break;
        case 'zwave_password':
            $zwavePasswordBDD = $row["value"];
            break;
        default:
    }
}

$zwaveIpAddress= ($isPost) ? $_POST["zwave_ip_address"] : $zwaveIpAddressBDD;
$zwaveLogin= ($isPost) ? $_POST["zwave_login"] : $zwaveLoginBDD;
$zwavePassword= ($isPost) ? $_POST["zwave_password"] : $zwavePasswordBDD;

if($isPost){
    $sql="UPDATE config SET value='".$zwaveIpAddress."' WHERE name='zwave_ip_address';";
    $sql.="UPDATE config SET value='".$zwaveLogin."' WHERE name='zwave_login';";
    $sql.="UPDATE config SET value='".$zwavePassword."' WHERE name='zwave_password';";

    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
            case 'zwave_ip_address':
                $value = $zwaveIpAddress;
                break;
            case 'zwave_login':
                $value = $zwaveLogin;
                break;
            case 'zwave_password':
                $value = $zwavePassword;
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
                        ZWave				
                        <small>Configuration de la passerelle Razberry ZWay</small>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb" style="margin-bottom:0px;">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="index.php">Admin</a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="admin_advanced">Avancés</a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>   
                            <a href="#">ZWave</a>
                        </li>
                    </ul>
                    <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <form class="form-horizontal form" method="POST" action="admin_zwave.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="adminzwave" />
            <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Général</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="zwave_ip_address">Adresse IP Razberry</label>
                        <div class="col-md-9">
                            <input class="form-control"name="zwave_ip_address" id="zwave_ip_address" type="text" value="<?php echo $zwaveIpAddress; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="zwave_login">Login</label>
                        <div class="col-md-9">
                            <input class="form-control"name="zwave_login" id="zwave_login" type="text" value="<?php echo $zwaveLogin; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="zwave_password">Mot de passe</label>
                        <div class="col-md-9">
                            <input class="form-control"name="zwave_password" id="zwave_password" type="password" value="<?php echo $zwavePassword; ?>">
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