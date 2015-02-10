<?php

class Page{
    public $id;
    public $name;
    public $description;
    public $active;
    public $icon;
    public $position;
    public $parent;
    public $color;
    
    public function __construct($id, $name, $description, $active, $icon, $position, $parent, $color) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->active = $active;
        $this->icon = $icon;
        $this->position = $position;
        $this->parent = $parent;
        $this->color = $color;
    }
    
    public static function PageExists($idPage) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM page";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idPage));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getPage($ids){
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
        $query .= ",active";
        $query .= ",icon";
        $query .= ",position";
        $query .= ",parent";
        $query .= ",color";
        $query .= " FROM page ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_page = new Page($row['id'],$row['name'],$row['description'],$row["active"],$row["icon"],$row["position"],$row["parent"], $row["color"]);

                    $result[] = $tmp_page;
                    $tmp_page = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Pages
    */
    public static function getPages($active=TRUE) {
        $query = "SELECT id FROM page";
        $query .= ($active) ? " WHERE active=1" : "";
        $query .= " ORDER BY position";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getPage($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les Pages parents
    */
    public static function getPageParents() {
        $query = "SELECT DISTINCT(parent) FROM page";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getPage($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les Pages non fille
    */
    public static function getPageNonFilles() {
        $query = "SELECT id FROM page";
        $query .= " WHERE parent IS NULL";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getPage($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les Pages fille
    */
    public static function getPageFilles($idParent) {
        $query = "SELECT id FROM page";
        $query .= " WHERE parent=".$idParent." ";
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getPage($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public function hasFilles(){
        $nbFilles=0;
        $query = "SELECT COUNT(id) as nbFilles FROM page";
        $query .= " WHERE parent=".$this->id." ";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nbFilles = $row["nbFilles"];
        }
        $return = ($nbFilles > 0) ? TRUE : FALSE;
        return $return;
    }
    
    public static function createPage($name,$description,$active=1,$icon,$parent,$color) {
        $position=0;
        if($parent != 0 && $parent != ""){
            $sqlPosition="SELECT TOP 1 position FROM page WHERE parent=".$parent." ORDER BY position DESC";
            //echo $sqlPosition;
            $stmt = $GLOBALS['dbconnec']->prepare($sqlPosition);
            if (!$stmt->execute(array())) {
                if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $position = $row["position"] + 1;
                }
            }
        }
        $parent=($parent == "") ? NULL : $parent;
        $parent=($parent == "NULL") ? NULL : $parent;
        
        $query = "INSERT INTO page (";
        $query .= "name";
        $query .= ",description";
        $query .= ",active ";
        $query .= ",icon ";
        $query .= ",position ";
        $query .= ",parent ";
        $query .= ",color ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:description";
        $query .= ",:active ";
        $query .= ",:icon ";
        $query .= ",:position ";
        $query .= ",:parent ";
        $query .= ",:color ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":description"] = $description;
        $params[":active"] = $active;
        $params[":icon"] = $icon;
        $params[":position"] = $position;
        $params[":parent"] = $parent;
        $params[":color"] = $color;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM page ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Page($id, $name, $description, $active,$icon,$position,$parent,$color);
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'une Page
    */
    public function delete() {		
        $query = "DELETE FROM page";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
}
?>
