<?php

class ScheduleAction{
    public $id;
    public $scheduleId;
    public $type;
    public $action;
    public $value;
    public $more;
    
    public function __construct($id, $scheduleId, $type, $action, $value, $more) {
        $this->id = $id;
        $this->scheduleId = $scheduleId;
        $this->type = $type;
        $this->action = $action;
        $this->value = $value;
        $this->more = $more;
    }
    
    public static function ScheduleActionExists($idScheduleAction) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM scheduleaction";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idScheduleAction));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getScheduleAction($ids){
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
        $query .= ",scheduleId";
        $query .= ",type";
        $query .= ",action";
        $query .= ",value";
        $query .= ",more";
        $query .= " FROM scheduleaction ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id" => $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_scheduleaction = new ScheduleAction($row['id'],$row['scheduleId'],$row['type'],$row['action'],$row["value"],$row["more"]);

                    $result[] = $tmp_scheduleaction;
                    $tmp_scheduleaction = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les ScheduleActions pour un device donné
    */
    public static function getScheduleActionsByDevice($type) {
        $query = "SELECT id FROM scheduleaction";
        $query .= " WHERE type=".$type;
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);
        return self::getScheduleAction($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les ScheduleActions pour le scheduleId donné
    */
    public static function getScheduleActionForSchedule($scheduleId) {
        $query = "SELECT id FROM scheduleaction";
        $query .= " WHERE scheduleId=".$scheduleId;
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getScheduleAction($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les ScheduleActions
    */
    public static function getScheduleActions() {
        $query = "SELECT id FROM scheduleaction";
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getScheduleAction($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createScheduleAction($scheduleId,$type,$action,$value,$more=NULL) {
        
        $query = "INSERT INTO scheduleaction (";
        $query .= "scheduleId";
        $query .= ",type";
        $query .= ",action";
        $query .= ",value ";
        $query .= ",more ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":scheduleId";
        $query .= ",:type";
        $query .= ",:action";
        $query .= ",:value ";
        $query .= ",:more ";
        $query .= ")";
        
        $params = array();
        $params[":scheduleId"] = $scheduleId;
        $params[":type"] = $type;
        $params[":action"] = $action;
        $params[":value"] = $value;
        $params[":more"] = $more;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM scheduleaction ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new ScheduleAction($id, $scheduleId, $type, $action, $value,$more);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'un ScheduleAction
    */
    public function delete() {		
        $query = "DELETE FROM scheduleaction";
        $query .= " WHERE id=:id;";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
    }
    
    public function update() {
        
        $query = "UPDATE scheduleaction SET";
        $query .= " scheduleId=:scheduleId";
        $query .= ", action=:action";
        $query .= ", value=:value";
        $query .= ", type=:type";
        $query .= ", more=:more";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':scheduleId'] = $this->scheduleId;
        $params[':action'] = $this->action;
        $params[':value'] = $this->value;
        $params[':type'] = $this->type;
        $params[':more'] = $this->more;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le messagedevice '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }
    
    
}
?>
