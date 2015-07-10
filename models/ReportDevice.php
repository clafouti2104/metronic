<?php

class ReportDevice{
    public $id;
    public $reportid;
    public $deviceid;
    public $position;
    
    public function __construct($id, $reportid, $deviceid, $position) {
        $this->id = $id;
        $this->reportid = $reportid;
        $this->deviceid = $deviceid;
        $this->position = $position;
    }
    
    public static function ReportDeviceExists($idReportDevice) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM reportdevice";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idReportDevice));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getReportDevice($ids){
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
        $query .= ",reportid";
        $query .= ",deviceid";
        $query .= ",position";
        $query .= " FROM reportdevice ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_reportdevice = new ReportDevice($row['id'],$row['reportid'],$row['deviceid'],$row["position"]);

                    $result[] = $tmp_reportdevice;
                    $tmp_reportdevice = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les ReportDevices
    */
    public static function getReportDevicesForReport($reportid,$position=TRUE) {
        $query = "SELECT id FROM reportdevice";
        $query .= " WHERE reportid=".$reportid;
        $query .= ($position) ? " ORDER BY position": "";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getReportDevice($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createReportDevice($reportid,$deviceid,$position) {
        
        $query = "INSERT INTO reportdevice (";
        $query .= "reportid";
        $query .= ",deviceid";
        $query .= ",position ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":reportid";
        $query .= ",:deviceid";
        $query .= ",:position ";
        $query .= ")";
        
        $params = array();
        $params[":reportid"] = $reportid;
        $params[":deviceid"] = $deviceid;
        $params[":position"] = $position;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM reportdevice ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new ReportDevice($id, $reportid, $deviceid, $position);
        
        return $tmpInstance;
    }
    
    
    public function update() {
        
        $query = "UPDATE reportdevice SET";
        $query .= " reportid=:reportid";
        $query .= ", deviceid=:deviceid";
        $query .= ", position=:position";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':reportid'] = $this->reportid;
        $params[':deviceid'] = $this->deviceid;
        $params[':position'] = $this->position;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le listedevice '".$this->id."'.");
        }
        $stmt = NULL;

        return TRUE;
    }
    
    /**
    * @desc Suppression d'une ReportDevice
    */
    public function delete() {		
        $query = "DELETE FROM reportdevice";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
}
?>
