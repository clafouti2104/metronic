<?php
/**
 * 
 * Alimente la table releve_consommation_d<DEVICE_ID>
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
    $sql="SELECT * FROM `histo`.`releve_consolidation_d".$row["id"]."` ";
    $sql.=" WHERE date = '".date('Y-m-d')."'";
    $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
    $stmt2->execute( array() );
    
    //Ligne Existante --> Update
    if($stmt2->rowCount() > 0){
        $sqlUpdate .= "UPDATE `histo`.`releve_consolidation_d".$row["id"]."` ";
        $sqlUpdate .= " SET value=( SELECT SUM(value) FROM releve_".$row["id"]." WHERE date > '".date('Y-m-d')." 00:00:00' ) ";
        $sqlUpdate .= " WHERE date = '".date('Y-m-d')."';";
    } else { //Ligne n'existe pas --> Insert
        $sqlInsert .= "INSERT INTO `histo`.`releve_consolidation_d".$row["id"]."` ";
        $sqlInsert .= " (value, date) VALUES (";
        $sqlInsert .= " 0, '".date('Y-m-d')."'";
        $sqlInsert .= " );";
    }
}

$sqlGlobal=$sqlInsert.$sqlUpdate;
if($sqlGlobal != ""){
    $stmt = $GLOBALS["histoconnec"]->prepare($sqlGlobal);
    $stmt->execute(array());
}

?>