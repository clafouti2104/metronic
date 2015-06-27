<?php
/**
 * 
 * Alimente la table temperature_consolidation
 */
include("../tools/config.php");

$year='2015';
$month='06';
$day='01';
$datetime=new DateTime($year."-".$month."-".$day);

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();

for($i=1;$i<=5;$i++){
    //Recherche device à historiser
    $sql="SELECT * FROM device WHERE ";
    $sql.=" active=1 AND collect IS NOT NULL AND collect > 0 AND (incremental = 0 OR incremental IS NULL)";
    $stmt = $GLOBALS["dbconnec"]->prepare($sql);
    $stmt->execute(array());

    $sqlInsert=$sqlUpdate=$sqlDelete="";
    $devices = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sql="SELECT * FROM `domo`.`temperature` ";
        $sql.=" WHERE date > '".$datetime->format('Y')."-".$datetime->format('m')."-".$datetime->format('d')." 00:00:00'";
        $sql.=" AND date < '".$datetime->format('Y')."-".$datetime->format('m')."-".$datetime->format('d')." 23:59:59'";
        $sql.=" AND deviceid=".$row["id"];
        $stmt2 = $GLOBALS["dbconnec"]->prepare($sql);
        $stmt2->execute( array() );

        $values=array();
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $date = new DateTime($row2["date"]);
            $values[$date->format('H:i')]=$row2["value"];
        }
        $sql="SELECT * FROM `histo`.`temperature_consolidation` ";
        $sql.=" WHERE date = '".$datetime->format('Y')."-".$datetime->format('m')."-".$datetime->format('d')."' AND deviceid=".$row["id"];
        $stmt3 = $GLOBALS["histoconnec"]->prepare($sql);
        $stmt3->execute( array() );
        //Ligne Existante --> Update
        if($stmt3->rowCount() > 0){
            $sqlUpdate .= "UPDATE `histo`.`temperature_consolidation` ";
            $sqlUpdate .= " SET value='".  json_encode($values)."' ";
            $sqlUpdate .= " WHERE date = '".$datetime->format('Y')."-".$datetime->format('m')."-".$datetime->format('d')."' AND deviceid=".$row["id"].";";
        } else { //Ligne n'existe pas --> Insert
            $sqlInsert .= "INSERT INTO `histo`.`temperature_consolidation` ";
            $sqlInsert .= " (deviceid, value, date) VALUES (";
            $sqlInsert .= " ".$row["id"].",'".json_encode($values)."', '".$datetime->format('Y')."-".$datetime->format('m')."-".$datetime->format('d')."'";
            $sqlInsert .= " );";
        }

        $sqlDelete.="DELETE FROM temperature WHERE deviceid=".$row["id"]."";
        $sqlDelete.=" AND date > '".$datetime->format('Y')."-".$datetime->format('m')."-".$datetime->format('d')." 00:00:00'";
        $sqlDelete.=" AND date < '".$datetime->format('Y')."-".$datetime->format('m')."-".$datetime->format('d')." 23:59:59'";
    }

    $sqlGlobal=$sqlInsert.$sqlUpdate.$sqlDelete;
    if($sqlGlobal != ""){
        $stmt = $GLOBALS["histoconnec"]->prepare($sqlGlobal);
        $stmt->execute(array());
    }
    $datetime->add(new DateInterval('P1D'));
}


?>