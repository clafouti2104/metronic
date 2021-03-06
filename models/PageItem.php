<?php

class PageItem{
    public $id;
    public $pageId;
    public $position;
    public $tuileId;
    public $scenarioId;
    public $chartId;
    public $listeId;
    public $params;
    public $deviceId;
    public $width;
    public $height;
    public $positiony;
    
    public function __construct($id, $pageId, $position, $tuileId, $scenarioId, $chartId, $listeId, $params,$deviceId, $width, $height, $positiony) {
        $this->id = $id;
        $this->pageId = $pageId;
        $this->position = $position;
        $this->tuileId = $tuileId;
        $this->scenarioId = $scenarioId;
        $this->chartId = $chartId;
        $this->listeId = $listeId;
        $this->params = $params;
        $this->deviceId = $deviceId;
        $this->width = $width;
        $this->height = $height;
        $this->positiony = $positiony;
    }
        
    public static function PageItemExists($idPageItem) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM pageitem";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idPageItem));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
        
    public static function PageItemExistsForPage($pageid, $tuileid=null, $scenarioid=null, $chartid=null, $listeid=null,$deviceId=null) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM pageitem";
            $query .= " WHERE pageId=:pageId";
            if(!is_null($tuileid)){
                $query .= " AND tuileId=:tuileId";
            } elseif(!is_null($scenarioid)){
                $query .= " AND scenarioId=:scenarioId";
            } elseif(!is_null($chartid)){
                $query .= " AND chartId=:chartId";
            } elseif(!is_null($listeid)){
                $query .= " AND listeId=:listeId";
            } elseif(!is_null($deviceId)){
                $query .= " AND deviceId=:deviceId";
            }

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $params = array();
            $params[":pageId"]=$pageid;
            if(!is_null($tuileid)){
                $params[":tuileId"]=$tuileid;
            } elseif(!is_null($scenarioid)){
                $params[":scenarioId"]=$scenarioid;
            } elseif(!is_null($chartid)){
                $params[":chartId"]=$chartid;
            } elseif(!is_null($listeid)){
                $params[":listeId"]=$listeid;
            } elseif(!is_null($deviceId)){
                $params[":deviceId"]=$deviceId;
            }
            
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getPageItem($ids){
        $result = array();
        
        // On vérifie le params d'arguement passé en paramètre
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
        $query .= ",pageId";
        $query .= ",position";
        $query .= ",tuileId";
        $query .= ",scenarioId";
        $query .= ",chartId";
        $query .= ",listeId";
        $query .= ",params";
        $query .= ",deviceId";
        $query .= ",width";
        $query .= ",height";
        $query .= ",positiony";
        $query .= " FROM pageitem ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_pageitem = new PageItem($row['id'],$row['pageId'],$row['position'], $row['tuileId'], $row['scenarioId'], $row['chartId'], $row["listeId"],$row['params'], $row["deviceId"], $row['width'], $row['height'], $row['positiony']);

                    $result[] = $tmp_pageitem;
                    $tmp_pageitem = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les PageItems
    */
    public static function getPageItemsForPage($pageId) {
        $query = "SELECT id FROM pageitem WHERE pageId=".$pageId." ORDER BY position";
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getPageItem($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createPageItem($pageId, $position, $tuileId, $scenarioId, $chartId, $listeId, $paramsI, $deviceId, $width, $height, $positiony) {
        
        $query = "INSERT INTO pageitem (";
        $query .= "pageId";
        $query .= ",position";
        $query .= ",tuileId";
        $query .= ",scenarioId";
        $query .= ",chartId";
        $query .= ",listeId";
        $query .= ",params";
        $query .= ",deviceId";
        $query .= ",width";
        $query .= ",height";
        $query .= ",positiony";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":pageId";
        $query .= ",:position";
        $query .= ",:tuileId";
        $query .= ",:scenarioId";
        $query .= ",:chartId";
        $query .= ",:listeId";
        $query .= ",:params";
        $query .= ",:deviceId";
        $query .= ",:width";
        $query .= ",:height";
        $query .= ",:positiony";
        $query .= ")";
        
        $params = array();
        $params[":pageId"] = $pageId;
        $params[":position"] = $position;
        $params[":tuileId"] = $tuileId;
        $params[":scenarioId"] = $scenarioId;
        $params[":chartId"] = $chartId;
        $params[":listeId"] = $listeId;
        $params[":params"] = $paramsI;
        $params[":deviceId"] = $deviceId;
        $params[":width"] = $width;
        $params[":height"] = $height;
        $params[":positiony"] = $positiony;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le PageItem.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT TOP 1 id FROM pageitem ORDER BY id DESC";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new PageItem($id, $pageId, $position, $tuileId, $scenarioId, $chartId, $listeId, $params,$deviceId,$width, $height, $positiony);

        return $tmpInstance;
    }
        
    public static function getNextPositionForPage($pageId) {
            $query = "SELECT TOP 1 positiony ";
            $query .= " FROM pageitem";
            $query .= " WHERE pageId=:pageId";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":pageId" => $pageId));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;
            $position=$row["positiony"];
            $position++;
            
            return $position;
    }
    
    /**
    * @desc Suppression d'un PageItem
    */
    public function delete() {		
        $query = "DELETE FROM pageitem";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
    
    public function update() {
        
        $query = "UPDATE pageitem SET";
        $query .= " pageId=:pageId";
        $query .= ", position=:position";
        $query .= ", tuileId=:tuileId";
        $query .= ", scenarioId=:scenarioId";
        $query .= ", chartId=:chartId";
        $query .= ", listeId=:listeId";
        $query .= ", params=:params";
        $query .= ", deviceId=:deviceId";
        $query .= ", width=:width";
        $query .= ", height=:height";
        $query .= ", positiony=:positiony";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[":pageId"] = $this->pageId;
        $params[":position"] = $this->position;
        $params[":tuileId"] = $this->tuileId;
        $params[":scenarioId"] = $this->scenarioId;
        $params[":chartId"] = $this->chartId;
        $params[":listeId"] = $this->listeId;
        $params[":params"] = $this->params;
        $params[":deviceId"] = $this->deviceId;
        $params[":width"] = $this->width;
        $params[":height"] = $this->height;
        $params[":positiony"] = $this->positiony;
        $params[":id"] = $this->id;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le messagedevice '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }
}
?>
