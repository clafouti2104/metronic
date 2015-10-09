<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["cameraId"])){
    echo "error";
    return "error";
}

$sql="DELETE FROM pageitem WHERE params LIKE '%\"id\":\"".$_POST["cameraId"]."\"%';";
$sql="DELETE FROM config WHERE id='".$_POST["cameraId"]."';";
$stmt = $GLOBALS["dbconnec"]->exec($sql);
echo "success";

?>
