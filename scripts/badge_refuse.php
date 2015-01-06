<?php
/*
 * Script exécuté lors du badgeage
 */

include("../tools/config.php");
include_once "../models/Log.php";

$GLOBALS["dbconnec"] = connectDB();

$badge=(isset($_GET["uid"])) ? $_GET["uid"] : "";

$date = new DateTime('now');
Log::createLog("badge_refuse", $badge, $date, NULL, 80);
?>
