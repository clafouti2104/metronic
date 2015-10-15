<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="admincalaos"){
    $isPost=TRUE;
}

$calaosIpAddressBDD=$calaosLoginBDD=$calaosPasswordBDD="";

$sql = "SELECT * FROM config WHERE name IN ('calaos_ip_address', 'calaos_login', 'calaos_password')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'calaos_ip_address':
            $calaosIpAddressBDD = $row["value"];
            break;
        case 'calaos_login':
            $calaosLoginBDD = $row["value"];
            break;
        case 'calaos_password':
            $calaosPasswordBDD = $row["value"];
            break;
        default:
    }
}

$calaosIpAddress= ($isPost) ? $_POST["calaos_ip_address"] : $calaosIpAddressBDD;
$calaosLogin= ($isPost) ? $_POST["calaos_login"] : $calaosLoginBDD;
$calaosPassword= ($isPost) ? $_POST["calaos_password"] : $calaosPasswordBDD;

if($isPost){
    $sql="UPDATE config SET value='".$calaosIpAddress."' WHERE name='calaos_ip_address';";
    $sql.="UPDATE config SET value='".$calaosLogin."' WHERE name='calaos_login';";
    $sql.="UPDATE config SET value='".$calaosPassword."' WHERE name='calaos_password';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
            case 'calaos_ip_address':
                $value = $calaosIpAddress;
                break;
            case 'calaos_login':
                $value = $calaosLogin;
                break;
            case 'calaos_password':
                $value = $calaosPassword;
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
                        Calaos				
                        <small>Configuration Calaos</small>
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
                            <a href="#">Calaos</a>
                        </li>
                    </ul>
                    <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <form class="form-horizontal form" method="POST" action="admin_calaos.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="admincalaos" />
            <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Général</h3>
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