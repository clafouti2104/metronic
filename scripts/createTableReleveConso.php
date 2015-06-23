<?php
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
include "../models/Device.php";

//Récupération des devices incrementaux
$sqlTable="";
$sqlRecall="SELECT id, incremental FROM device WHERE incremental IS NOT NULL AND incremental > 0";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlRecall);
$stmt->execute(array());
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sqlTable .= "CREATE TABLE IF NOT EXISTS releve_consolidation_d".$row["id"]." (
  `id` mediumint(9) NOT NULL,
  `value` float DEFAULT NULL,
  `date` datetime DEFAULT NULL,
   PRIMARY KEY (`id`)  
        )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
         $sqlTable .= "ALTER TABLE `releve_consolidation_d".$row["id"]."` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;";
}

if($sqlTable!=""){
    $stmt = $GLOBALS["dbconnec"]->exec($sqlTable);
}

?>
