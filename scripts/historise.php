<?php
/**
 * Historise les valeurs des équipements
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();

//Recherche device à historiser
$sql="SELECT * FROM device WHERE ";
$sql.=" active=1 AND collect IS NOT NULL AND collect > 0";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());

$sqlInsert="";
$devices = array();
$date=date('H')*60+date('i');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sql="SELECT * FROM `domo`.`temperature` WHERE deviceid=:deviceid AND date > NOW() - INTERVAL ".$row['collect']." MINUTE ";
//      echo $sql." - ".$row["id"];
    $stmt2 = $GLOBALS["dbconnec"]->prepare($sql);
    $stmt2->execute(array(":deviceid"=>$row["id"]));
    if($stmt2->rowCount() == 0){
        $row['state']=doubleval($row['state']);
        //Consommation: Alimente la table releve_<DEVICE_ID>
        if($row["incremental"] == 1){
            //Récupération de la dernière valeur de la table température
            $sqlGetLastValue = "SELECT value FROM temperature WHERE deviceid=:deviceid AND VALUE != 0 ORDER BY date DESC LIMIT 1";
            //echo $sqlGetLastValue."->".$row["id"];
            $stmt3 = $GLOBALS["dbconnec"]->prepare($sqlGetLastValue);
            $stmt3->execute(array(":deviceid"=>$row["id"]));
            if($stmt3->rowCount() > 0){
                //echo "STATE=";
                //print_r($row["state"]);

                $val = $stmt3->fetch(PDO::FETCH_ASSOC);
                $value=$row["state"]-$val["value"];
                //echo ">>VAL=".$val["value"];
            } else {
                $value=0;
            }
            $sqlInsert.="INSERT INTO releve_".$row["id"]." (date,value) ";
            $sqlInsert.=" VALUES (NOW(), ".$value.");";

        }
        $sqlInsert.="INSERT INTO temperature (name,date,value,deviceid) ";
        $sqlInsert.=" VALUES ('".$row['name']."', NOW(), ".$row['state'].", ".$row['id'].");";
    }
}
echo $sqlInsert;
if($sqlInsert != ""){
    $stmt = $GLOBALS["dbconnec"]->prepare($sqlInsert);
    $stmt->execute(array());
}

?>