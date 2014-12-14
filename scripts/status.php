<?php
/*
 * Renvoie les status à jours à partir des devicesId fournis
 */
include("../tools/config.php");
include("../models/Device.php");

$GLOBALS["dbconnec"] = connectDB();


if(!isset($_POST["ids"])){
   return null; 
}

$ids = explode(',',$_POST["ids"]);
$sqlDevice = '';
foreach($ids as $id){
    $sqlDevice .= ($sqlDevice == '') ? '' : ',';
    $sqlDevice .= $id;
}

//Requete de récupération des status
$query = "SELECT id,state,data_type,unite ";
$query .= " FROM device ";
$query .= " WHERE id IN (".$sqlDevice.")";
$stmt = $GLOBALS["dbconnec"]->prepare($query);
$stmt->execute(array());

$result = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $result[$row["id"]] = Device::showStateGeneric($row["state"], $row["data_type"], $row["unite"]);
    //echo Device::showStateGeneric($row["state"], $row["data_type"], $row["unite"]);
}
echo json_encode($result);
?>
