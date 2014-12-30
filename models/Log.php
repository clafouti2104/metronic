<?php

class Log{
    public $id;
    public $rfid;
    public $value;
    public $date;
    public $deviceid;
    public $level;
    
    public function __construct($id, $rfid, $value, $date, $deviceid, $level) {
        $this->id = $id;
        $this->rfid = $rfid;
        $this->value = $value;
        $this->date = $date;
        $this->deviceid = $deviceid;
        $this->level = $level;
    }
    
    private static $levels = array(
            10 => 'info', //Temperature
            40 => 'alert', // alert
            50 => 'important', // Alarme
            80 => 'error', // Perte de communication
    );
    
    public static function getLevels() {
        return self::$levels;
    }
    
    public static function getLog($ids){
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
        $query .= ",rfid";
        $query .= ",value";
        $query .= ",date";
        $query .= ",deviceid";
        $query .= ",level";
        $query .= " FROM log ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_log = new Log($row['id'],$row['rfid'],$row['value'], $row['date'], $row['deviceid'],$row['level']);

                    $result[] = $tmp_log;
                    $tmp_log = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    public static function getLastLogs($number){
        $result = array();
        
        $query = "SELECT ";
        $query .= "id";
        $query .= ",rfid";
        $query .= ",value";
        $query .= ",date";
        $query .= ",deviceid";
        $query .= ",level";
        $query .= " FROM log ";
        $query .= " ORDER BY id DESC";
        $query .= " LIMIT ".$number;
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        $params = array();
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp_log = new Log($row['id'],$row['rfid'],$row['value'], $row['date'], $row['deviceid'], $row['level']);

                $result[] = $tmp_log;
                $tmp_log = NULL;
        }
        
        $stmt = NULL;
        return $result;
    }
    
    public static function getLastLogsDate($date){
        $result = array();
        
        $query = "SELECT ";
        $query .= "id";
        $query .= ",rfid";
        $query .= ",value";
        $query .= ",date";
        $query .= ",deviceid";
        $query .= ",level";
        $query .= " FROM log ";
        $query .= " WHERE date > '".$date."'";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        $params = array();
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp_log = new Log($row['id'],$row['rfid'],$row['value'], $row['date'], $row['deviceid'], $row['level']);

                $result[] = $tmp_log;
                $tmp_log = NULL;
        }
        
        $stmt = NULL;
        return $result;
    }
    
    /**
    * @desc Renvoie tous les Logs
    */
    public static function getLogs() {
        $query = "SELECT id FROM log";

        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getLog($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createLog($rfid, $value, $date, $deviceid, $level) {
        
        $query = "INSERT INTO log (";
        $query .= "rfid";
        $query .= ",value";
        $query .= ",date";
        $query .= ",deviceid";
        $query .= ",level";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":rfid";
        $query .= ",:value";
        $query .= ",NOW()";
        $query .= ",:deviceid";
        $query .= ",:level";
        $query .= ")";
        
        $params = array();
        $params[":rfid"] = $rfid;
        $params[":value"] = $value;
        //$params[":date"] = $date;
        $params[":deviceid"] = $deviceid;
        $params[":level"] = $level;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Log.");
        }
        $stmt = NULL;

        $tmpInstance = new Log(0, $rfid, $value, $date, $deviceid, $level);

        return $tmpInstance;
    }
}
?>
