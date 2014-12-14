<?php

class Scenario{
    public $id;
    public $name;
    public $description;
    public $color;
    public $size;
    public $icon;
    
    public function __construct($id, $name, $description, $color, $size, $icon) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
        $this->size = $size;
        $this->icon = $icon;
    }
    
    public static function ScenarioExists($idScenario) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM scenario";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idScenario));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getScenario($ids){
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
        $query .= ",color";
        $query .= ",size";
        $query .= ",icon";
        $query .= " FROM scenario ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_scenario = new Scenario($row['id'],$row['name'],$row['description'],$row["color"],$row["size"],$row["icon"]);

                    $result[] = $tmp_scenario;
                    $tmp_scenario = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Scenarios
    */
    public static function getScenarios() {
        $query = "SELECT id FROM scenario";
        //$query .= ($color) ? " WHERE color=1" : "";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getScenario($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createScenario($name,$description,$color,$size,$icon) {
        
        $query = "INSERT INTO scenario (";
        $query .= "name";
        $query .= ",description";
        $query .= ",color ";
        $query .= ",size ";
        $query .= ",icon ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:description";
        $query .= ",:color ";
        $query .= ",:size ";
        $query .= ",:icon ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":description"] = $description;
        $params[":color"] = $color;
        $params[":size"] = $size;
        $params[":icon"] = $icon;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM scenario ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Scenario($id, $name, $description, $color,$size,$icon);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'une Scenario
    */
    public function delete() {		
        $query = "DELETE FROM scenario";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
}
?>
