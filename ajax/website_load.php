<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["websiteId"])){
    echo "error";
    return "error";
}
$output="";

$sqlPlugins = "SELECT value FROM config ";
$sqlPlugins .= " WHERE id=".$_POST["websiteId"];
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $output="$('#websiteUrl').val('".  addslashes($row["value"])."');";
}


echo $output;
?>
