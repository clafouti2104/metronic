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
            case '5':
                $duration='7';
                break;
            default:
                $duration='1';
        }
        $dateEnd=clone $dateFrom;
        $dateEnd->add(new DateInterval("P".$duration."D"));
        
        $query = "SELECT ";
        $query .= "date";
        switch($period){
            case '1':
                $query .= ", value ";
                break;
            case '2':
                $query .= ", value4h as tmpvalues ";
                break;
            case '3':
                $query .= ", value4h as tmpvalues ";
                break;
            case '4':
                $query .= ", value4h as tmpvalues ";
                break;
            case '5':
                $query .= ", valuehalf as tmpvalues ";
                break;
                
        }
        
        $query .= " FROM temperature_consolidation ";
        $query .= " WHERE deviceid=:deviceid";
        if($period == '1'){
            $query .= " AND (date='".$dateFrom->format('Y-m-d')."' OR date = '".$dateEnd->format('Y-m-d')."')";
        } else {
            $query .= " AND date >= '".$dateFrom->format('Y-m-d')."' AND date < '".$dateEnd->format('Y-m-d')."'";
        }
        $query .= " ORDER BY date ";
        //echo $query;
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
        
        // On récupère les élèments
        $params = array(":deviceid"	=> $deviceid);
        $jsSerie="";
        $i=1;
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $jsSerie .= ($jsSerie == "") ? ""  : ",";
            
            //Jour
            if($period == '1'){
                $values = json_decode($row['value'], TRUE);
                foreach($values as $tmpDate=>$tmpValue){
                    //1er jour, ne prend qu'à partir de l'heure souhaitée
                    if($row["date"] == $dateFrom->format('Y-m-d')){
                        $heureDepart = $dateFrom->format('H:i');
                        $heureDepart = str_replace(":", "", $heureDepart);
                        $tmpHeure = str_replace(":", "", $tmpDate);

                        if(intval($heureDepart) < intval($tmpHeure)){
                            continue;
                        }
                    }

                    //Dernier Jour
                    if($row["date"] == $dateEnd->format('Y-m-d')){
                        $heureFin = $dateEnd->format('H:i');
                        $heureFin = str_replace(":", "", $heureFin);
                        $tmpHeure = str_replace(":", "", $tmpDate);

                        if(intval($heureFin) > intval($tmpHeure)){
                            continue;
                        }

                    }

                    if($value != ""){
                        $jsSerie .= ($jsSerie == "") ? ""  : ",";
                        $date = new DateTime($row["date"]." ".$tmpDate.":00");
                        $value = $tmpValue;
                        $month = (substr($date->format('m'), 0, 1) == '0') ? substr($date->format('m'),1,1) : $date->format('m');
                        $month--;
                        $day = (substr($date->format('d'), 0, 1) == '0') ? substr($date->format('d'),1,1) : $date->format('d');
                        $hour = (substr($date->format('H'), 0, 1) == '0') ? substr($date->format('H'),1,1) : $date->format('H');
                        $minute = (substr($date->format('i'), 0, 1) == '0') ? substr($date->format('i'),1,1) : $date->format('i');
                        $jsSerie .= "[Date.UTC(".$date->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    }

                }
                
            } else {
                /*if($period == '5'){
                    
                }else{
                    
                }
                echo "\nDate Depart ".$dateFrom->format('Y-m-d H:i');
                echo "\nDate Fin ".$dateEnd->format('Y-m-d H:i');
                exit;*/
                $values = json_decode($row['tmpvalues'], TRUE);
                foreach($values as $tmpDate=>$tmpValue){
                    //1er jour, ne prend qu'à partir de l'heure souhaitée
                    if($row["date"] == $dateFrom->format('Y-m-d')){
                        $heureDepart = $dateFrom->format('H:i');
                        $heureDpart = str_replace(":", "", $heureDepart);
                        $tmpHeure = str_replace(":", "", $tmpDate);

                        if(intval($heureDepart) < intval($tmpHeure)){
                            continue;
                        }
                    }

                    //Dernier Jour
                    if($row["date"] == $dateEnd->format('Y-m-d')){
                        $heureFin = $dateEnd->format('H:i');
                        $heureFin = str_replace(":", "", $heureFin);
                        $tmpHeure = str_replace(":", "", $tmpDate);

                        if(intval($heureFin) > intval($tmpHeure)){
                            continue;
                        }

                    }

                    if($tmpValue != ""){
                        $jsSerie .= ($jsSerie == "") ? ""  : ",";
                        $date = new DateTime($row["date"]." ".$tmpDate.":00");
                        $value = $tmpValue;
                        $month = (substr($date->format('m'), 0, 1) == '0') ? substr($date->format('m'),1,1) : $date->format('m');
                        $month--;
                        $day = (substr($date->format('d'), 0, 1) == '0') ? substr($date->format('d'),1,1) : $date->format('d');
                        $hour = (substr($date->format('H'), 0, 1) == '0') ? substr($date->format('H'),1,1) : $date->format('H');
                        $minute = (substr($date->format('i'), 0, 1) == '0') ? substr($date->format('i'),1,1) : $date->format('i');
                        $jsSerie .= "[Date.UTC(".$date->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    }
                }
                
            }
            
            $i++;
        }
        $stmt = NULL;
        $jsSerie=trim($jsSerie);
        if(substr($jsSerie, -1)==','){
            $jsSerie = substr($jsSerie, 0, strlen($jsSerie) -1);
        }
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
                    $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                            $value = $stateTemp."";
                        }
                    }
                    
                    $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                    $month--;
                    $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                    $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                    $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                    if($value != ""){
                        $jsSerie .= ($jsSerie == "") ? "" : ",";
                        $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    }
                    //$jsSerie .= ($jsSerie == "") ? "" : ",";
                    //$jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),'".$value."']";
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
                    $query .= " value as somme";
                    $query .= " FROM releve_consolidation_d$deviceid";
                    $query .= " WHERE ";
                    $query .= " date = '".$dateFrom->format('Y-m-d')."'";
                    //echo "\n".$query."\n  ";
                    $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                            $value = $stateTemp."";
                        }
                    }
                    $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                    $month--;
                    $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                    $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                    $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                    if($value != ""){
                        $jsSerie .= ($jsSerie == "") ? "" : ",";
                        $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    }
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
                    $query .= " value as somme";
                    $query .= " FROM releve_consolidation_d$deviceid";
                    $query .= " WHERE ";
                    $query .= " date = '".$dateFrom->format('Y-m-d')."'";
                    //echo $query;
                    $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                            $value = $stateTemp."";
                        }
                    }
                    $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                    $month--;
                    $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                    $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                    $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                    if($value != ""){
                        $jsSerie .= ($jsSerie == "") ? "" : ",";
                        $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                    }
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateFrom->add(new DateInterval("P1D"));
                }
                break;
            case '4': //Annee
                $duration='365';
                break;
            case '5': //Semaine_heure
                $duration='7';
                $dateEnd=clone $dateFrom;
                $dateEnd->add(new DateInterval("P".$duration."D"));
                
                $query = "SELECT ";
                $query .= "date";
                $query .= ", valuehalf as tmpvalues";
                $query .= " FROM temperature_consolidation ";
                $query .= " WHERE deviceid=:deviceid";
                $query .= " AND date >= '".$dateFrom->format('Y-m-d')."' AND date < '".$dateEnd->format('Y-m-d')."'";
                $query .= " ORDER BY date ";
                
                $stmt = $GLOBALS["histoconnec"]->prepare($query);
                // On récupère les élèments
                $params = array(":deviceid"	=> $deviceid);
                $jsSerie="";
                $i=1;
                $stmt->execute($params);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $jsSerie .= ($jsSerie == "") ? ""  : ",";
                    
                    $values = json_decode($row['tmpvalues'], TRUE);
                    foreach($values as $tmpDate=>$tmpValue){
                        //1er jour, ne prend qu'à partir de l'heure souhaitée
                        if($row["date"] == $dateFrom->format('Y-m-d')){
                            $heureDepart = $dateFrom->format('H:i');
                            $heureDpart = str_replace(":", "", $heureDepart);
                            $tmpHeure = str_replace(":", "", $tmpDate);

                            if(intval($heureDepart) < intval($tmpHeure)){
                                continue;
                            }
                        }

                        //Dernier Jour
                        if($row["date"] == $dateEnd->format('Y-m-d')){
                            $heureFin = $dateEnd->format('H:i');
                            $heureFin = str_replace(":", "", $heureFin);
                            $tmpHeure = str_replace(":", "", $tmpDate);

                            if(intval($heureFin) > intval($tmpHeure)){
                                continue;
                            }

                        }

                        if($tmpValue != ""){
                            $jsSerie .= ($jsSerie == "") ? ""  : ",";
                            $date = new DateTime($row["date"]." ".$tmpDate.":00");
                            $value = $tmpValue;
                            $month = (substr($date->format('m'), 0, 1) == '0') ? substr($date->format('m'),1,1) : $date->format('m');
                            $month--;
                            $day = (substr($date->format('d'), 0, 1) == '0') ? substr($date->format('d'),1,1) : $date->format('d');
                            $hour = (substr($date->format('H'), 0, 1) == '0') ? substr($date->format('H'),1,1) : $date->format('H');
                            $minute = (substr($date->format('i'), 0, 1) == '0') ? substr($date->format('i'),1,1) : $date->format('i');
                            $jsSerie .= "[Date.UTC(".$date->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";
                        }
                    }
                    
                }
                
                
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
        $jsSerie=trim($jsSerie);
        if(substr($jsSerie, -1)==','){
            $jsSerie = substr($jsSerie, 0, strlen($jsSerie) -1);
        }
        
        return $jsSerie;
    }
    
    public static function getHistoryHighchartBarre($deviceid,$period,$from,$formula=NULL,$showDate=FALSE,$dateBegin=NULL){
        $result = array();
        //echo "rentre";exit;
        if($dateBegin != ""){
            $dateFrom=new DateTime($dateBegin);
        }else {
            $dateFrom=new DateTime('now');
            if($from != ""){
                $interval=new DateInterval($from);
                $interval->invert=1;
                $dateFrom->add($interval);
            }
            
        }
        
        $jsSerie="";
        switch($period){
            case '1': //Jour
                $duration='1';

                if(!is_null($dateBegin)){
                    for($i=0;$i<=23;$i++){
                        $dateEnd=clone $dateFrom;

                        $query = "SELECT ";
                        $query .= " SUM(value) as somme";
                        $query .= " FROM releve_$deviceid";
                        $query .= " WHERE ";
                        $query .= " date > '".$dateFrom->format('Y-m-d ').str_pad($i, 2, '0', STR_PAD_LEFT).":00:00' AND date < '".$dateEnd->format('Y-m-d ').str_pad($i, 2, '0', STR_PAD_LEFT).":59:59'";
                        //echo "\n  ".$query;
                        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                                $value = $stateTemp."";
                            }
                        }

                        if($showDate){
                            $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                            $month--;
                            $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                            $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                            $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";

                        } else {
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= $value;

                        }

                        //echo "==>".$value;
                        $stmt=NULL;
                        //echo $query." - ";
                        $dateEnd->add(new DateInterval("PT1H"));
                        $dateFrom->add(new DateInterval("PT1H"));
                    }
                    
                } else {
                    for($i=0;$i<=23;$i++){
                        $dateEnd=clone $dateFrom;

                        $query = "SELECT ";
                        $query .= " SUM(value) as somme";
                        $query .= " FROM releve_$deviceid";
                        $query .= " WHERE ";
                        $query .= " date > '".$dateFrom->format('Y-m-d H').":00:00' AND date < '".$dateEnd->format('Y-m-d H').":59:59'";
                         // echo "\n  ".$query;
                        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                                $value = $stateTemp."";
                            }
                        }

                        if($showDate){
                            $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                            $month--;
                            $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                            $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                            $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";

                        } else {
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= $value;

                        }

                        //echo "==>".$value;
                        $stmt=NULL;
                        //echo $query." - ";
                        $dateEnd->add(new DateInterval("PT1H"));
                        $dateFrom->add(new DateInterval("PT1H"));
                    }
                }
                break;
            case '2': //Semaine
                $duration='7';
                if(!is_null($dateBegin)){
                    for($i=0;$i<=6;$i++){
                        $dateEnd=clone $dateFrom;
                        $dateEnd->add(new DateInterval("P1D"));

                        /*$query = "SELECT ";
                        $query .= " SUM(value) as somme";
                        $query .= " FROM releve_$deviceid";
                        $query .= " WHERE ";
                        $query .= " date > '".$dateFrom->format('Y-m-d')." 00:00:00' AND date < '".$dateFrom->format('Y-m-d')." 23:59:59'";*/
                        
                        $query = "SELECT ";
                        $query .= " value as somme";
                        $query .= " FROM releve_consolidation_d$deviceid";
                        $query .= " WHERE ";
                        $query .= " date = '".$dateFrom->format('Y-m-d')."'";
                        //echo "\n".$query."\n  ";
                        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                                //$value = round($stateTemp, 1)."";
                                $value = $stateTemp."";
                            }
                        }

                        if($showDate){
                            $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                            $month--;
                            $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                            $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                            $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";

                        } else {
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= $value;

                        }
                        //$jsSerie .= ($jsSerie == "") ? "" : ",";
                        //$jsSerie .= $value;
                        $stmt=NULL;
                        //echo $query." - ";
                        $dateFrom->add(new DateInterval("P1D"));
                    }
                    
                } else {
                    for($i=0;$i<=6;$i++){
                        $dateEnd=clone $dateFrom;
                        $dateEnd->add(new DateInterval("P1D"));

                        /*$query = "SELECT ";
                        $query .= " SUM(value) as somme";
                        $query .= " FROM releve_$deviceid";
                        $query .= " WHERE ";
                        $query .= " date > '".$dateFrom->format('Y-m-d')." 00:00:00' AND date < '".$dateFrom->format('Y-m-d')." 23:59:59'";*/
                        
                        $query = "SELECT ";
                        $query .= " value as somme";
                        $query .= " FROM releve_consolidation_d$deviceid";
                        $query .= " WHERE ";
                        $query .= " date = '".$dateFrom->format('Y-m-d')."'";
                        
                        //echo "\n".$query."\n  ";
                        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                                //$value = round($stateTemp, 1)."";
                                $value = $stateTemp."";
                            }
                        }

                        if($showDate){
                            $month = (substr($dateFrom->format('m'), 0, 1) == '0') ? substr($dateFrom->format('m'),1,1) : $dateFrom->format('m');
                            $month--;
                            $day = (substr($dateFrom->format('d'), 0, 1) == '0') ? substr($dateFrom->format('d'),1,1) : $dateFrom->format('d');
                            $hour = (substr($dateFrom->format('H'), 0, 1) == '0') ? substr($dateFrom->format('H'),1,1) : $dateFrom->format('H');
                            $minute = (substr($dateFrom->format('i'), 0, 1) == '0') ? substr($dateFrom->format('i'),1,1) : $dateFrom->format('i');
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= "[Date.UTC(".$dateFrom->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$value."]";

                        } else {
                            $jsSerie .= ($jsSerie == "") ? "" : ",";
                            $jsSerie .= $value;

                        }
                        //$jsSerie .= ($jsSerie == "") ? "" : ",";
                        //$jsSerie .= $value;
                        $stmt=NULL;
                        //echo $query." - ";
                        $dateFrom->add(new DateInterval("P1D"));
                    }
                    
                }
                break;
            case '3': // Mois
                $duration='31';
                if(!is_null($dateBegin)){
                    $numberOfDays=$dateFrom->format('t');
                    for($i=0;$i<$numberOfDays;$i++){
                        $dateEnd=clone $dateFrom;
                        $dateEnd->add(new DateInterval("P1D"));

                        /*$query = "SELECT ";
                        $query .= " SUM(value) as somme";
                        $query .= " FROM releve_$deviceid";
                        $query .= " WHERE ";
                        $query .= " date > '".$dateFrom->format('Y-m-d')." 00:00:00' AND date < '".$dateFrom->format('Y-m-d')." 23:59:59'";*/
                        
                        $query = "SELECT ";
                        $query .= " value as somme";
                        $query .= " FROM releve_consolidation_d$deviceid";
                        $query .= " WHERE ";
                        $query .= " date = '".$dateFrom->format('Y-m-d')."'";
                        //$query .= " date > '".$dateFrom->format('Y-m-d H:i:s')."' AND date < '".$dateEnd->format('Y-m-d H:i:s')."'";
                        //echo $query;
                        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                                $value = $stateTemp."";
                            }
                        }
                        $jsSerie .= ($jsSerie == "") ? "" : ",";
                        $jsSerie .= $value;
                        $stmt=NULL;
                        //echo $query." - ";
                        $dateFrom->add(new DateInterval("P1D"));
                    }
                    
                } else {
                    for($i=0;$i<=31;$i++){
                        $dateEnd=clone $dateFrom;
                        $dateEnd->add(new DateInterval("P1D"));

                        /*$query = "SELECT ";
                        $query .= " SUM(value) as somme";
                        $query .= " FROM releve_$deviceid";
                        $query .= " WHERE ";
                        //$query .= " date > '".$dateEnd->format('Y-m-d')." 00:00:00' AND date < '".$dateEnd->format('Y-m-d')." 23:59:59'";
                        $query .= " date > '".$dateFrom->format('Y-m-d H:i:s')."' AND date < '".$dateEnd->format('Y-m-d H:i:s')."'";*/
                        
                        $query = "SELECT ";
                        $query .= " value as somme";
                        $query .= " FROM releve_consolidation_d$deviceid";
                        $query .= " WHERE ";
                        $query .= " date = '".$dateFrom->format('Y-m-d')."'";
                        
                        //echo $query;
                        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                                $value = $stateTemp."";
                            }
                        }
                        $jsSerie .= ($jsSerie == "") ? "" : ",";
                        $jsSerie .= $value;
                        $stmt=NULL;
                        //echo $query." - ";
                        $dateFrom->add(new DateInterval("P1D"));
                    }
                    
                }
                break;
            case '4': //Annee
                $duration='12';
                for($i=0;$i<=11;$i++){
                    //$dateEnd=clone $dateFrom;
                    //$dateEnd->add(new DateInterval("P1M"));

                    /*$query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_$deviceid";
                    $query .= " WHERE ";
                    //$query .= " date > '".$dateEnd->format('Y-m-d')." 00:00:00' AND date < '".$dateEnd->format('Y-m-d')." 23:59:59'";
                    $query .= " date > '".$dateFrom->format('Y-m')."-01 00:00:00"."' AND date < '".$dateFrom->format('Y-m-t')." 23:59:59'";
                    //echo $query;*/
                    $query = "SELECT ";
                    $query .= " SUM(value) as somme";
                    $query .= " FROM releve_consolidation_d$deviceid";
                    $query .= " WHERE ";
                    $query .= " date > '".$dateFrom->format('Y-m-d')."' AND date < '".$dateFrom->format('Y-m-t')." 23:59:59' ";
                    
                    $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                            $value = $stateTemp."";
                        }
                    }
                    $jsSerie .= ($jsSerie == "") ? "" : ",";
                    $jsSerie .= $value;
                    $stmt=NULL;
                    //echo $query." - ";
                    $dateFrom->add(new DateInterval("P1M"));
                }
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
            
            $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
                    $value = $stateTemp;
                }
            }
            $jsSerie .= ($jsSerie == "") ? "" : ",";
            $jsSerie .= $value;
            $dateFrom->add(new DateInterval("P1D"));
        }
        
        return $jsSerie;
    }
    
    //Renvoie les données de consommations à partir de la période
    public static function getCountForPeriodDate($deviceid, $period, $date=NULL, $year=NULL){
        $year = (is_null($year)) ? date('Y') : $year;
        $date = (is_null($date)) ? date('m') : $date;
        $query = "SELECT SUM(value) as somme ";
        $query .= " FROM releve_".$deviceid." ";
        $query .= " WHERE ";
        
        
        switch($period){
            case '1':
                $query .= " date BETWEEN '".$date." 00:00:00' AND '".$date." 23:59:59'";
                //echo $query;
                break;
            case '2':
                $auj = $date;
                $weekdays =  self::generateWeekDays($auj);
                $query .= " date BETWEEN '".$weekdays[0]." 00:00:00' AND '".$weekdays[6]." 23:59:59'";
                //echo $query;
                break;
            case '3':
                $query .= " date BETWEEN '".$year."-".$date."-01 00:00:00' AND '".$year."-".$date."-".date('t')." 23:59:59'";
                //echo $query;
                break;
            case '4':
                $query .= " date BETWEEN '".$year."-01-01 00:00:00' AND '".$year."-12-31 23:59:59'";
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
    
    //Renvoie les données de consommations à partir de la période
    public static function getCountForPeriodDateLast($deviceid, $period, $date=NULL, $year=NULL){
        $year = (is_null($year)) ? date('Y') : $year;
        $date = (is_null($date)) ? date('m') : $date;
        $query = "SELECT SUM(value) as somme ";
        $query .= " FROM releve_".$deviceid." ";
        $query .= " WHERE ";
        
        
        switch($period){
            case '1':
                $dateFrom=new DateTime($date);
                $interval=new DateInterval("P1D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')." 00:00:00' AND '".$dateFrom->format('Y-m-d')." 23:59:59'";
                //echo $query;
                break;
            case '2':
                $auj = $date;
                $dateFrom=new DateTime($date);
                $interval=new DateInterval("P7D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $weekdays =  self::generateWeekDays($dateFrom->format("Y-m-d"));
                $query .= " date BETWEEN '".$weekdays[0]." 00:00:00' AND '".$weekdays[6]." 23:59:59'";
                //echo $query;
                break;
            case '3':
                $dateFrom=new DateTime($year."-".str_pad($date, 2, '0', STR_PAD_LEFT)."-".date('d'));
                $interval=new DateInterval("P1M");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m')."-01 00:00:00' AND '".$dateFrom->format('Y-m')."-".date('t')." 23:59:59'";
                //echo $query;
                break;
            case '4':
                $query .= " date BETWEEN '".$year."-01-01 00:00:00' AND '".$year."-12-31 23:59:59'";
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
        
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
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
    
    public static function getConsolidation($deviceId, $period){
        $value=array();
        $query = "SELECT date, value, value4h, valuehalf, min, max, avg ";
        $query .= " FROM temperature_consolidation ";
        $query .= " WHERE deviceid=".$deviceId." AND ";
        
        $dateFrom=new DateTime('now');
        
        switch($period){
            case '1':
                $interval=new DateInterval("P1D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')."' AND '".$dateFrom->format('Y-m-d')."'";
                break;
            case '2':
                $interval=new DateInterval("P7D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $auj = $dateFrom->format('Y-m-d');
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')."' AND '".date('Y-m-d')."'";
                //echo $query;
                break;
            case '3':
                $interval=new DateInterval("P1M");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-')."01' AND '".$dateFrom->format('Y-m-').date('d')."'";
                break;
            case '4':
                $interval=new DateInterval("P1Y");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-')."01-01' AND '".$dateFrom->format('Y-m-d')."'";
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        //echo $query;
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
        $stmt->execute(array());
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $value[$row["date"]]=array(
                "value" => $row["value"],
                "value4h" => $row["value4h"],
                "valuehalf" => $row["valuehalf"],
                "max" => $row["max"],
                "min" => $row["min"],
                "avg" => $row["avg"]    
            );
        }
        
        
        return $value;
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
    
    public static function getDataForChart($data, $period, $incremental){
        if(count($data) == 0){
            return "Error with data";
        }
        $result="";
        switch($period){
            case '1':
                break;
            case '2':
                //Boucle sur date
                foreach($data as $date => $values){
                    $datetime = new DateTime($date);
                    if($incremental == "" || $incremental == "0" || is_null($incremental)){
                        $values = json_decode($values["value4h"], TRUE);
                        //print_r($date);
                        //print_r($values);
                        foreach($values as $tmpHour => $tmpValue){
                            //echo "<br/>".$tmpHour." - ".$tmpValue;
                            if($tmpValue != ""){
                                $month = (substr($datetime->format('m'), 0, 1) == '0') ? substr($datetime->format('m'),1,1) : $datetime->format('m');
                                $month--;
                                $day = (substr($datetime->format('d'), 0, 1) == '0') ? substr($datetime->format('d'),1,1) : $datetime->format('d');
                                $hour = (substr($tmpHour, 0, 1) == '0') ? substr($tmpHour,1,1) : $tmpHour;
                                $hour = ($hour == "") ? '0' : $hour;
                                $minute = 0;
                                $result .= ($result == "") ? "" : ",";
                                $result .= "[Date.UTC(".$datetime->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$tmpValue."]";

                            }
                        }
                        
                    } else {
                        $tmpValue = ($values["avg"] = "") ? "0" : "";
                            $month = (substr($datetime->format('m'), 0, 1) == '0') ? substr($datetime->format('m'),1,1) : $datetime->format('m');
                            $month--;
                            $day = (substr($datetime->format('d'), 0, 1) == '0') ? substr($datetime->format('d'),1,1) : $datetime->format('d');
                            $hour = 0;
                            $minute = 0;
                            $result .= ($result == "") ? "" : ",";
                            $result .= "[Date.UTC(".$datetime->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$tmpValue."]";
                        
                    }
                    //echo "<br/>";
                    //print_r($values);
                }
                //exit;
                break;
            case '3':
                //Boucle sur date
                foreach($data as $date => $values){
                    $datetime = new DateTime($date);
                    $values = json_decode($values["value4h"], TRUE);
		    //print_r($date);
		    //print_r($values);
                    foreach($values as $tmpHour => $tmpValue){
			//echo "<br/>".$tmpHour." - ".$tmpValue;
                        if($tmpValue != ""){
                            $month = (substr($datetime->format('m'), 0, 1) == '0') ? substr($datetime->format('m'),1,1) : $datetime->format('m');
                            $month--;
                            $day = (substr($datetime->format('d'), 0, 1) == '0') ? substr($datetime->format('d'),1,1) : $datetime->format('d');
			    $hour = (substr($tmpHour, 0, 1) == '0') ? substr($tmpHour,1,1) : $tmpHour;
			    $hour = ($hour == "") ? '0' : $hour;
                            $minute = 0;
                            $result .= ($result == "") ? "" : ",";
                            $result .= "[Date.UTC(".$datetime->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$tmpValue."]";

                        }
                    }
                    //echo "<br/>";
                    //print_r($values);
                }
                break;
            case '4':
                //Boucle sur date
                foreach($data as $date => $values){
                    $datetime = new DateTime($date);
                    $values = json_decode($values["value4h"], TRUE);
		    //print_r($date);
		    //print_r($values);
                    foreach($values as $tmpHour => $tmpValue){
			//echo "<br/>".$tmpHour." - ".$tmpValue;
                        if($tmpValue != ""){
                            $month = (substr($datetime->format('m'), 0, 1) == '0') ? substr($datetime->format('m'),1,1) : $datetime->format('m');
                            $month--;
                            $day = (substr($datetime->format('d'), 0, 1) == '0') ? substr($datetime->format('d'),1,1) : $datetime->format('d');
			    $hour = (substr($tmpHour, 0, 1) == '0') ? substr($tmpHour,1,1) : $tmpHour;
			    $hour = ($hour == "") ? '0' : $hour;
                            $minute = 0;
                            $result .= ($result == "") ? "" : ",";
                            $result .= "[Date.UTC(".$datetime->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$tmpValue."]";

                        }
                    }
                    //echo "<br/>";
                    //print_r($values);
                }
                break;
            case '5':
                //Boucle sur date
                foreach($data as $date => $values){
                    $datetime = new DateTime($date);
                    $values = json_decode($values["valuehalf"], TRUE);
		    //print_r($date);
		    //print_r($values);
                    foreach($values as $tmpHour => $tmpValue){
			//echo "<br/>".$tmpHour." - ".$tmpValue;
                        if($tmpValue != ""){
                            $month = (substr($datetime->format('m'), 0, 1) == '0') ? substr($datetime->format('m'),1,1) : $datetime->format('m');
                            $month--;
                            $day = (substr($datetime->format('d'), 0, 1) == '0') ? substr($datetime->format('d'),1,1) : $datetime->format('d');
			    $hour = (substr($tmpHour, 0, 1) == '0') ? substr($tmpHour,1,1) : $tmpHour;
			    $hour = ($hour == "") ? '0' : $hour;
                            $minute = 0;
                            $result .= ($result == "") ? "" : ",";
                            $result .= "[Date.UTC(".$datetime->format('Y').",".$month.",".$day.",".$hour.",".$minute."),".$tmpValue."]";

                        }
                    }
                    //echo "<br/>";
                    //print_r($values);
                }
                break;
        }
        
        return $result;
    }
    
    public static function getMinMaxForDevices($deviceIds, $period, $mode="max"){
        if(count($deviceIds) == 0){
            return "ERR - No array given";
        }
        $sqlId="";
        foreach($deviceIds as $deviceId){
            if($deviceId == ""){
                continue;
            }
            $sqlId .= ($sqlId=="") ? "" : ",";
            $sqlId .= $deviceId;
        }
        
        if($sqlId==""){
            return "ERR - No deviceId returned";
        }
        
        $dateFrom=new DateTime('now');
        $mode = (strtolower($mode) == "max") ? "MAX" : "MIN";
        
        $query = "SELECT deviceid, ".$mode."(".$mode.") as value FROM temperature_consolidation ";
        $query .= " WHERE ";
        $query .= " deviceid IN (".$sqlId.") AND ";
        switch($period){
            case '1':
                $interval=new DateInterval("P1D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')."' AND '".$dateFrom->format('Y-m-d')."'";
                break;
            case '2':
                $interval=new DateInterval("P7D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $auj = $dateFrom->format('Y-m-d');
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')."' AND '".date('Y-m-d')."'";
                //echo $query;
                break;
            case '3':
                $interval=new DateInterval("P1M");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-')."01' AND '".$dateFrom->format('Y-m-').date('d')."'";
                break;
            case '4':
                $interval=new DateInterval("P1Y");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-')."01-01' AND '".$dateFrom->format('Y-m-d')."'";
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        $query .= " GROUP BY deviceid";
        
        //echo $query;
        $value=array();
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
        $stmt->execute(array());
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $value[$row["deviceid"]]=$row["value"];
        }
        return $value;
    }
    
    public static function getTotalAvgForDevices($deviceIds, $period,$mode){
        if(count($deviceIds) == 0){
            return "ERR - No array given";
        }
        if(!in_array(strtolower($mode),array("avg","sum"))){
            return "ERR - Incorrect Mode";
        }
        $sqlId="";
        foreach($deviceIds as $deviceId){
            if($deviceId == ""){
                continue;
            }
            $sqlId .= ($sqlId=="") ? "" : ",";
            $sqlId .= $deviceId;
        }
        
        if($sqlId==""){
            return "ERR - No deviceId returned";
        }
        
        $dateFrom=new DateTime('now');
        $mode = (strtolower($mode) == "avg") ? "AVG" : "SUM";
        
        $query = "SELECT deviceid, ".$mode."(avg) as value FROM temperature_consolidation ";
        $query .= " WHERE ";
        $query .= " deviceid IN (".$sqlId.") AND ";
        switch($period){
            case '1':
                $interval=new DateInterval("P1D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')."' AND '".$dateFrom->format('Y-m-d')."'";
                break;
            case '2':
                $interval=new DateInterval("P7D");
                $interval->invert=1;
                $dateFrom->add($interval);
                $auj = $dateFrom->format('Y-m-d');
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-d')."' AND '".date('Y-m-d')."'";
                //echo $query;
                break;
            case '3':
                $interval=new DateInterval("P1M");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-m-')."01' AND '".$dateFrom->format('Y-m-').date('d')."'";
                break;
            case '4':
                $interval=new DateInterval("P1Y");
                $interval->invert=1;
                $dateFrom->add($interval);
                $query .= " date BETWEEN '".$dateFrom->format('Y-')."01-01' AND '".$dateFrom->format('Y-m-d')."'";
                break;
            default:
                return '$ERRPeriode incorrecte';
        }
        $query .= " GROUP BY deviceid";
        
        //echo $query;
        $value=array();
        $stmt = $GLOBALS["histoconnec"]->prepare($query);
        $stmt->execute(array());
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $value[$row["deviceid"]]=$row["value"];
        }
        return $value;
    }
    
    
}
?>
