<?php
/**
 * 
 * Alimente la table temperature_consolidation - champ 4h (à exécuter toutes les 6h)
 */
include("../tools/config.php");

$year='2015';
$month='06';
$day='01';
$datetime=new DateTime($year."-".$month."-".$day);

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();


for($i=1;$i<=10;$i++){
    //Recherche device à historiser
    $sql="SELECT * FROM device WHERE ";
    $sql.=" active=1 AND collect IS NOT NULL AND collect > 0";
    $stmt = $GLOBALS["dbconnec"]->prepare($sql);
    $stmt->execute(array());
    
    $heureCourante = date('H');
    $loop = intval($heureCourante / 4);
    $sqlInsert=$sqlUpdate="";
    $devices = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //Non incrémental
        if($row["incremental"] == "" || $row["incremental"] == "0"){
            $result = array();
            //Parcours des tranches de 4h
            for($i=1; $i<=6; $i++){
                $tmpHeureStart = ($i - 1) * 4;
                $tmpHeureEnd = ($i * 4) - 1;

                $query="SELECT AVG(value) as avgValue FROM temperature_".$row["id"];
                $query.=" WHERE date > '".$datetime->format('Y-m-d')." ".$tmpHeureStart.":00:00' ";
                $query.=" AND date < '".$datetime->format('Y-m-d')." ".$tmpHeureEnd.":59:59' ";
                $stmt2 = $GLOBALS["histoconnec"]->prepare($query);
                $stmt2->execute( array() );
                if($row2=$stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $result[$tmpHeureStart] = number_format($row2["avgValue"], 2);
                }
            }
        }

        //Incremental
        if($row["incremental"] != "" && $row["incremental"] > "0"){
            $result = array();
            //Parcours des tranches de 4h
            for($i=1; $i<=$loop; $i++){
                $tmpHeureStart = ($i - 1) * 4;
                $tmpHeureEnd = ($i * 4) - 1;

                $query="SELECT SUM(value) as sumValue FROM releve_".$row["id"];
                $query.=" WHERE date > '".$datetime->format('Y-m-d')." ".$tmpHeureStart.":00:00' ";
                $query.=" AND date < '".$datetime->format('Y-m-d')." ".$tmpHeureEnd.":59:59' ";
                $stmt2 = $GLOBALS["histoconnec"]->prepare($query);
                $stmt2->execute( array() );
                if($row2=$stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $result[$tmpHeureStart] = $row2["sumValue"];
                }
            }
        }

        $sqlUpdate .= "UPDATE `histo`.`temperature_consolidation`";
        $sqlUpdate .= " SET value4h='".  json_encode($result)."'";
        $sqlUpdate .= " WHERE deviceid=".$row["id"]." ";
        $sqlUpdate .= " AND date='".$datetime->format('Y-m-d')."'; ";

    }

    $sqlGlobal=$sqlInsert.$sqlUpdate;
    if($sqlGlobal != ""){
        echo $sqlGlobal;
        $stmt = $GLOBALS["histoconnec"]->prepare($sqlGlobal);
        $stmt->execute(array());
    }
    
    $datetime->add(new DateInterval('P1D'));
}


?>