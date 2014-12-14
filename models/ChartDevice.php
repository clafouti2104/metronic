<?php

class ChartDevice{
    public $id;
    public $chartid;
    public $deviceid;
    
    public function __construct($id, $chartid, $deviceid) {
        $this->id = $id;
        $this->chartid = $chartid;
        $this->deviceid = $deviceid;
    }
    
    public static function ChartDeviceExists($idChartDevice) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM chartdevice";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idChartDevice));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getChartDevice($ids){
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
        $query .= ",chartid";
        $query .= ",deviceid";
        $query .= " FROM chartdevice ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_chartdevice = new ChartDevice($row['id'],$row['chartid'],$row['deviceid']);

                    $result[] = $tmp_chartdevice;
                    $tmp_chartdevice = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    public static function getChartDeviceForChart($chartid){
        $result = array();

        
        $query = "SELECT ";
        $query .= "id";
        $query .= ",chartid";
        $query .= ",deviceid";
        $query .= " FROM chartdevice ";
        $query .= " WHERE chartid=:chartid";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
            $params = array(":chartid"	=> $chartid);
            $stmt->execute($params);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_chartdevice = new ChartDevice($row['id'],$row['chartid'],$row['deviceid'],$row['chartid']);

                    $result[] = $tmp_chartdevice;
                    $tmp_chartdevice = NULL;
            }
        $stmt = NULL;
        return $result;
    }
    
    /**
    * @desc Renvoie tous les ChartDevices
    */
    public static function getChartDevices() {
        $query = "SELECT id FROM chartdevice";
        $query .= " ORDER BY chartid";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getChartDevice($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createChartDevice($chartid,$deviceid) {
        
        $query = "INSERT INTO chartdevice (";
        $query .= "chartid";
        $query .= ",deviceid";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":chartid";
        $query .= ",:deviceid";
        $query .= ")";
        
        $params = array();
        $params[":chartid"] = $chartid;
        $params[":deviceid"] = $deviceid;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM device ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new ChartDevice($id, $chartid, $deviceid);
        
        return $tmpInstance;
    }
    
    /**
    * @desc Suppression d'une ChartDevice
    */
    public static function deleteForChart($chartid) {		
        $query = "DELETE FROM chartdevice";
        $query .= " WHERE chartid=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $chartid));      
        $stmt=NULL;
    }
    
    
    /**
    * @desc Suppression d'une ChartDevice
    */
    public function delete() {		
        $query = "DELETE FROM chartdevice";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
    }
}
?>
