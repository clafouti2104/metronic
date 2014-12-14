<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["notificationId"])){
    echo "error";
    return "error";
}

exec('curl "http://api.pushingbox.com/pushingbox?devid='.$_POST["notificationId"].'"');

echo "success";
?>
