<?php

class Condition{
    public $id;
    public $condId;
    public $type;
    public $operator;
    public $value;
    public $objectId;
    
    public function __construct($id, $condId, $type, $operator, $value, $objectId) {
        $this->id = $id;
        $this->condId = $condId;
        $this->type = $type;
        $this->operator = $operator;
        $this->value = $value;
        $this->objectId = $objectId;
    }
    
    public static function ConditionExists($idCondition) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM conditions";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idCondition));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getCondition($ids){
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
        $query .= ",operator";
        $query .= ",value";
        $query .= ",objectId";
        $query .= " FROM conditions ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id" => $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_condition = new Condition($row['id'], $row["condId"],$row['type'],$row['operator'],$row["value"],$row["objectId"]);

                    $result[] = $tmp_condition;
                    $tmp_condition = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    public static function getConditionForCond($ids){
        $result = array();
        
        $query = "SELECT ";
        $query .= "id";
        $query .= ",condId";
        $query .= ",type";
        $query .= ",operator";
        $query .= ",value";
        $query .= ",objectId";
        $query .= " FROM conditions ";
        $query .= " WHERE condId=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        $params = array(":id" => $ids);
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp_condition = new Condition($row['id'], $row["condId"],$row['type'],$row['operator'],$row["value"],$row["objectId"]);

                $result[] = $tmp_condition;
                $tmp_condition = NULL;
        }
        $stmt = NULL;
        return $result;
    }
    
    /**
    * @desc Renvoie tous les Conditions pour un device donné
    */
    public static function getConditionsByDevice($type) {
        $query = "SELECT id FROM conditions";
        $query .= " WHERE type=".$type;
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);
        return self::getCondition($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les Conditions
    */
    public static function getConditions() {
        $query = "SELECT id FROM condition";
        $query .= " ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getCondition($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createCondition($condId,$type,$operator,$value,$objectId) {
        
        $query = "INSERT INTO conditions (";
        $query .= "condId";
        $query .= ",type";
        $query .= ",operator";
        $query .= ",value ";
        $query .= ",objectId ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":condId";
        $query .= ",:type";
        $query .= ",:operator";
        $query .= ",:value ";
        $query .= ",:objectId ";
        $query .= ")";
        
        echo $query;
        
        $params = array();
        $params[":condId"] = $condId;
        $params[":type"] = $type;
        $params[":operator"] = $operator;
        $params[":value"] = $value;
        $params[":objectId"] = $objectId;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM conditions ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Condition($id, $condId, $type, $operator, $value, $objectId);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'un Condition
    */
    public function delete() {		
        $query = "DELETE FROM conditions";
        $query .= " WHERE id=:id;";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
    }
    
    public function update() {
        
        $query = "UPDATE conditions SET";
        $query .= " condId=:condId";
        $query .= ", operator=:operator";
        $query .= ", value=:value";
        $query .= ", objectId=:objectId";
        $query .= ", type=:type";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':condId'] = $this->condId;
        $params[':operator'] = $this->operator;
        $params[':value'] = $this->value;
        $params[':objectId'] = $this->objectId;
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
