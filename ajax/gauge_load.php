<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["gaugeId"])){
    echo "error";
    return "error";
}
$output="";

$sqlPlugins = "SELECT value,comment FROM config ";
$sqlPlugins .= " WHERE id=".$_POST["gaugeId"];
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $params=json_decode($row["comment"]);
    $minimum = (isset($params->minimum)) ? $params->minimum : 0;
    $maximum = (isset($params->maximum)) ? $params->maximum : 100;
    
    $output="$('#gaugeDevice').val('".  $row["value"]."');";
    $output.="$('#gaugeMinimum').val('".  $minimum."');";
    $output.="$('#gaugeMaximum').val('".  $maximum."');";
}


echo $output;
?>
