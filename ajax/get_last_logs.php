<?php
/**
 * Récupère les logs ou lignes issues de la table température
 */
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Device.php";
$output="";

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}
if(!isset($_POST["type"])){
    echo "error";
    return "error";
}

$device = Device::getDevice($_POST["deviceId"]);
if($_POST["type"] == "consommation"){
    $logs = $device->getConsommation();
    $table="Consommation";
} else {
    $logs = $device->getHistorique();
    $table="Historique";
}
$output .= "$('#table".$table."').empty();";
if(count($logs) > 0){
    $output .= "$('#table".$table."').append('<tr><th>Date</th><th>Etat</th></tr>";
    foreach($logs as $log){
        $output .= "<tr>";
        $output .= "<td>".$log["date"]."</td>";
        $output .= "<td>".$log["value"]."</td>";
        $output .= "</tr>";
    }
    $output .= "');";
} else {
    $output .= "$('#table".$table."').append('<tr class=\"info\"><td>Aucune donnée disponible</td></tr>');";
}
echo $output;
?>
