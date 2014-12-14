<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["cameraId"])){
    echo "error";
    return "error";
}
$output="";

$sqlPlugins = "SELECT value,comment FROM config ";
$sqlPlugins .= " WHERE id=".$_POST["cameraId"];
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $camera=json_decode($row["comment"]);
    $output="$('#cameraName').val('".  addslashes($row["value"])."');";
    $output.="$('#cameraIp').val('".  addslashes($camera->ip)."');";
    $output.="$('#cameraStream').val('".  addslashes($camera->cameraStream)."');";
    $output.="$('#cameraStreamImage').val('".  addslashes($camera->cameraStreamImage)."');";
}

echo $output;
?>
