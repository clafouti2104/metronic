<?php
/**
 * 
 * Alimente la table temperature_consolidation - champ Halfh (à exécuter toutes les 6h)
 */
include("../tools/config.php");

$year='2015';
$month='06';
$day='01';
$datetime=new DateTime($year."-".$month."-".$day);

$GLOBALS["dbconnec"] = connectDB();
$GLOBALS["histoconnec"] = connectHistoDB();


while(true){
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
        $datetimehourBegin=new DateTime($datetime->format('Y-m-d')." 00:00:00");
        $datetimehourEnd=new DateTime($datetime->format('Y-m-d')." 00:30:00");
        //Non incrémental
        if($row["incremental"] == "" || $row["incremental"] == "0"){
            $result = array();
            //Parcours des tranches de 4h
            for($i=1; $i<=48; $i++){
                
                $query="SELECT AVG(value) as avgValue FROM temperature_".$row["id"];
                $query.=" WHERE date >= '".$datetimehourBegin->format('Y-m-d H:i:s')."' ";
                $query.=" AND date < '".$datetimehourEnd->format('Y-m-d H:i:s')."' ";
                $stmt2 = $GLOBALS["histoconnec"]->prepare($query);
                $stmt2->execute( array() );
                if($row2=$stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $result[$datetimehourBegin->format('H:i')] = number_format($row2["avgValue"], 2);
                }
                $datetimehourBegin->add(new DateInterval('PT30M'));
                $datetimehourEnd->add(new DateInterval('PT30M'));
            }
        } else {

        //Incremental
        //if($row["incremental"] != "" && $row["incremental"] > "0"){
            $result = array();
            //Parcours des tranches de 4h
            for($i=1; $i<=48; $i++){
                
                $query="SELECT SUM(value) as sumValue FROM releve_".$row["id"];
                $query.=" WHERE date >= '".$datetimehourBegin->format('Y-m-d H:i:s')."' ";
                $query.=" AND date < '".$datetimehourEnd->format('Y-m-d H:i:s')."' ";
                $stmt2 = $GLOBALS["histoconnec"]->prepare($query);
                $stmt2->execute( array() );
                if($row2=$stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $result[$datetimehourBegin->format('H:i')] = $row2["sumValue"];
                }
                $datetimehourBegin->add(new DateInterval('PT30M'));
                $datetimehourEnd->add(new DateInterval('PT30M'));
            }
        }

        if($row["id"] == '44'){
            echo $sqlUpdate;
        }
        $sqlUpdate .= "UPDATE `histo`.`temperature_consolidation`";
        $sqlUpdate .= " SET valuehalf='".  json_encode($result)."'";
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