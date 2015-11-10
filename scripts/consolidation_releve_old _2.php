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
$sql.=" active=1 AND id=31";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());

$dateDebut="2015-06-01";
$dateFin="2015-06-30";
$sqlInsert=$sqlUpdate="";
$devices = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sql="SELECT value ";
    $sql .= ", date ";
    $sql .= " FROM `histo`.`releve_consolidation_d".$row["id"]."` ";
    $sql.=" WHERE date BETWEEN '".$dateDebut."' AND '".$dateFin."' ";
    $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
    $stmt2->execute( array() );
    
    while ($rowHisto = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $sqlUpdate .= "UPDATE `histo`.`temperature_consolidation` ";
        $sqlUpdate .= " SET avg=".$rowHisto['value']." ";
        $sqlUpdate .= " WHERE deviceid=".$row["id"]." ";
        $sqlUpdate .= " AND date=".$rowHisto["date"].";";
    }
}

$sqlGlobal=$sqlInsert.$sqlUpdate;
if($sqlGlobal != ""){
    $stmt = $GLOBALS["histoconnec"]->prepare($sqlGlobal);
    $stmt->execute(array());
}

?>