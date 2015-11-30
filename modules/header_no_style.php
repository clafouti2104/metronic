<?php
include_once "tools/config.php";
include_once "models/Alert.php";
include_once "models/Log.php";
include_once "models/Page.php";
require_once 'tools/Mobile_Detect.php';
$detect = new Mobile_Detect;

$deviceType = ($detect->isMobile()) ? 'mobile' : 'computer';
$GLOBALS["dbconnec"]=connectDB();
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
    <link href="<?php echo $GLOBALS['path']; ?>/assets/global/css/font.css" rel="stylesheet" type="text/css"/>
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

<!-- END HEADER -->