<?php

class ListeMessage{
    public $id;
    public $listeid;
    public $deviceid;
    public $position;
    public $messageid;
    
    public function __construct($id, $listeid, $deviceid, $position,$messageid) {
        $this->id = $id;
        $this->listeid = $listeid;
        $this->deviceid = $deviceid;
        $this->position = $position;
        $this->messageid = $messageid;
    }
    
    public static function ListeMessageExists($idListeMessage) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM listemessage";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idListeMessage));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getListeMessage($ids){
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
        $query .= ",listeid";
        $query .= ",deviceid";
        $query .= ",position";
        $query .= ",messageid";
        $query .= " FROM listemessage ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_listemessage = new ListeMessage($row['id'],$row['listeid'],$row['deviceid'],$row["position"],$row["messageid"]);

                    $result[] = $tmp_listemessage;
                    $tmp_listemessage = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les ListeMessages
    */
    public static function getListeMessagesForListe($listeid,$position=TRUE) {
        $query = "SELECT id FROM listemessage";
        $query .= " WHERE listeid=".$listeid;
        $query .= ($position) ? " ORDER BY position": "";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getListeMessage($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createListeMessage($listeid,$deviceid,$position,$messageid=TRUE) {
        
        $query = "INSERT INTO listemessage (";
        $query .= "listeid";
        $query .= ",deviceid";
        $query .= ",position ";
        $query .= ",messageid ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":listeid";
        $query .= ",:deviceid";
        $query .= ",:position ";
        $query .= ",:messageid ";
        $query .= ")";
        
        $params = array();
        $params[":listeid"] = $listeid;
        $params[":deviceid"] = $deviceid;
        $params[":position"] = $position;
        $params[":messageid"] = $messageid;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM listemessage ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new ListeMessage($id, $listeid, $deviceid, $position, $messageid);
        
        return $tmpInstance;
    }
    
    
    public function update() {
        
        $query = "UPDATE listemessage SET";
        $query .= " listeid=:listeid";
        $query .= ", deviceid=:deviceid";
        $query .= ", position=:position";
        $query .= ", messageid=:messageid";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':listeid'] = $this->listeid;
        $params[':deviceid'] = $this->deviceid;
        $params[':position'] = $this->position;
        $params[':messageid'] = $this->messageid;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le listedevice '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }
    
    /**
    * @desc Suppression d'une ListeMessage
    */
    public function delete() {		
        $query = "DELETE FROM listemessage";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
}
?>
