<?php

class MessageDevice{
    public $id;
    public $deviceId;
    public $name;
    public $status;
    public $value;
    public $last_update;
    public $type;
    public $active;
    public $parameters;
    public $command;
    
    public function __construct($id, $deviceId, $name, $status, $value, $last_update, $type, $active,$parameters,$command) {
        $this->id = $id;
        $this->deviceId = $deviceId;
        $this->name = $name;
        $this->status = $status;
        $this->value = $value;
        $this->last_update = $last_update;
        $this->type = $type;
        $this->active = $active;
        $this->parameters = $parameters;
        $this->command = $command;
    }
    
    private static $types = array(
            /*'raspberry',
            'light',
            'sensor',
            'sensor_humidity',
            'presence'*/
    );
    
    public static function getTypes() {
        return self::$types;
    }
    
    public static function MessageDeviceExists($idMessageDevice) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM messagedevice";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idMessageDevice));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getMessageDevice($ids){
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
        $query .= ",deviceId";
        $query .= ",name";
        $query .= ",status";
        $query .= ",value";
        $query .= ",last_update";
        $query .= ",type";
        $query .= ", active ";
        $query .= ", parameters ";
        $query .= ", command ";
        $query .= " FROM messagedevice ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_messagedevice = new MessageDevice($row['id'],$row['deviceId'],$row['name'], $row['status'], $row['value'], $row['last_update'],$row['type'], $row['active'],$row["parameters"], $row["command"]);

                    $result[] = $tmp_messagedevice;
                    $tmp_messagedevice = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    public static function getMessageDeviceForCommandAndDevice($deviceId, $command){
        $result = array();
        
        $query = "SELECT ";
        $query .= "id";
        $query .= ",deviceId";
        $query .= ",name";
        $query .= ",status";
        $query .= ",value";
        $query .= ",last_update";
        $query .= ",type";
        $query .= ", active ";
        $query .= ", parameters ";
        $query .= ", command ";
        $query .= " FROM messagedevice ";
        $query .= " WHERE deviceId=:deviceId";
        $query .= " AND command=:command";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        $params = array(":deviceId"	=> $deviceId, ":command"=>$command);
        $stmt->execute($params);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp_messagedevice = new MessageDevice($row['id'],$row['deviceId'],$row['name'], $row['status'], $row['value'], $row['last_update'],$row['type'], $row['active'],$row["parameters"], $row["command"]);

                $result[] = $tmp_messagedevice;
                $tmp_messagedevice = NULL;
        }
        $stmt = NULL;
        return (count($result) > 0 ? $result[0] : NULL);
    }
    
    /**
    * @desc Renvoie tous les MessageDevices
    */
    public static function getMessageDevicesForDevice($deviceId, $active=TRUE) {
        $query = "SELECT id FROM messagedevice WHERE deviceId=".$deviceId;
        $query .= ($active) ? " AND active=1 " : "";
        $query .= " ORDER BY name";
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getMessageDevice($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createMessageDevice($deviceId, $name, $status, $value, $last_update, $type, $active=1,$parameters,$command) {
        
        $query = "INSERT INTO messagedevice (";
        $query .= "deviceId";
        $query .= ",name";
        $query .= ",status";
        $query .= ",value";
        $query .= ",last_update";
        $query .= ",type";
        $query .= ",active ";
        $query .= ",parameters ";
        $query .= ",command ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":deviceId";
        $query .= ",:name";
        $query .= ",:status";
        $query .= ",:value";
        $query .= ",:last_update";
        $query .= ",:type";
        $query .= ",:active ";
        $query .= ",:parameters ";
        $query .= ",:command ";
        $query .= ")";
        
        $params = array();
        $params[":deviceId"] = $deviceId;
        $params[":name"] = $name;
        $params[":status"] = $status;
        $params[":value"] = $value;
        $params[":last_update"] = $last_update;
        $params[":type"] = $type;
        $params[":active"] = $active;
        $params[":parameters"] = $parameters;
        $params[":command"] = $command;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le MessageDevice.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT TOP 1 id FROM messagedevice ORDER BY id DESC";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new MessageDevice($id, $deviceId, $name, $status, $value, $last_update, $type, $active=1,$parameters,$command);

        return $tmpInstance;
    }
    
    public function update() {
        
        $query = "UPDATE messagedevice SET";
        $query .= " name=:name";
        $query .= ", status=:status";
        $query .= ", type=:type";
        $query .= ", active=:active";
        $query .= ", parameters=:parameters";
        $query .= ", command=:command";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':name'] = $this->name;
        $params[':status'] = $this->status;
        $params[':type'] = $this->type;
        $params[':active'] = $this->active;
        $params[':parameters'] = $this->parameters;
        $params[':command'] = $this->command;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le messagedevice '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }
    
    /**
    * @desc Suppression d'un MessageDevice
    */
    public function delete() {		
        $query = "DELETE FROM scenariomessage";
        $query .= " WHERE messageid=:id;";
        $query .= "DELETE FROM messagedevice";
        $query .= " WHERE id=:id;";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
    }
}
?>
