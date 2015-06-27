<?php
/**
 * Historise les valeurs des équipements
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();

//Recherche device à historiser
$sql="SELECT * FROM device WHERE ";
$sql.=" active=1 AND collect IS NOT NULL AND collect > 0";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());

$sqlInsert="";
$devices = array();
$date=date('H')*60+date('i');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sql =  "SELECT * FROM `histo`.`temperature_".$row['id']."` ";
    $sql .= " WHERE deviceid=:deviceid AND date > NOW() - INTERVAL ".$row['collect']." MINUTE ";
    $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
    $stmt2->execute(array(":deviceid"=>$row["id"]));
    
    //Pas de données historisé dans la période
    if($stmt2->rowCount() == 0){
        //Modification Status
        $row['state']=($row['state'] == "on") ? 1 : $row['state'];
        $row['state']=($row['state'] == "off") ? 0 : $row['state'];
        $row['state']=doubleval($row['state']);
        
        //Consommation: Alimente la table releve_<DEVICE_ID>
        if($row["incremental"] == 1){
            //Récupération de la dernière valeur de la table température
            $sqlGetLastValue = "SELECT value FROM temperature_".$row['id']." ";
            $sqlGetLastValue .= " WHERE deviceid=:deviceid AND value != 0 AND value IS NOT NULL ORDER BY date DESC LIMIT 1";
            $stmt3 = $GLOBALS["histoconnec"]->prepare($sqlGetLastValue);
            $stmt3->execute(array(":deviceid"=>$row["id"]));
            $value=0;
            if($stmt3->rowCount() > 0){
                $val = $stmt3->fetch(PDO::FETCH_ASSOC);
                $value=$row["state"]-$val["value"];
                //echo ">>VAL=".$val["value"];
                if($value >= 0){
                    //$sqlInsert.="INSERT INTO releve_".$row["id"]." (date,value) ";
                    //$sqlInsert.=" VALUES (NOW(), ".$value.");";
                    $sqlInsert.="INSERT INTO releve_".$row["id"]." (date,value) ";
                    $sqlInsert.=" VALUES (NOW(), ".$value.");";
                }
            }
            
            $row['state']=($row['state'] == 0) ? 0 : $row['state'];
        }
        //$sqlInsert.="INSERT INTO temperature (name,date,value,deviceid) ";
        //$sqlInsert.=" VALUES ('".$row['name']."', NOW(), ".$row['state'].", ".$row['id'].");";
        $sqlInsert.="INSERT INTO temperature_".$row['id']." (name,date,value,deviceid) ";
        $sqlInsert.=" VALUES ('".$row['name']."', NOW(), ".$row['state'].", ".$row['id'].");";
    }
}
echo $sqlInsert;
if($sqlInsert != ""){
    $stmt = $GLOBALS["histoconnec"]->prepare($sqlInsert);
    $stmt->execute(array());
}

?>