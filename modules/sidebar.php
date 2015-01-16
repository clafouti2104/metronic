<?php 
$pageAdmin=array(
    $GLOBALS['path']."/admin_device.php",
    $GLOBALS['path']."/admin_parameters.php",
    $GLOBALS['path']."/admin_advanced.php",
    $GLOBALS['path']."/admin_page.php",
    $GLOBALS['path']."/admin_scenario.php",
    $GLOBALS['path']."/admin_tuile.php",
    $GLOBALS['path']."/admin_logs.php",
    $GLOBALS['path']."/admin_chart.php",
    $GLOBALS['path']."/admin_plugins.php",
    $GLOBALS['path']."/edit_chart.php",
    $GLOBALS['path']."/edit_device.php",
    $GLOBALS['path']."/edit_scenario.php",
    $GLOBALS['path']."/edit_tuile.php",
    $GLOBALS['path']."/edit_liste.php",
    $GLOBALS['path']."/edit_page.php"
);
?>
<!-- BEGIN CONTAINER -->
	<div class="page-container">
		<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
				</li>
				<li class="start <?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/index.php") echo "active"; if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/") echo "active";  ?> ">
					<a href="<?php echo $GLOBALS['path']; ?>/index.php">
					<i class="fa fa-home"></i> 
					<span class="title">Accueil</span>
					<span class="selected"></span>
					</a>
				</li>
				<li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/charts.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/charts.php">
					<i class="fa fa-bar-chart-o"></i> 
					<span class="title">Températures</span>
					</a>
				</li>
				                <?php
                                foreach($pages as $pageTmp){
                                    $class=($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/page.php?pageId=".$pageTmp->id) ? "active" : "";
                                    $link = ($pageTmp->hasFilles()) ? "_parent" : "";
                                    
                                    echo "
                                    <li class=\"".$class."\">
                                            <a href=\"".$GLOBALS['path']."/page".$link.".php?pageId=".$pageTmp->id."\">
                                            <i class=\"fa ".$pageTmp->icon."\"></i> 
                                            <span class=\"title\">".$pageTmp->name."</span>
                                            </a>
                                    </li>";
                                    
                                }
                                ?>
                <!--<li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/scenario.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/scenario.php">
					<i class="fa fa-align-justify "></i> 
					<span class="title">Scénario</span>
					</a>
				</li>
                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/camera.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/camera.php">
					<i class="fa fa-eye "></i> 
					<span class="title">Surveillance</span>
					</a>
				</li>
                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/salon.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/salon.php">
					<i class="fa fa-picture-o "></i> 
					<span class="title">Salon</span>
					</a>
				</li>-->
                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/alarm.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/alarm.php">
					<i class="fa fa-bullhorn "></i> 
					<span class="title">Alarme</span>
					</a>
				</li>
                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/conso.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/conso.php">
                                            <i class="fa fa-tachometer"></i> 
                                            <span class="title">Consommations</span>
					</a>
				</li>
                <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/system.php") echo "active"; ?>">
					<a href="<?php echo $GLOBALS['path']; ?>/system.php">
                                            <i class="fa fa-wrench"></i> 
                                            <span class="title">Système</span>
					</a>
				</li>
                <li class="<?php if(in_array($_SERVER["REQUEST_URI"], $pageAdmin)) echo "active"; ?> has-sub">
					<a href="javascript:;">
                                            <i class="fa fa-list"></i> 
                                            <span class="selected"></span>
                                            <span class="title">Administration</span>
                                            <span class="arrow"></span>
					</a>
                                        <ul class="sub-menu">
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_device.php") echo "active"; ?>">
                                                <a href="admin_device.php">Device</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_parameters.php") echo "active"; ?>">
                                                <a href="admin_parameters.php">Paramètres</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_advanced.php") echo "active"; ?>">
                                                <a href="admin_advanced.php">Avancés</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_page.php") echo "active"; ?>">
                                                <a href="admin_page.php">Page</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_scenario.php") echo "active"; ?>">
                                                <a href="admin_scenario.php">Scénario</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_liste.php") echo "active"; ?>">
                                                <a href="admin_liste.php">Liste</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_tuile.php") echo "active"; ?>">
                                                <a href="admin_tuile.php">Tuile</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_chart.php") echo "active"; ?>">
                                                <a href="admin_chart.php">Graphique</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_plugins.php") echo "active"; ?>">
                                                <a href="admin_plugins.php">Plugins</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_maintenance.php") echo "active"; ?>">
                                                <a href="admin_maintenance.php">Maintenance</a>
                                            </li>
                                            <li class="<?php if($_SERVER["REQUEST_URI"] == $GLOBALS['path']."/admin_logs.php") echo "active"; ?>">
                                                <a href="admin_logs.php">Logs</a>
                                            </li>
                                        </ul>
				</li>
			</ul>
		</div>
	</div>
