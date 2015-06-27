<?php
/**
 * 
 * Alimente la table temperature_consolidation
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();

//Recherche device à historiser
$sql="SELECT * FROM device WHERE ";
$sql.=" active=1 AND collect IS NOT NULL AND collect > 0";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());

$sqlInsert=$sqlUpdate="";
$devices = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sql="SELECT * FROM `histo`.`temperature_".$row["id"]."` ";
    $sql.=" WHERE date > '".date('Y-m-d')." 00:00:00' AND date < '".date('Y-m-d')." 23:59:59'";
    $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
    $stmt2->execute( array() );
    
    $values=array();
    while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $date = new DateTime($row2["date"]);
        $values[$date->format('H:i')]=$row2["value"];
    }
    $sql="SELECT * FROM `histo`.`temperature_consolidation` ";
    $sql.=" WHERE date = '".date('Y-m-d')."' AND deviceid=".$row["id"];
    $stmt3 = $GLOBALS["histoconnec"]->prepare($sql);
    $stmt3->execute( array() );
    //Ligne Existante --> Update
    if($stmt3->rowCount() > 0){
        $sqlUpdate .= "UPDATE `histo`.`temperature_consolidation` ";
        $sqlUpdate .= " SET value='".  json_encode($values)."' ";
        $sqlUpdate .= " WHERE date = '".date('Y-m-d')."' AND deviceid=".$row["id"].";";
    } else { //Ligne n'existe pas --> Insert
        $sqlInsert .= "INSERT INTO `histo`.`temperature_consolidation` ";
        $sqlInsert .= " (deviceid, value, date) VALUES (";
        $sqlInsert .= " ".$row["id"].",'', '".date('Y-m-d')."'";
        $sqlInsert .= " );";
    }
}

$sqlGlobal=$sqlInsert.$sqlUpdate;
if($sqlGlobal != ""){
    $stmt = $GLOBALS["histoconnec"]->prepare($sqlGlobal);
    $stmt->execute(array());
}

?>