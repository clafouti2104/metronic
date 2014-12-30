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
    $output .= "$('#tableAlert').append('<tr><th>Opérateur</th><th>Valeur</th><th>Pushing Box</th><th>Action</th></tr>";
    foreach($alerts as $alert){
        $output .= "<tr id=\"line-alert-".$alert->id."\">";
        $output .= "<td>".$alert->operator."</td>";
        $output .= "<td>".$alert->value."</td>";
        $output .= "<td>".$alert->notificationId."</td>";
        $output .= "<td>";
        $output .= "<a href=\"edit_device.php#editAlert\" data-toggle=\"modal\" ><i class=\"fa fa-edit btnEditAlert\" title=\"Editer\" onclick=\"editAlert(".$alert->id.");\" idAlert=\"".$alert->id."\" style=\"cursor:pointer;color:black;\"></i></a>&nbsp;&nbsp;";
        $output .= "<a href=\"edit_device.php#deleteAlert\" data-toggle=\"modal\" onclick=\"document.getElementById(\'idalert\').value=\'".$alert->id."\';\" ><i class=\"fa fa-trash-o btnDeleteMessage\" title=\"Supprimer\" idAlert=\"".$alert->id."\" style=\"cursor:pointer;color:black;\"></i></a></td>";
        $output .= "</td>";
        $output .= "</tr>";
    }
    $output .= "');";
} else {
    $output .= "$('#tableAlert').append('<tr class=\"info\"><td>Aucune donnée disponible</td></tr>');";
}
echo $output;
?>
