<?php

class Alert{
    public $id;
    public $deviceId;
    public $operator;
    public $value;
    public $notificationId;
    public $message;
    public $sent;
    public $last_sent;
    
    public function __construct($id, $deviceId, $operator, $value, $notificationId, $message, $sent, $last_sent) {
        $this->id = $id;
        $this->deviceId = $deviceId;
        $this->operator = $operator;
        $this->value = $value;
        $this->notificationId = $notificationId;
        $this->message = $message;
        $this->sent = $sent;
        $this->last_sent = $last_sent;
    }
    
    public static function AlertExists($idAlert) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM alert";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idAlert));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getAlert($ids){
        $result = array();
        
        // On vérifie le value d'arguement passé en paramètre
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
        $query .= ",deviceId";
        $query .= ",operator";
        $query .= ",value";
        $query .= ",notificationId";
        $query .= ",message";
        $query .= ",sent";
        $query .= ",last_sent";
        $query .= " FROM alert ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_alert = new Alert($row['id'],$row['deviceId'],$row['operator'],$row["value"],$row["notificationId"],$row["message"], $row["sent"], $row["last_sent"]);

                    $result[] = $tmp_alert;
                    $tmp_alert = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Alerts pour un device donné
    */
    public static function getAlertsByDevice($deviceId) {
        $query = "SELECT id FROM alert";
        $query = " WHERE deviceId=".$deviceId;
        $query .= " ORDER BY deviceId";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getAlert($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les Alerts
    */
    public static function getAlerts() {
        $query = "SELECT id FROM alert";
        $query .= " ORDER BY deviceId";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getAlert($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createAlert($deviceId,$operator,$value,$notificationId,$message) {
        
        $query = "INSERT INTO alert (";
        $query .= "deviceId";
        $query .= ",operator";
        $query .= ",value ";
        $query .= ",notificationId ";
        $query .= ",message ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":deviceId";
        $query .= ",:operator";
        $query .= ",:value ";
        $query .= ",:notificationId ";
        $query .= ",:message ";
        $query .= ")";
        
        $params = array();
        $params[":deviceId"] = $deviceId;
        $params[":operator"] = $operator;
        $params[":value"] = $value;
        $params[":notificationId"] = $notificationId;
        $params[":message"] = $message;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM alert ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Alert($id, $deviceId, $operator, $value, $notificationId, $message);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'un Alert
    */
    public function delete() {		
        $query = "DELETE FROM alert";
        $query .= " WHERE id=:id;";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
    }
    
    public function update() {
        
        $query = "UPDATE alert SET";
        $query .= " operator=:operator";
        $query .= ", value=:value";
        $query .= ", notificationId=:notificationId";
        $query .= ", message=:message";
        $query .= ", sent=:sent";
        $query .= ", last_sent=:last_sent";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':operator'] = $this->operator;
        $params[':value'] = $this->value;
        $params[':notificationId'] = $this->notificationId;
        $params[':message'] = $this->message;
        $params[':sent'] = $this->sent;
        $params[':last_sent'] = $this->last_sent;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le messagedevice '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }
    
    
}
?>
