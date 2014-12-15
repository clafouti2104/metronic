<?php

class Chart{
    public $id;
    public $name;
    public $description;
    public $type;
    public $period;
    public $from;
    public $size;
    public $abscisse;
    public $ordonne;
    public $scaleMin;
    public $scaleMax;
    
    public function __construct($id, $name, $description, $type, $period, $from, $size, $abscisse, $ordonne, $scaleMin, $scaleMax) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->period = $period;
        $this->from = $from;
        $this->size = $size;
        $this->abscisse = $abscisse;
        $this->scaleMin = $scaleMin;
        $this->scaleMax = $scaleMax;
    }
        
    private static $types = array(
            "temps",
            "ligne",
            "barre"
    );
    
    public static function getTypes() {
        return self::$types;
    }
        
    private static $periods = array(
            '1' => "jour",
            '2' => "semaine",
            '3' => "mois",
            '4' => "annee"
    );
    
    public static function getPeriods() {
        return self::$periods;
    }
    
    public static function ChartExists($idChart) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM chart";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idChart));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getChart($ids){
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
        $query .= ",type";
        $query .= ",period";
        $query .= ",froms";
        $query .= ",size";
        $query .= ",abs";
        $query .= ",ord";
        $query .= ",scaleMin";
        $query .= ",scaleMax";
        $query .= " FROM chart ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_chart = new Chart($row['id'],$row['name'],$row['description'],$row["type"],$row["period"],$row["froms"], $row["size"], $row["abs"], $row["ord"], $row["scaleMin"], $row["scaleMax"]);

                    $result[] = $tmp_chart;
                    $tmp_chart = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Charts
    */
    public static function getCharts() {
        $query = "SELECT id FROM chart";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getChart($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createChart($name,$description,$type,$period,$from,$size,$abs,$ord,$scaleMin,$scaleMax) {
        
        $query = "INSERT INTO chart (";
        $query .= "name";
        $query .= ",description";
        $query .= ",type ";
        $query .= ",period ";
        $query .= ",froms ";
        $query .= ",size ";
        $query .= ",abs ";
        $query .= ",ord ";
        $query .= ",scaleMin ";
        $query .= ",scaleMax ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:description";
        $query .= ",:type ";
        $query .= ",:period ";
        $query .= ",:froms ";
        $query .= ",:size ";
        $query .= ",:abs ";
        $query .= ",:ord ";
        $query .= ",:scaleMin ";
        $query .= ",:scaleMax ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":description"] = $description;
        $params[":type"] = $type;
        $params[":period"] = $period;
        $params[":froms"] = $from;
        $params[":size"] = $size;
        $params[":abs"] = $abs;
        $params[":ord"] = $ord;
        $params[":scaleMin"] = $scaleMin;
        $params[":scaleMax"] = $scaleMax;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM chart ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Chart($id, $name, $description, $type, $period, $from, $size, $abs, $ord, $scaleMin, $scaleMax);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'un Chart
    */
    public function delete() {		
        $query = "DELETE FROM chart";
        $query .= " WHERE id=:id;";
        $query .= "DELETE FROM chartdevice";
        $query .= " WHERE chartid=:id;";
        $query .= "DELETE FROM pageitem";
        $query .= " WHERE chartId=:id;";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
    }
    
    public function getBorneDates(){
        $from = $this->from;
        $period = $this->period;
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
        return "du ".$dateFrom->format('d/m H:i')." au ".$dateEnd->format('d/m H:i');
    }
    
    public function getHeureFormatted(){
        $dateFrom=new DateTime('now');
        return $dateFrom->format('H');
    }
    
    public function getDayFormatted(){
        $dateFrom=new DateTime('now');
        return $dateFrom->format('d');
    }
    
    public function getDaysForWeek(){
        $days="";
        $dateFrom=new DateTime('now');
        $interval=new DateInterval("P6D");
        $interval->invert=1;
        $dateFrom->add($interval);
        $days .= "'".$dateFrom->format('d')."'";
        for($i=1;$i<=6;$i++){
            $interval=new DateInterval("P1D");
            $dateFrom->add($interval);
            $days .= ",'".$dateFrom->format('d')."'";
        }
        return $days;
    }
    
    public function getMonthFormatted(){
        $dateFrom=new DateTime('now');
        return $dateFrom->format('d');
    }
}
?>
