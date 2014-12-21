<?php
/**
 * Récupère les alertes
 */
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Alert.php";
$output="";

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}

$alerts = Alert::getAlertsByDevice($_POST["deviceId"]);
$output .= "$('#tableAlert').empty();";
if(count($alerts) > 0){
    $output .= "$('#tableAlert').append('<tr><th>Date</th><th>Etat</th></tr>";
    foreach($alerts as $alert){
        $output .= "<tr>";
        $output .= "<td>".$alert["deviceId"]."</td>";
        $output .= "<td>".$alert["notificationId"]."</td>";
        $output .= "</tr>";
    }
    $output .= "');";
} else {
    $output .= "$('#tableAlert').append('<tr class=\"info\"><td>Aucune donnée disponible</td></tr>');";
}
echo $output;
?>
