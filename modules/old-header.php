<?php
include_once "tools/config.php";
include_once "models/Page.php";

$GLOBALS["dbconnec"]=connectDB();
$pages= Page::getPages(TRUE);

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
$notifications=array();
$resultatLogs=$GLOBALS["dbconnec"]->query("SELECT l.deviceId,l.date,l.value,l.level,l.rfId,d.name FROM log l, device d WHERE d.id=l.deviceId AND date > '".$lastDate->format('Y-m-d H:i:s')."' AND level >= ".$logLevel." ORDER BY date DESC");
$resultatLogs->setFetchMode(PDO::FETCH_OBJ);
while( $resultatLog = $resultatLogs->fetch() )
{
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
	<title>DomoGyss</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
        <meta name="apple-mobile-web-app-capable" content="yes">
	<link href="<?php echo $GLOBALS['path']; ?>/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/css/metro.css" rel="stylesheet" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/css/style.css" rel="stylesheet" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/css/style_responsive.css" rel="stylesheet" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/css/style_<?php echo $theme; ?>.css" rel="stylesheet" id="style_color" />
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['path']; ?>/assets/gritter/css/jquery.gritter.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['path']; ?>/assets/uniform/css/uniform.default.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['path']; ?>/assets/bootstrap-daterangepicker/daterangepicker.css" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/fullcalendar/fullcalendar/bootstrap-fullcalendar.css" rel="stylesheet" />
	<link href="<?php echo $GLOBALS['path']; ?>/assets/jqvmap/jqvmap/jqvmap.css" media="screen" rel="stylesheet" type="text/css" />
        <script src="<?php echo $GLOBALS['path']; ?>/assets/js/jquery-1.8.3.min.js"></script>	
	<!--[if lt IE 9]>
	<script src="<?php echo $GLOBALS['path']; ?>/assets/js/excanvas.js"></script>
	<script src="<?php echo $GLOBALS['path']; ?>/assets/js/respond.js"></script>	
	<![endif]-->
	<link rel="shortcut icon" href="favicon.ico" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
<!-- BEGIN HEADER -->
	<div class="header navbar navbar-inverse navbar-fixed-top">
		<!-- BEGIN TOP NAVIGATION BAR -->
		<div class="navbar-inner">
			<div class="container-fluid">
				<!-- BEGIN LOGO -->
				<a class="brand" href="index.php">
				<img src="<?php echo $GLOBALS['path']; ?>/assets/img/logo.png" alt="logo" />
				</a>
				<!-- END LOGO -->
				<!-- BEGIN RESPONSIVE MENU TOGGLER -->
				<a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
				<img src="<?php echo $GLOBALS['path']; ?>/assets/img/menu-toggler.png" alt="" />
				</a>          
				<!-- END RESPONSIVE MENU TOGGLER -->				
				<!-- BEGIN TOP NAVIGATION MENU -->					
				<ul class="nav pull-right">
					<!-- BEGIN NOTIFICATION DROPDOWN -->	
					<li class="dropdown" id="header_notification_bar">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-warning-sign"></i>
                                                <?php 
                                                if( count($notifications) > 0){
                                                ?>
                                                    <span class="badge"><?php echo count($notifications); ?></span>
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
                                                            $tmpInfo="";
                                                            switch (strtolower($notification["value"])){
                                                                case "on":
                                                                    $label="success";
                                                                    $icon="bolt";
                                                                    break;
                                                                case "off":
                                                                    $label="important";
                                                                    $icon="bolt";
                                                                    break;
                                                                case "armed":
                                                                    $label="important";
                                                                    $icon="bullhorn";
                                                                    break;
                                                                case "disarmed":
                                                                    $label="success";
                                                                    $icon="bullhorn";
                                                                    break;
                                                                case "partial":
                                                                    $label="warning";
                                                                    $icon="bullhorn";
                                                                    break;
                                                                case "alert":
                                                                    $label="important";
                                                                    $icon="icon-ban-circle";
                                                                    $tmpInfo="Perte Communication: ";
                                                                    break;
                                                                default:
                                                                    $label="info";
                                                                    $icon="info-sign";
                                                                    
                                                            }
                                                            $date = new DateTime($notification["date"]);
                                                            echo "<li>";
                                                            echo "<a href='javascript:;' onclick='App.onNotificationClick(1)'>";
                                                            echo "<span class='label label-".$label."'><i class='icon-".$icon."'></i></span>";
                                                            echo $device.". ";
                                                            echo "<span class='time'>".$date->format('d-m-Y H:i')."</span>";
                                                            echo "</a>";
                                                            echo "</li>";
                                                        } 
                                                        ?>
							<!--<li>
								<a href="javascript:;" onclick="App.onNotificationClick(1)">
								<span class="label label-success"><i class="icon-plus"></i></span>
								New user registered. 
								<span class="time">Just now</span>
								</a>
							</li>
							<li>
								<a href="#">
								<span class="label label-important"><i class="icon-bolt"></i></span>
								Server #12 overloaded. 
								<span class="time">15 mins</span>
								</a>
							</li>
							<li>
								<a href="#">
								<span class="label label-warning"><i class="icon-bell"></i></span>
								Server #2 not respoding.
								<span class="time">22 mins</span>
								</a>
							</li>
							<li>
								<a href="#">
								<span class="label label-info"><i class="icon-bullhorn"></i></span>
								Application error.
								<span class="time">40 mins</span>
								</a>
							</li>
							<li>
								<a href="#">
								<span class="label label-important"><i class="icon-bolt"></i></span>
								Database overloaded 68%. 
								<span class="time">2 hrs</span>
								</a>
							</li>
							<li>
								<a href="#">
								<span class="label label-important"><i class="icon-bolt"></i></span>
								2 user IP blocked.
								<span class="time">5 hrs</span>
								</a>
							</li>
							<li class="external">
								<a href="#">See all notifications <i class="m-icon-swapright"></i></a>
							</li>-->
						</ul>
					</li>
                                    </ul>
				<!-- END TOP NAVIGATION MENU -->	
			</div>
		</div>
		<!-- END TOP NAVIGATION BAR -->
	</div>
<!-- END HEADER -->