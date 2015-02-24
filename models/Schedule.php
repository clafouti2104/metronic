<?php

class Schedule{
    public $id;
    public $name;
    public $description;
    public $weekdays;
    public $hour;
    public $minute;
    
    public $crontabFile="/var/www/crontabFile";
    
    public function __construct($id, $name, $description, $weekdays, $hour, $minute) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->weekdays = $weekdays;
        $this->hour = $hour;
        $this->minute = $minute;
    }
    
    public static function ScheduleExists($idSchedule) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM schedule";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idSchedule));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getSchedule($ids){
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
        $query .= ",weekdays";
        $query .= ",hour";
        $query .= ",minute";
        $query .= " FROM schedule ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_schedule = new Schedule($row['id'],$row['name'],$row['description'],$row["weekdays"],$row["hour"],$row["minute"]);

                    $result[] = $tmp_schedule;
                    $tmp_schedule = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Schedules
    */
    public static function getSchedules() {
        $query = "SELECT id FROM schedule";
        $query .= " ORDER BY minute";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getSchedule($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    

    public static function createSchedule($name,$description,$weekdays,$hour,$minute) {
        $query = "INSERT INTO schedule (";
        $query .= "name";
        $query .= ",description";
        $query .= ",weekdays ";
        $query .= ",hour ";
        $query .= ",minute ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:description";
        $query .= ",:weekdays ";
        $query .= ",:hour ";
        $query .= ",:minute ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":description"] = $description;
        $params[":weekdays"] = $weekdays;
        $params[":hour"] = $hour;
        $params[":minute"] = $minute;
        
        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de créer le Device.");
        }
        $stmt = NULL;
        
        // On récupère l'id de l'élément
        $query = "SELECT id FROM schedule ORDER BY id DESC LIMIT 1";
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array());
        $id = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $stmt = NULL;

        $tmpInstance = new Schedule($id, $name, $description, $weekdays,$hour,$minute);
        
        self::cronGenerateJobs();
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Suppression d'une Schedule
    */
    public function delete() {		
        $query = "DELETE FROM schedule";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
        $this->cronGenerateJobs($this->crontabFile);
    }
    
    public function update() {
        
        $query = "UPDATE schedule SET";
        $query .= " name=:name";
        $query .= ", description=:description";
        $query .= ", weekdays=:weekdays";
        $query .= ", hour=:hour";
        $query .= ", minute=:minute";
        $query .= " WHERE id=:id";
        
        $params = array();
        $params[':id'] = $this->id;
        $params[':name'] = $this->name;
        $params[':description'] = $this->description;
        $params[':weekdays'] = $this->weekdays;
        $params[':hour'] = $this->hour;
        $params[':minute'] = $this->minute;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("ERREUR : Impossible de mettre a jour le messagedevice '".$this->id."'.");
        }
        $stmt = NULL;
        
        $this->cronGenerateJobs($this->crontabFile);

        return TRUE;
    }
    
    public function cronGenerateJobs($filepath="/tmp/crontabFile"){
        //Récupère toutes les planifications
        $schedules = self::getSchedules();
        
        $lines="#\n";
        foreach($schedules as $schedule){
            $cmd="cd /var/www/controllers;php run_schedule_task.php --scheduleid=".$schedule->id." >/dev/null 2>&1";
            $lines.=$schedule->minute." ".$schedule->hour." * * ".$schedule->weekdays." ".$cmd."\n";
        }
        
        file_put_contents($filepath, $lines);
        exec('crontab '.$filepath);
        return TRUE;
    }
}
?>
