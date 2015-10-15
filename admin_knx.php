<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminknx"){
    $isPost=TRUE;
}

$knxIpAddressBDD="";

$sql = "SELECT * FROM config WHERE name IN ('knx_ip_address')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'knx_ip_address':
            $knxIpAddressBDD = $row["value"];
            break;
        default:
    }
}

$knxIpAddress= ($isPost) ? $_POST["knx_ip_address"] : $knxIpAddressBDD;

if($isPost){
    $sql="UPDATE config SET value='".$knxIpAddress."' WHERE name='knx_ip_address';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);

    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content="[parameters]\n";
    foreach($ini as $title => $value){
        switch($title){
            case 'knx_ip_address':
                $value = $knxIpAddress;
                break;
            default:
        }
        $content .="\n\t".$title."=\"".$value."\"";
        
    }
    file_put_contents("/var/www/metronic/tools/parameters.ini", $content);
    file_put_contents("/etc/domokine/eibnetmux", "#Do not edit\n\nIP_KNX_ROUTER=\"".$knxIpAddress."\"");
    
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
                        KNX				
                        <small>Configuration de la partie KNX</small>
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
                            <a href="#">KNX</a>
                        </li>
                    </ul>
                    <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <form class="form-horizontal form" method="POST" action="admin_knx.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="adminknx" />
            <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Routeur IP/KNX</h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="knx_ip_address">Adresse IP</label>
                        <div class="col-md-9">
                            <input class="form-control" name="knx_ip_address" id="knx_ip_address" type="text" value="<?php echo $knxIpAddress; ?>">
                            <span class="help-inline">Adresse IP du routeur KNX</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-3">Service</label>
                        <div class="col-md-9">
                            <button class="btn btn-primary eibnetmuxStart" type="button">Start</button>
                            <button class="btn default eibnetmuxStop" type="button">Stop</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">
                    <i class="icon-ok"></i>Valider
                </button>
                <a href="admin_advanced.php"><button class="btn" type="button">Retourner</button></a>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function() { 
    $('.eibnetmuxStart').bind('click',function(e){
        $.ajax({
            url: "ajax/eibnetmux_handle.php",
            type: "POST",
            data: {
                action: 'start'
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                toastr.info("KNX activé");
            }
        });
    });

    $('.eibnetmuxStop').bind('click',function(e){
        $.ajax({
            url: "ajax/eibnetmux_handle.php",
            type: "POST",
            data: {
                action: 'stop'
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                toastr.info("KNX activé");
            }
        });
    });
});
</script>

<?php
include "modules/footer.php";
?>