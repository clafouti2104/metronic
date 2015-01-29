<?php

class CondAction{
    public $id;
    public $condId;
    public $type;
    public $action;
    public $value;
    
    public function __construct($id, $condId, $type, $action, $value) {
        $this->id = $id;
        $this->condId = $condId;
        $this->type = $type;
        $this->action = $action;
        $this->value = $value;
    }
    
    public static function CondActionExists($idCondAction) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM condaction";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idCondAction));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getCondAction($ids){
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
        $query .= ",condId";
        $query .= ",type";
        $query .= ",action";
        $query .= ",value";
        $query .= " FROM condaction ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id" => $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_condaction = new CondAction($row['id'],$row['condId'],$row['type'],$row['action'],$row["value"]);

                    $result[] = $tmp_condaction;
                    $tmp_condaction = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les CondActions pour un device donné
    */
    public static function getCondActionsByDevice($type) {
        $query = "SELECT id FROM condaction";
        $query .= " WHERE type=".$type;
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);
        return self::getCondAction($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les CondActions pour le condId donné
    */
    public static function getCondActionForCond($condId) {
        $query = "SELECT id FROM condaction";
        $query .= " WHERE condId=".$condId;
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getCondAction($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les CondActions
    */
    public static function getCondActions() {
        $query = "SELECT id FROM condaction";
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getCondAction($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createCondAction($condId,$type,$action,$value) {
        
        $query = "INSERT INTO condaction (";
        $query .= "condId";
        $query .= ",type";
        $query .= ",action";
        $query .= ",value ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":condId";
        $query .= ",:type";
        $query .= ",:action";
        $query .= ",:value ";
        $query .= ")";
        
        $params = array();
        $params[":condId"] = $condId;
        $params[":type"] = $type;
        $params[":action"] = $action;
        $params[":value"] = $value;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM condaction ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new CondAction($id, $condId, $type, $action, $value);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'un CondAction
    */
    public function delete() {		
        $query = "DELETE FROM condaction";
        $query .= " WHERE id=:id;";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
    }
    
    public function update() {
        
        $query = "UPDATE condaction SET";
        $query .= " condId=:condId";
        $query .= ", action=:action";
        $query .= ", value=:value";
        $query .= ", type=:type";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':condId'] = $this->condId;
        $params[':action'] = $this->action;
        $params[':value'] = $this->value;
        $params[':type'] = $this->type;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le messagedevice '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }
    
    
}
?>
