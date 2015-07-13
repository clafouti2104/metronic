<?php
include_once "tools/config.php";
include_once "models/Alert.php";
include_once "models/Log.php";
include_once "models/Page.php";
require_once 'tools/Mobile_Detect.php';
$detect = new Mobile_Detect;

$deviceType = ($detect->isMobile()) ? 'mobile' : 'computer';
$GLOBALS["dbconnec"]=connectDB();
$pages= Page::getPageNonFilles();

//get log level 
$logLevel=10;
$theme='default';
$resultats=$GLOBALS["dbconnec"]->query("SELECT value,name FROM config WHERE name IN ('log_level','theme')");
$resultats->setFetchMode(PDO::FETCH_OBJ);
while( $resultat = $resultats->fetch() )
{
    switch(strtolower($resultat->name)){
        case 'log_level':
            $logLevel=$resultat->value;
            break;
        case 'theme':
            $theme=$resultat->value;
            break;
        default:
    }
}

//get last time logged
$lastDate=new DateTime("now");
$resultats=$GLOBALS["dbconnec"]->query("SELECT value FROM config WHERE name='last_time_logged'");
$resultats->setFetchMode(PDO::FETCH_OBJ);
while( $resultat = $resultats->fetch() )
{
    //echo 'Last Date : '.$resultat->value.'<br>';
    $lastDate=new DateTime($resultat->value);
}

//echo "DATE ==>"."SELECT * FROM log WHERE date > '".$lastDate->format('Y-m-d H:i:s')."'";
$unknownNotif=0;
$notifications=array();
//$resultatLogs=$GLOBALS["dbconnec"]->query("SELECT l.deviceId,l.date,l.value,l.level,l.rfId,d.name FROM log l, device d WHERE d.id=l.deviceId AND date > '".$lastDate->format('Y-m-d H:i:s')."' AND level >= ".$logLevel." ORDER BY date DESC");
$resultatLogs=$GLOBALS["dbconnec"]->query("SELECT l.deviceId,l.date,l.value,l.level,l.rfId,d.name FROM log l, device d WHERE d.id=l.deviceId AND level >= ".$logLevel." ORDER BY date DESC LIMIT 0,10");
$resultatLogs->setFetchMode(PDO::FETCH_OBJ);
while( $resultatLog = $resultatLogs->fetch() )
{
    $date = new DateTime($resultatLog->date);
    if($date->format('U') > $lastDate->format('U') ){
        $unknownNotif++;
    }
    $notifications[]=array(
        "deviceId"=>$resultatLog->deviceId,
        "deviceName"=>$resultatLog->name,
        "date"=>$resultatLog->date,
        "value"=>$resultatLog->value,
        "level"=>$resultatLog->level,
        "rfId"=>$resultatLog->rfId
    );
}
//print_r($notifications);
//exit;
$sqlUpdate="UPDATE config SET value=NOW() WHERE name='last_time_logged'";
//echo $sqlUpdate;
$stmt = $GLOBALS["dbconnec"]->query($sqlUpdate);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title>DomoKine</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
        <meta name="apple-touch-fullscreen" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="apple-mobile-web-app-title" content="DomoKine">
        <link rel="apple-touch-startup-image" href="assets/ios/iphone5_ios7.png">
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-summernote/summernote.css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-toastr/toastr.min.css">
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo $GLOBALS['path']; ?>/assets/global/css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['path']; ?>/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['path']; ?>/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="<?php echo $GLOBALS['path']; ?>/assets/admin/layout/css/themes/<?php echo $theme; ?>.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['path']; ?>/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
        <script src="<?php echo $GLOBALS['path']; ?>/assets/js/jquery-1.8.3.min.js"></script>	
	<!--[if lt IE 9]>
	<script src="<?php echo $GLOBALS['path']; ?>/assets/js/excanvas.js"></script>
	<script src="<?php echo $GLOBALS['path']; ?>/assets/js/respond.js"></script>	
	<![endif]-->
	<link rel="shortcut icon" href="favicon.ico" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed ">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="index.php">
			<img src="<?php echo $GLOBALS['path']; ?>/assets/img/logo.png" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<div class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse" style="padding:20px;margin-top:0px;">
		</div>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu" style="clear:none;">
			<ul class="nav navbar-nav pull-right">
					<!-- BEGIN NOTIFICATION DROPDOWN -->	
					<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                                <i class="fa fa-warning"></i>
						<i class="icon-warning-sign"></i>
                                                <?php 
                                                if( $unknownNotif > 0){
                                                ?>
                                                    <span class="badge badge-default"><?php echo $unknownNotif; ?></span>
                                                <?php
                                                }
                                                ?>
						</a>
						<ul class="dropdown-menu extended notification">
							<li>
								<p>Vous avez <?php echo count($notifications); ?> notifications</p>
							</li>
                                                        <?php 
                                                        foreach ($notifications as $notification){
                                                            $device= ($notification["deviceName"] != "") ? $notification["deviceName"] : $notification["rfId"];
                                                            $tmpInfo=$prefix="";
                                                            switch (strtolower($notification["value"])){
                                                                case "on":
                                                                    $label="success";
                                                                    $icon="bolt";
                                                                    break;
                                                                case "off":
                                                                    $label="danger";
                                                                    $icon="bolt";
                                                                    break;
                                                                case "armed":
                                                                    $prefix="MES ";
                                                                    $label="danger";
                                                                    $icon="bullhorn";
                                                                    break;
                                                                case "disarmed":
                                                                    $prefix="MHS ";
                                                                    $label="success";
                                                                    $icon="bullhorn";
                                                                    break;
                                                                case "partial":
                                                                    $prefix="MES ";
                                                                    $label="warning";
                                                                    $icon="bullhorn";
                                                                    break;
                                                                case "alert":
                                                                    $label="danger";
                                                                    $icon="icon-ban-circle";
                                                                    break;
                                                                case "double badgeage":
                                                                    $label="danger";
                                                                    $icon="user";
                                                                    break;
                                                                case "badge accepté":
                                                                    $label="success";
                                                                    $icon="user";
                                                                    break;
                                                                default:
                                                                    $label="info";
                                                                    $icon="info-circle";
                                                                    
                                                            }
                                                            $date = new DateTime($notification["date"]);
                                                            echo "<li>";
                                                            echo "<a href='javascript:;' onclick='App.onNotificationClick(1)'>";
                                                            echo "<span class='label label-".$label."'><i class='fa fa-".$icon."'></i></span>";
                                                            echo " ".$prefix.$device.". ";
                                                            echo "<span class='time'>".$date->format('d-m-Y H:i')."</span>";
                                                            echo "</a>";
                                                            echo "</li>";
                                                        } 
                                                        ?>
                                                        <li class="external">
                                                            <a href="system.php">
                                                                Toutes les notifications <i class="m-icon-swapright"></i>
                                                            </a>
                                                        </li>
						</ul>
					</li>
                                        <li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                                <i class="fa fa-tasks"></i>
                                            </a>
                                            <ul class="dropdown-menu extended tasks">
                                                <li>
                                                    <p>Opérations</p>
                                                </li>
                                                <li>
                                                    <a href="#" class="btnReboot">
                                                        <i class="fa fa-repeat "></i>
                                                        Redémarrer
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
				<!-- END TOP NAVIGATION MENU -->	
			</div>
		</div>
		<!-- END TOP NAVIGATION BAR -->
	</div>
	<div class="clearfix"></div>
<!-- END HEADER -->