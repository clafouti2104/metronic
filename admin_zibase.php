<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminzibase"){
    $isPost=TRUE;
}

$zibaseLoginBDD=$zibasePasswordBDD="";

$sql = "SELECT * FROM config WHERE name IN ('zibase_login', 'zibase_password')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'zibase_login':
            $zibaseLoginBDD = $row["value"];
            break;
        case 'zibase_password':
            $zibasePasswordBDD = $row["value"];
            break;
        default:
    }
}

$zibaseLogin= ($isPost) ? $_POST["zibase_login"] : $zibaseLoginBDD;
$zibasePassword= ($isPost) ? $_POST["zibase_password"] : $zibasePasswordBDD;

if($isPost){
    $sql="UPDATE config SET value='".$zibaseLogin."' WHERE name='zibase_login';";
    $sql.="UPDATE config SET value='".$zibasePassword."' WHERE name='zibase_password';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
            case 'zibase_login':
                $value = $zibaseLogin;
                break;
            case 'zibase_password':
                $value = $zibasePassword;
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
                        Zibase				
                        <small>Configuration Zibase</small>
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
                            <a href="#">Zibase</a>
                        </li>
                    </ul>
                    <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <form class="form-horizontal form" method="POST" action="admin_zibase.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="adminzibase" />
            <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Général</h3>
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