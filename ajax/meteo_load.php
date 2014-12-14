<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["meteoId"])){
    echo "error";
    return "error";
}
$output="";

$sqlPlugins = "SELECT value FROM config ";
$sqlPlugins .= " WHERE id=".$_POST["meteoId"];
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $city=explode(",",$row["value"]);
    $output="$('#meteoVille').val('".  addslashes($city[0])."');";
    $output.="$('#meteoPays').val('".  addslashes($city[1])."');";
}


echo $output;
?>
