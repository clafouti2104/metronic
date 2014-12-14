<?php

class ScenarioMessage{
    public $id;
    public $scenarioid;
    public $messageid;
    public $position;
    public $pause;
    
    public function __construct($id, $scenarioid, $messageid, $position, $pause) {
        $this->id = $id;
        $this->scenarioid = $scenarioid;
        $this->messageid = $messageid;
        $this->position = $position;
        $this->pause = $pause;
    }
    
    public static function ScenarioMessageExists($idScenarioMessage) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM scenariomessage";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idScenarioMessage));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getScenarioMessage($ids){
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
        $query .= ",scenarioid";
        $query .= ",messageid";
        $query .= ",position";
        $query .= ",pause";
        $query .= " FROM scenariomessage ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_scenariomessage = new ScenarioMessage($row['id'],$row['scenarioid'],$row['messageid'],$row["position"], $row["pause"]);

                    $result[] = $tmp_scenariomessage;
                    $tmp_scenariomessage = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les ScenarioMessages
    */
    public static function getScenarioMessagesForScenario($scenarioid,$position=TRUE) {
        $query = "SELECT id FROM scenariomessage";
        $query .= " WHERE scenarioid=".$scenarioid;
        $query .= ($position) ? " ORDER BY position": "";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getScenarioMessage($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createScenarioMessage($scenarioid,$messageid,$position,$pause=NULL) {
        
        $query = "INSERT INTO scenariomessage (";
        $query .= "scenarioid";
        $query .= ",messageid";
        $query .= ",position ";
        $query .= ",pause ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":scenarioid";
        $query .= ",:messageid";
        $query .= ",:position ";
        $query .= ",:pause ";
        $query .= ")";
        
        $params = array();
        $params[":scenarioid"] = $scenarioid;
        $params[":messageid"] = $messageid;
        $params[":position"] = $position;
        $params[":pause"] = $pause;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM scenariomessage ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new ScenarioMessage($id, $scenarioid, $messageid, $position, $pause);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'une ScenarioMessage
    */
    public function delete() {		
        $query = "DELETE FROM scenariomessage";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
    
    public static function getNextPositionForScenario($scenarioId){
        $query = "SELECT position FROM scenariomessage ";
        $query .= " WHERE scenarioid=:scenarioid ";
        $query .= " ORDER BY position DESC LIMIT 1";
        
        $params = array(":scenarioid"=>$scenarioId);
        $params[":scenarioid"] = $scenarioId;
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $position = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $position = $row["position"];
        }
        
        return $position++;
    }
}
?>
