<?php

class Page{
    public $id;
    public $name;
    public $description;
    public $active;
    public $icon;
    
    public function __construct($id, $name, $description, $active,$icon) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->active = $active;
        $this->icon = $icon;
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
        $query .= " FROM page ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_page = new Page($row['id'],$row['name'],$row['description'],$row["active"],$row["icon"]);

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
        $query .= " ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getPage($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createPage($name,$description,$active=1,$icon) {
        
        $query = "INSERT INTO page (";
        $query .= "name";
        $query .= ",description";
        $query .= ",active ";
        $query .= ",icon ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:description";
        $query .= ",:active ";
        $query .= ",:icon ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":description"] = $description;
        $params[":active"] = $active;
        $params[":icon"] = $icon;
        
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

        $tmpInstance = new Page($id, $name, $description, $active,$icon);
        
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
