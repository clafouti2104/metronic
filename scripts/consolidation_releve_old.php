<?php
/**
 * 
 * Alimente la table releve_consommation_d<DEVICE_ID> avec les anciennes données
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();

//Recherche device à historiser
$sql="SELECT * FROM device WHERE ";
$sql.=" active=1 AND incremental IS NOT NULL AND incremental > 0";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());

$sqlInsert=$sqlUpdate="";
$devices = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sql="SELECT SUM(value) as value ";
    $sql .= ", YEAR(date) as year, MONTH(date) as month, DAY(date) as day ";
    $sql .= " FROM `histo`.`releve_".$row["id"]."` ";
    $sql.=" GROUP BY YEAR(date), MONTH(date), DAY(date)";
    $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
    $stmt2->execute( array() );
    
    //echo $sql;
    while ($rowHisto = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $sqlInsert .= "INSERT INTO `histo`.`releve_consolidation_d".$row["id"]."` ";
        $sqlInsert .= " (value, date) VALUES (";
        $sqlInsert .= " ".$rowHisto['value'].", '".$rowHisto['year']."-".$rowHisto['month']."-".$rowHisto['day']."'";
        $sqlInsert .= " );";
    }
}

$sqlGlobal=$sqlInsert.$sqlUpdate;
if($sqlGlobal != ""){
    $stmt = $GLOBALS["histoconnec"]->prepare($sqlGlobal);
    $stmt->execute(array());
}

?>