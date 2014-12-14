<?php

class Tuile{
    public $id;
    public $name;
    public $deviceid;
    public $color;
    
    public function __construct($id, $name, $deviceid, $color) {
        $this->id = $id;
        $this->name = $name;
        $this->deviceid = $deviceid;
        $this->color = $color;
    }
    
    public static function TuileExists($idTuile) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM tuile";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idTuile));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getTuile($ids){
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
        $query .= ",deviceid";
        $query .= ",color";
        $query .= " FROM tuile ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_tuile = new Tuile($row['id'],$row['name'],$row['deviceid'],$row["color"]);

                    $result[] = $tmp_tuile;
                    $tmp_tuile = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Tuiles
    */
    public static function getTuiles() {
        $query = "SELECT id FROM tuile";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getTuile($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createTuile($name,$deviceid,$color=1) {
        
        $query = "INSERT INTO tuile (";
        $query .= "name";
        $query .= ",deviceid";
        $query .= ",color ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:deviceid";
        $query .= ",:color ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":deviceid"] = $deviceid;
        $params[":color"] = $color;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM tuile ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Tuile($id, $name, $deviceid, $color);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'une Tuile
    */
    public function delete() {		
        $query = "DELETE FROM tuile";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
}
?>
