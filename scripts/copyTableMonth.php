<?php
/*
 * 
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();
include "../models/Device.php";

$year='2015';
$month='06';
$deviceid='1';

$dateFirst = new DateTime($year.'-'.$month.'-01');

//Récupération de la durée de rappel d'alerte en jours
$sqlTable="";
$sqlRecall="SELECT * FROM temperature WHERE deviceid = '".$deviceid."' AND date BETWEEN '".$dateFirst->format('Y-m-d')." 00:00:00' AND '".$dateFirst->format('Y-m-t')." 23:59:59'";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlRecall);
$stmt->execute(array());
$i=1;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($i==1){
        $sqlCreateTable = "CREATE TABLE IF NOT EXISTS temperature_".$deviceid." (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `value` double NOT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `calaosid` int(11) DEFAULT NULL) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        $stmt2 = $GLOBALS["dbconnec"]->exec($sqlCreateTable);
    }
    $sqlTable .= "INSERT INTO temperature_".$deviceid." ";
    $sqlTable .= " VALUES (";
    $sqlTable .= "'".$row['name']."' ";
    $sqlTable .= ",'".$row['date']."' ";
    $sqlTable .= ",'".$row['value']."' ";
    $sqlTable .= ",'".$row['deviceid']."' ";
    $sqlTable .= ",'".$row['calaosid']."' ";
    $sqlTable .= " );";
    $i++;
}

if($sqlTable!=""){
    echo $sqlTable;
    $stmt = $GLOBALS["dbconnec"]->exec($sqlTable);
}

?>
