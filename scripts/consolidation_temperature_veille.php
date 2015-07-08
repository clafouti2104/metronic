<?php
/**
 * 
 * Alimente la table temperature_consolidation: met à jour les champs 
 *      MIN valeur min pour non incremental
 *      MAX: valeur max pour non incremental
 *      AVG: 
 *          moyenne pour non incremental
 *          total pour incremental
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();

$yesterday=new DateTime(date('Y-m-d')." 00:00:00");
$yesterday->sub(new DateInterval('P1D'));

//Recherche device à historiser
$sql="SELECT * FROM device WHERE ";
$sql.=" active=1 AND collect IS NOT NULL AND collect > 0";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());

$sqlInsert=$sqlUpdate="";
$devices = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $min=$max=$avg=NULL;
    
    //Non incremental: récupère min, max & avg
    if($row["incremental"] == "" || $row["incremental"] == "0"){
        //Récupération moyenne
        $sql="SELECT AVG(value) as moyenne FROM `histo`.`temperature_".$row["id"]."` ";
        $sql.=" WHERE date >= '".$yesterday->format('Y-m-d')." 00:00:00' AND date < '".$yesterday->format('Y-m-d')." 23:59:59'";
        $sql.=" AND value IS NOT NULL";
        $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
        $stmt2->execute( array() );
        if($row2 = $stmt2->fetch(PDO::FETCH_ASSOC) ){
            $avg=$row3["moyenne"];
        }
        
        //Récupération Minimum
        $sql="SELECT value as min, date FROM `histo`.`temperature_".$row["id"]."` ";
        $sql.=" WHERE date >= '".$yesterday->format('Y-m-d')." 00:00:00' AND date < '".$yesterday->format('Y-m-d')." 23:59:59'";
        $sql.=" AND value IS NOT NULL";
        $sql.="  ORDER BY value ASC LIMIT 1 ";
        $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
        $stmt2->execute( array() );
        if($row2 = $stmt2->fetch(PDO::FETCH_ASSOC) ){
            $min=$row3["min"];
        }
        
        //Récupération Maximum
        $sql="SELECT value as max, date FROM `histo`.`temperature_".$row["id"]."` ";
        $sql.=" WHERE date >= '".$yesterday->format('Y-m-d')." 00:00:00' AND date < '".$yesterday->format('Y-m-d')." 23:59:59'";
        $sql.=" AND value IS NOT NULL";
        $sql.="  ORDER BY value DESC LIMIT 1 ";
        $stmt2 = $GLOBALS["histoconnec"]->prepare($sql);
        $stmt2->execute( array() );
        if($row2 = $stmt2->fetch(PDO::FETCH_ASSOC) ){
            $max=$row3["max"];
        }
    }else{
        //Incremental: ne recupere que le total sur la journée
        $sql="SELECT SUM(value) as total FROM `histo`.`releve_".$row["id"]."` ";
        $sql.=" WHERE date >= '".$yesterday->format('Y-m-d')." 00:00:00' AND date < '".$yesterday->format('Y-m-d')." 23:59:59'";
        $stmt3 = $GLOBALS["histoconnec"]->prepare($sql);
        $stmt3->execute( array() );
        if($row3 = $stmt3->fetch(PDO::FETCH_ASSOC) ){
            $avg=$row3["total"];
        }
    }
    
    $sqlUpdate .= "UPDATE `histo`.`temperature_consolidation` ";
    $sqlUpdate .= " SET avg='".  $avg."' ";
    $sqlUpdate .= (!is_null($min)) ? " ,min=".$min." " : "";
    $sqlUpdate .= (!is_null($max)) ? " ,min=".$max." " : "";
    $sqlUpdate .= " WHERE date = '".$yesterday->format('Y-m-d')."' AND deviceid=".$row["id"].";";
}

$sqlGlobal=$sqlInsert.$sqlUpdate;
if($sqlGlobal != ""){
    $stmt = $GLOBALS["histoconnec"]->prepare($sqlGlobal);
    $stmt->execute(array());
}

?>