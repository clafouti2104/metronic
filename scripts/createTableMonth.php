<?php
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
include "../models/Device.php";

//Récupération de la durée de rappel d'alerte en jours
$sqlTable="";
$sqlRecall="SELECT id, collect, incremental FROM device WHERE collect IS NOT NULL OR incremental IS NOT NULL";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlRecall);
$stmt->execute(array());
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if(!is_null($row['collect']) && $row['collect'] != ""){
        $sqlTable .= "CREATE TABLE IF NOT EXISTS temperature_".$row["id"]."(
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `value` double NOT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `calaosid` int(11) DEFAULT NULL) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
    }
    if(!is_null($row['incremental']) && $row['incremental'] != ""){
        $sqlTable .= "CREATE TABLE IF NOT EXISTS releve_".$row["id"]." (
  `id` mediumint(9) NOT NULL,
  `value` float DEFAULT NULL,
  `date` datetime DEFAULT NULL    
        )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
    }
}

if($sqlTable!=""){
    $stmt = $GLOBALS["dbconnec"]->exec($sqlTable);
}

?>
