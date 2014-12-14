<?php 
$pageAdmin=array(
    $GLOBALS['path']."/admin_device.php",
    $GLOBALS['path']."/admin_parameters.php",
    $GLOBALS['path']."/admin_page.php",
    $GLOBALS['path']."/admin_scenario.php",
    $GLOBALS['path']."/admin_tuile.php",
    $GLOBALS['path']."/admin_logs.php"
);
?>
<!-- BEGIN CONTAINER -->
	<div class="page-container row-fluid">
		<!-- BEGIN SIDEBAR -->
		<div class="page-sidebar nav-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->        	
			<ul>
				<li>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler hidden-phone"></div>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
				</li>
				<li class="start <?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/index.php") echo "active"; if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/") echo "active";  ?> ">
					<a href="<?php echo $GLOBALS['path']; ?>/index.php">
					<i class="icon-home"></i> 
					<span class="title">Accueil</span>
					<span class="selected"></span>
					</a>
				</li>
                                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/charts.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/charts.php">
					<i class="icon-bar-chart"></i> 
					<span class="title">Températures</span>
					</a>
				</li>
                                <?php
                                foreach($pages as $pageTmp){
                                    $class=($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/charts.php") ? "active" : "";
                                    echo "
                                    <li class=\"".$class."\">
                                            <a href=\"".$GLOBALS['path']."/page.php?pageId=".$pageTmp->id."\">
                                            <i class=\"icon-bar-chart\"></i> 
                                            <span class=\"title\">".$pageTmp->name."</span>
                                            </a>
                                    </li>";
                                    
                                }
                                ?>
                                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/scenario.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/scenario.php">
					<i class="icon-reorder"></i> 
					<span class="title">Scénario</span>
					</a>
				</li>
                                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/camera.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/camera.php">
					<i class="icon-eye-open"></i> 
					<span class="title">Surveillance</span>
					</a>
				</li>
                                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/salon.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/salon.php">
					<i class="icon-picture"></i> 
					<span class="title">Salon</span>
					</a>
				</li>
                                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/alarm.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/alarm.php">
					<i class="icon-bullhorn"></i> 
					<span class="title">Alarme</span>
					</a>
				</li>
                                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/system.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/system.php">
                                            <i class="icon-wrench"></i> 
                                            <span class="title">Système</span>
					</a>
				</li>
                                <li class="<?php if(in_array($_SERVER["REQUEST_URI"], $pageAdmin)) echo "active"; ?> has-sub">
					<a href="javascript:;">
                                            <i class="icon-th-list"></i> 
                                            <span class="selected"></span>
                                            <span class="arrow"></span>
                                            <span class="title">Administration</span>
					</a>
                                        <ul class="sub">
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_device.php") echo "active"; ?>">
                                                <a href="admin_device.php">Device</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_parameters.php") echo "active"; ?>">
                                                <a href="admin_parameters.php">Paramètres</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_page.php") echo "active"; ?>">
                                                <a href="admin_page.php">Page</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_scenario.php") echo "active"; ?>">
                                                <a href="admin_scenario.php">Scénario</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_tuile.php") echo "active"; ?>">
                                                <a href="admin_tuile.php">Tuile</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_logs.php") echo "active"; ?>">
                                                <a href="admin_logs.php">Logs</a>
                                            </li>
                                        </ul>
				</li>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
                <!-- END SIDEBAR -->