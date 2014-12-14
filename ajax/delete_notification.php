<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["notificationId"])){
    echo "error";
    return "error";
}

$sql="DELETE FROM config WHERE name='pushing_box' AND value='".$_POST["notificationId"]."'";
$stmt = $GLOBALS["dbconnec"]->exec($sql);
echo "success";

?>
