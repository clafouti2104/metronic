<?php

class History{
    public $id;
    public $name;
    public $date;
    public $value;
    public $deviceid;
    
    public function __construct($id, $name, $date, $value,$deviceid) {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->value = $value;
        $this->deviceid = $deviceid;
    }
    
    public static function HistoryExists($idHistory) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM temperature";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idHistory));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getHistoryHighchartLine($deviceid,$period,$from){
        $result = array();
        
        $dateFrom=new DateTime('now');
        if($from != ""){
            $interval=new DateInterval($from);
            $interval->invert=1;
            $dateFrom->add($interval);
        }
        
        switch($period){
            case '1':
                $duration='1';
                break;
            case '2':
                $duration='7';
                break;
            case '3':
                $duration='31';
                break;
            case '4':
                $duration='365';
                break;
            default:
                $duration='1';
        }
        $dateEnd=clone $dateFrom;
        $dateEnd->add(new DateInterval("P".$duration."D"));
        
        $query = "SELECT ";
        $query .= "date";
        $query .= ",value";
        $query .= " FROM temperature ";
        $query .= " WHERE deviceid=:deviceid";
        $query .= " AND date > '".$dateFrom->format('Y-m-d H:i:s')."' AND date < '".$dateEnd->format('Y-m-d H:i:s')."'";
        //echo $query;
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        $params = array(":deviceid"	=> $deviceid);
        $jsSerie="";
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date = new DateTime($row["date"]);
            $jsSerie .= ($jsSerie == "") ? ""  : ",";
            $value = $row['value'];
            $month = (substr($date->format('m'), 0, 1) == '0') ? substr($date->format('m'),1,1) : $date->format('m');
            $month--;
            $day = (substr($date->format('d'), 0, 1) == '0') ? substr($date->format('d'),1,1) : $date->format('d');
            $hour = (substr($date->format('H'), 0, 1) == '0') ? substr($date->format('H'),1,1) : $date->format('H');
            $minute = (substr($date->format('i'), 0, 1) == '0') ? substr($date->format('i'),1,1) : $date->format('i');
            $jsSerie .= "[Date.UTC(".$date->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
        }
        $stmt = NULL;
        return $jsSerie;
    }
    
    public static function getHistoryHighchartLineIncremental($deviceid,$period,$from,$formula=NULL){
        $result = array();
        //echo "rentre";exit;
        $dateFrom=new DateTime('now');
        if($from != ""){
            $interval=new DateInterval($from);
            $interval->invert=1;
            $dateFrom->add($interval);
        }
        
        $jsSerie="";
        switch($period){
            case '1': //Jour
                $duration='1';

                for($i=0;$i<=23;$i++){
                    $dateEnd=clone $dateFrom;

                    $query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_$deviceid";
                    $query .= " WHERE ";
                    $query .= " date > '".$dateFrom->format('Y-m-d H').":00:00' AND date < '".$dateEnd->format('Y-m-d H').":59:59'";
                     // echo "\n  ".$query;
                    $stmt = $GLOBALS["dbconnec"]->prepare($query);
                    $stmt->execute(array());
                    $value=0;
                    if($stmt->rowCount() > 0){
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row["somme"]!=""){
                                $value = $row["somme"];
                            }
                        }
                    }
                    //echo "==>".$value;
                    
                    if(!is_null($formula)){
                        $fonction = str_replace("x", $value, $formula);
                        @eval('$stateTemp='.$fonction.';');
                        if(isset($stateTemp)){
                            $value = round($stateTemp, 1)."";
                        }
                    }
                    
                    $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                    $month--;
                    $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                    $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                    $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                    $jsSerie .= ($jsSerie == "") ? "" : ",";
                    $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateEnd->add(new DateInterval("PT1H"));
                    $dateFrom->add(new DateInterval("PT1H"));
                }
                break;
            case '2': //Semaine
                $duration='7';
                for($i=0;$i<=6;$i++){
                    $dateEnd=clone $dateFrom;
                    $dateEnd->add(new DateInterval("P1D"));

                    $query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_$deviceid";
                    $query .= " WHERE ";
                    $query .= " date > '".$dateFrom->format('Y-m-d')." 00:00:00' AND date < '".$dateEnd->format('Y-m-d')." 23:59:59'";
                    //echo "\n".$query."\n  ";
                    $stmt = $GLOBALS["dbconnec"]->prepare($query);
                    $stmt->execute(array());
                    $value=0;
                    if($stmt->rowCount() > 0){
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row["somme"]!=""){
                                $value = $row["somme"];
                            }
                        }
                    }
                    
                    if(!is_null($formula)){
                        $fonction = str_replace("x", $value, $formula);
                        @eval('$stateTemp='.$fonction.';');
                        if(isset($stateTemp)){
                            $value = round($stateTemp, 1)."";
                        }
                    }
                    $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                    $month--;
                    $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                    $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                    $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                    $jsSerie .= ($jsSerie == "") ? "" : ",";
                    $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateFrom->add(new DateInterval("P1D"));
                }
                break;
            case '3': // Mois
                $duration='31';
                for($i=0;$i<=31;$i++){
                    $dateEnd=clone $dateFrom;
                    $dateEnd->add(new DateInterval("P1D"));

                    $query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_$deviceid";
                    $query .= " WHERE ";
                    $query .= " date > '".$dateFrom->format('Y-m-d H:i:s')."' AND date < '".$dateEnd->format('Y-m-d H:i:s')."'";
                    //echo $query;
                    $stmt = $GLOBALS["dbconnec"]->prepare($query);
                    $stmt->execute(array());
                    $value=0;
                    if($stmt->rowCount() > 0){
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row["somme"]!=""){
                                $value = $row["somme"];
                            }
                        }
                    }
                    
                    if(!is_null($formula)){
                        $fonction = str_replace("x", $value, $formula);
                        @eval('$stateTemp='.$fonction.';');
                        if(isset($stateTemp)){
                            $value = round($stateTemp, 1)."";
                        }
                    }
                    $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                    $month--;
                    $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                    $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                    $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                    $jsSerie .= ($jsSerie == "") ? "" : ",";
                    $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateFrom->add(new DateInterval("P1D"));
                }
                break;
            case '4': //Annee
                $duration='365';
                break;
            default:
                $duration='1';
        }
        
        
        //echo $query;
        /*$stmt = $GLOBALS["dbconnec"]->prepare($query);
        $params = array();
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
        }*/
        //print_r($jsSerie);
        
        return $jsSerie;
    }
    
    public static function getHistoryHighchartBarre($deviceid,$period,$from,$formula=NULL){
        $result = array();
        //echo "rentre";exit;
        $dateFrom=new DateTime('now');
        if($from != ""){
            $interval=new DateInterval($from);
            $interval->invert=1;
            $dateFrom->add($interval);
        }
        
        $jsSerie="";
        switch($period){
            case '1': //Jour
                $duration='1';

                for($i=0;$i<=23;$i++){
                    $dateEnd=clone $dateFrom;

                    $query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_$deviceid";
                    $query .= " WHERE ";
                    $query .= " date > '".$dateFrom->format('Y-m-d H').":00:00' AND date < '".$dateEnd->format('Y-m-d H').":59:59'";
                     // echo "\n  ".$query;
                    $stmt = $GLOBALS["dbconnec"]->prepare($query);
                    $stmt->execute(array());
                    $value=0;
                    if($stmt->rowCount() > 0){
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row["somme"]!=""){
                                $value = $row["somme"];
                            }
                        }
                    }
                    
                    if(!is_null($formula)){
                        $fonction = str_replace("x", $value, $formula);
                        @eval('$stateTemp='.$fonction.';');
                        if(isset($stateTemp)){
                            $value = round($stateTemp, 1)."";
                        }
                    }
                    //echo "==>".$value;
                    $jsSerie .= ($jsSerie == "") ? "" : ",";
                    $jsSerie .= $value;
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateEnd->add(new DateInterval("PT1H"));
                    $dateFrom->add(new DateInterval("PT1H"));
                }
                break;
            case '2': //Semaine
                $duration='7';
                for($i=0;$i<=6;$i++){
                    $dateEnd=clone $dateFrom;
                    $dateEnd->add(new DateInterval("P1D"));

                    $query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_$deviceid";
                    $query .= " WHERE ";
                    $query .= " date > '".$dateFrom->format('Y-m-d')." 00:00:00' AND date < '".$dateEnd->format('Y-m-d')." 23:59:59'";
                    //echo "\n".$query."\n  ";
                    $stmt = $GLOBALS["dbconnec"]->prepare($query);
                    $stmt->execute(array());
                    $value=0;
                    if($stmt->rowCount() > 0){
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row["somme"]!=""){
                                $value = $row["somme"];
                            }
                        }
                    }
                    if(!is_null($formula)){
                        $fonction = str_replace("x", $value, $formula);
                        @eval('$stateTemp='.$fonction.';');
                        if(isset($stateTemp)){
                            $value = round($stateTemp, 1)."";
                        }
                    }
                    $jsSerie .= ($jsSerie == "") ? "" : ",";
                    $jsSerie .= $value;
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateFrom->add(new DateInterval("P1D"));
                }
                break;
            case '3': // Mois
                $duration='31';
                for($i=0;$i<=31;$i++){
                    $dateEnd=clone $dateFrom;
                    $dateEnd->add(new DateInterval("P1D"));

                    $query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_$deviceid";
                    $query .= " WHERE ";
                    $query .= " date > '".$dateFrom->format('Y-m-d H:i:s')."' AND date < '".$dateEnd->format('Y-m-d H:i:s')."'";
                    //echo $query;
                    $stmt = $GLOBALS["dbconnec"]->prepare($query);
                    $stmt->execute(array());
                    $value=0;
                    if($stmt->rowCount() > 0){
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row["somme"]!=""){
                                $value = $row["somme"];
                            }
                        }
                    }
                    if(!is_null($formula)){
                        $fonction = str_replace("x", $value, $formula);
                        @eval('$stateTemp='.$fonction.';');
                        if(isset($stateTemp)){
                            $value = round($stateTemp, 1)."";
                        }
                    }
                    $jsSerie .= ($jsSerie == "") ? "" : ",";
                    $jsSerie .= $value;
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateFrom->add(new DateInterval("P1D"));
                }
                break;
            case '4': //Annee
                $duration='365';
                break;
            default:
                $duration='1';
        }
        
        
        //echo $query;
        /*$stmt = $GLOBALS["dbconnec"]->prepare($query);
        $params = array();
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
        }*/
        //print_r($jsSerie);
        
        return $jsSerie;
    }
    
    public static function getHistoryHighchartBarreCustom($deviceid,$dateFrom,$durationDays,$formula){
        $jsSerie="";
        $dateFrom = new DateTime($dateFrom);
        for($i=1;$i<=$durationDays;$i++){
            $query = "SELECT ";
            $query .= " SUM(value) as somme";
            $query .= " FROM releve_$deviceid";
            $query .= " WHERE ";
            $query .= " date > '".$dateFrom->format('Y-m-d')." 00:00:00' AND date < '".$dateFrom->format('Y-m-d')." 23:59:59'";
            
            $stmt = $GLOBALS["dbconnec"]->prepare($query);
            $stmt->execute(array());
            
            $value=0;
            if($stmt->rowCount() > 0){
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if($row["somme"]!=""){
                        $value = $row["somme"];
                    }
                }
            }
            if(!is_null($formula)){
                $fonction = str_replace("x", $value, $formula);
                @eval('$stateTemp='.$fonction.';');
                if(isset($stateTemp)){
                    $value = round($stateTemp, 1);
                }
            }
            $jsSerie .= ($jsSerie == "") ? "" : ",";
            $jsSerie .= $value;
            $dateFrom->add(new DateInterval("P1D"));
        }
        
        return $jsSerie;
    }
    
    //Renvoie les données de consommations à partir de la période
    public static function getCountForPeriod($deviceid, $period){
        $query = "SELECT SUM(value) as somme ";
        $query .= " FROM releve_".$deviceid." ";
        $query .= " WHERE ";
        
        
        switch($period){
            case '1':
                $query .= " date BETWEEN '".date('Y-m-d')." 00:00:00' AND '".date('Y-m-d')." 23:59:59'";
                break;
            case '2':
                $auj = date('Y-m-d');
                $weekdays =  self::generateWeekDays($auj);
                $query .= " date BETWEEN '".$weekdays[0]." 00:00:00' AND '".$weekdays[6]." 23:59:59'";
                //echo $query;
                break;
            case '3':
                $query .= " date BETWEEN '".date('Y-m-')."01 00:00:00' AND '".date('Y-m-t')." 23:59:59'";
                //echo $query;
                break;
            case '4':
                $query .= " date BETWEEN '".date('Y-')."01-01 00:00:00' AND '".date('Y-')."12-31 23:59:59'";
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $value=0;
        if($stmt->rowCount() > 0){
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($row["somme"]!=""){
                    $value = $row["somme"];
                }
            }
        }
        
        return $value;
    }
    
    //Renvoie les données de consommations de la période -1 à partir de la période
    public static function getCountForLastPeriod($deviceid, $period){
        $query = "SELECT SUM(value) as somme ";
        $query .= " FROM releve_".$deviceid." ";
        $query .= " WHERE ";
        
        $dateFrom=new DateTime('now');
        
        switch($period){
            case '1':
                $interval=new DateInterval("P1D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')." 00:00:00' AND '".$dateFrom->format('Y-m-d')." 23:59:59'";
                break;
            case '2':
                $interval=new DateInterval("P7D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $auj = date('Y-m-d');
                $weekdays = self::generateWeekDays($dateFrom->format('Y-m-d'));
                $query .= " date BETWEEN '".$weekdays[0]." 00:00:00' AND '".$weekdays[6]." 23:59:59'";
                break;
            case '3':
                $interval=new DateInterval("P1M");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-')."01 00:00:00' AND '".$dateFrom->format('Y-m-t')." 23:59:59'";
                break;
            case '4':
                $interval=new DateInterval("P1Y");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-')."01-01 00:00:00' AND '".$dateFrom->format('Y-m-d')." 23:59:59'";
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        //echo $query;
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $value=0;
        if($stmt->rowCount() > 0){
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($row["somme"]!=""){
                    $value = $row["somme"];
                }
            }
        }
        
        return $value;
    }
    
    //Renvoie les données de consommations de la période précédente jusqu'à l'heure actuelle
    public static function getCountForLastPeriodUntilNow($deviceid, $period){
        $query = "SELECT SUM(value) as somme ";
        $query .= " FROM releve_".$deviceid." ";
        $query .= " WHERE ";
        
        $dateFrom=new DateTime('now');
        
        switch($period){
            case '1':
                $interval=new DateInterval("P1D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')." 00:00:00' AND '".$dateFrom->format('Y-m-d H:i:s')."'";
                break;
            case '2':
                $interval=new DateInterval("P7D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $auj = $dateFrom->format('Y-m-d');
                $weekdays =  self::generateWeekDays($auj);
                $query .= " date BETWEEN '".$weekdays[0]." 00:00:00' AND '".$dateFrom->format('Y-m-d H:i:s')."'";
                //echo $query;
                break;
            case '3':
                $interval=new DateInterval("P1M");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-')."01 00:00:00' AND '".$dateFrom->format('Y-m-').date('d')." ".date('H:i:s')."'";
                break;
            case '4':
                $interval=new DateInterval("P1Y");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-')."01-01 00:00:00' AND '".$dateFrom->format('Y-m-d')." ".date('H:i:s')."'";
                //echo $query;
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        //echo $query;
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $value=0;
        if($stmt->rowCount() > 0){
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($row["somme"]!=""){
                    $value = $row["somme"];
                }
            }
        }
        
        return $value;
    }
    
    /**
    * @desc Renvoie tous les Historys
    */
    public static function getHistorys($value=TRUE) {
        $query = "SELECT id FROM temperature";
        $query .= ($value) ? " WHERE value=1" : "";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getHistory($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createHistory($name,$date,$value=1,$deviceid) {
        
        $query = "INSERT INTO temperature (";
        $query .= "name";
        $query .= ",date";
        $query .= ",value ";
        $query .= ",deviceid ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:date";
        $query .= ",:value ";
        $query .= ",:deviceid ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":date"] = $date;
        $params[":value"] = $value;
        $params[":deviceid"] = $deviceid;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM temperature ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new History($id, $name, $date, $value,$deviceid);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'une History
    */
    public function delete() {		
        $query = "DELETE FROM temperature";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
    
    public static function generateWeekDays($auj){
        $weekdays = array();
        $t_auj = strtotime($auj);
        $p_auj = date('N', $t_auj);
        if($p_auj == 1){
          $deb = $t_auj;
          $fin = strtotime($auj.' + 6 day');
        }
        else if($p_auj == 7){
          $deb = strtotime($auj.' - 6 day');
          $fin = $t_auj;
        }
        else{
          $deb = strtotime($auj.' - '.(6-(7-$p_auj)).' day');
          $fin = strtotime($auj.' + '.(7-$p_auj).' day');
        }
        while($deb <= $fin){
            $weekdays[] = date('Y-m-d', $deb);
            $deb += 86400;
        }
        return $weekdays;
    }
}
?>
