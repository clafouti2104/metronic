<?php

class Report{
    public $id;
    public $name;
    public $description;
    public $chart;
    public $contacts;
    public $period;
    
    public function __construct($id, $name, $description, $chart, $contacts, $period) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->chart = $chart;
        $this->contacts = $contacts;
        $this->period = $period;
    }
    
    public static function ReportExists($idReport) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM report";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idReport));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getReport($ids){
        $result = array();
        
        // On vérifie le type d'arguement passé en paramètre
        if (!is_array($ids)) {
            $ids = array($ids);
            $return_array = FALSE;
        } else {
            if (count($ids) < 1) {
                    return $result;
            }
            // On supprime les doublons
            $ids = array_unique($ids);
            $return_array = TRUE;
        }
        
        $query = "SELECT ";
        $query .= "id";
        $query .= ",name";
        $query .= ",description";
        $query .= ",chart";
        $query .= ",contacts";
        $query .= ",period";
        $query .= " FROM report ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_report = new Report($row['id'],$row['name'],$row['description'],$row["chart"],$row["contacts"],$row["period"]);

                    $result[] = $tmp_report;
                    $tmp_report = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Reports
    */
    public static function getReports() {
        $query = "SELECT id FROM report";
        //$query .= ($chart) ? " WHERE chart=1" : "";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getReport($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createReport($name,$description,$chart,$contacts,$period) {
        
        $query = "INSERT INTO report (";
        $query .= "name";
        $query .= ",description";
        $query .= ",chart ";
        $query .= ",contacts ";
        $query .= ",period ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:description";
        $query .= ",:chart ";
        $query .= ",:contacts ";
        $query .= ",:period ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":description"] = $description;
        $params[":chart"] = $chart;
        $params[":contacts"] = $contacts;
        $params[":period"] = $period;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM report ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Report($id, $name, $description, $chart,$contacts,$period);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'une Report
    */
    public function delete() {		
        $query = "DELETE FROM report";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
    
    public function update() {
        
        $query = "UPDATE report SET";
        $query .= " name=:name";
        $query .= ", description=:description";
        $query .= ", period=:period";
        $query .= ", contacts=:contacts";
        $query .= ", chart=:chart";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':name'] = $this->name;
        $params[':description'] = $this->description;
        $params[':period'] = $this->period;
        $params[':chart'] = $this->chart;
        $params[':contacts'] = $this->contacts;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le report '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }

    public function getBorneDatesReport(){
        $period = $this->period;
        switch($period){
            case '1':
                $from='P1D';
                break;
            case '2':
                $from='P7D';
                break;
            case '3':
                $from='P1M';
                break;
            case '4':
                $from='P1Y';
                break;
        }
        $dateFrom=new DateTime('now');
        $interval=new DateInterval($from);
        $interval->invert=1;
        $dateFrom->add($interval);
        
        switch($period){
            case '1':
                $duration='1';
                break;
            case '2':
                $duration='6';
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
        return "du ".$dateFrom->format('d/m H:i')." au ".$dateEnd->format('d/m H:i');
    }
}
?>
