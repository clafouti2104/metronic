<?php
include_once "Alert.php";
include_once "Cond.php";
include_once "Condition.php";
include_once "CondAction.php";
include_once "Log.php";
include_once "Product.php";
include_once "/var/www/metronic/tools/action.php";

class Device{
    public $id;
    public $name;
    public $type;
    public $state;
    public $states;
    public $last_update;
    public $ip_address;
    public $model;
    public $active;
    public $parameters;
    public $alert_lost_communication;
    public $last_alert;
    public $product_id;
    public $param1;
    public $param2;
    public $param3;
    public $param4;
    public $param5;
    public $collect;
    public $incremental;
    public $unite;
    public $data_type;
    public $state_parameters;
    public $state_results;
    public $chart_formula;
    
    public function __construct($id, $name, $type, $state, $states, $last_update, $ip_address,$model,$active,$parameters,$alert_lost_communication,$last_alert,$product_id,$param1,$param2,$param3,$param4,$param5,$collect,$incremental,$unite, $data_type, $state_parameters, $state_results, $chart_formula) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->state = $state;
        $this->states = $states;
        $this->last_update = $last_update;
        $this->ip_address = $ip_address;
        $this->model = $model;
        $this->active = $active;
        $this->parameters = $parameters;
        $this->alert_lost_communication = $alert_lost_communication;
        $this->last_alert = $last_alert;
        $this->product_id = $product_id;
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
        $this->param4 = $param4;
        $this->param5 = $param5;
        $this->collect = $collect;
        $this->incremental = $incremental;
        $this->unite = $unite;
        $this->data_type = $data_type;
        $this->state_parameters = $state_parameters;
        $this->state_results = $state_results;
        $this->chart_formula = $chart_formula;
    }
    
    private static $types = array(
            'door',
            'light',
            'eau',
            'electricy',
            'music',
            'presence',
            'raspberry',
            'sensor',
            'sensor_humidity',
            'tv',
            'website'
    );
    
    private static $models = array(
            'ds18b20',
            'oregon',
            'myfox_light',
            'myfox_alarm',
            'freebox',
            'chacon'
    );
    
    private static $data_types = array(
            '0'=>'',
            '1'=>'Nombre 0 Décimale',
            '2'=>'Nombre 1 Décimale',
            '3'=>'Nombre 2 Décimales',
            '4'=>'Texte'
    );
    
    public static function getTypes() {
        return self::$types;
    }
    
    public static function getModels() {
        return self::$models;
    }
    
    public static function getDataTypes() {
        return self::$data_types;
    }
    
    public static function DeviceExists($idDevice) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM device";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idDevice));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getDevice($ids){
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
        $query .= ",type";
        $query .= ",state";
        $query .= ",states";
        $query .= ",last_update";
        $query .= ",ip_address";
        $query .= ", model ";
        $query .= ", active ";
        $query .= ", parameters ";
        $query .= ", alert_lost_communication ";
        $query .= ", last_alert ";
        $query .= ", product_id ";
        $query .= ", param1 ";
        $query .= ", param2 ";
        $query .= ", param3 ";
        $query .= ", param4 ";
        $query .= ", param5 ";
        $query .= ", collect ";
        $query .= ", incremental ";
        $query .= ", unite ";
        $query .= ", data_type ";
        $query .= ", state_parameters ";
        $query .= ", state_results ";
        $query .= ", chart_formula ";
        $query .= " FROM device ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_device = new Device($row['id'],$row['name'],$row['type'], $row['state'], $row['states'], $row['last_update'], $row['ip_address'], $row['model'], $row['active'],$row["parameters"], $row["alert_lost_communication"], $row["last_alert"],$row["product_id"],$row["param1"],$row["param2"],$row["param3"],$row["param4"],$row["param5"],$row["collect"],$row["incremental"], $row["unite"], $row["data_type"], $row["state_parameters"], $row["state_results"], $row["chart_formula"]);

                    $result[] = $tmp_device;
                    $tmp_device = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Devices
    */
    public static function getDevices() {
        $query = "SELECT id FROM device ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getDevice($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les Devices incremental
    */
    public static function getDevicesIncremental() {
        $query = "SELECT id FROM device WHERE incremental=1 ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getDevice($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    /**
    * @desc Renvoie tous les Devices
    */
    public static function getDevicesByType() {
        $query = "SELECT id FROM device ORDER BY type";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getDevice($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
    
    public static function createDevice($name,$type,$state,$states,$last_update=NULL,$ip_address,$model,$active=1,$parameters,$alert_lost_communication, $last_alert,$product_id,$param1,$param2,$param3,$param4,$param5, $collect, $incremental,$unite,$data_type,$state_parameters=NULL, $state_results=NULL, $chart_formula=NULL) {
        
        $query = "INSERT INTO device (";
        $query .= "name";
        $query .= ",type";
        $query .= ",state";
        $query .= ",states";
        $query .= ",last_update";
        $query .= ",ip_address";
        $query .= ",model ";
        $query .= ",active ";
        $query .= ",parameters ";
        $query .= ",alert_lost_communication ";
        $query .= ",last_alert ";
        $query .= ",product_id ";
        $query .= ",param1 ";
        $query .= ",param2 ";
        $query .= ",param3 ";
        $query .= ",param4 ";
        $query .= ",param5 ";
        $query .= ",collect ";
        $query .= ",incremental ";
        $query .= ",unite ";
        $query .= ",data_type ";
        $query .= ",state_parameters ";
        $query .= ",state_results ";
        $query .= ",chart_formula ";
        $query .= ") ";
        $query .= " VALUES (";
        $query .= ":name";
        $query .= ",:type";
        $query .= ",:state";
        $query .= ",:states";
        $query .= ",:last_update";
        $query .= ",:ip_address";
        $query .= ",:model ";
        $query .= ",:active ";
        $query .= ",:parameters ";
        $query .= ",:alert_lost_communication ";
        $query .= ",:last_alert ";
        $query .= ",:product_id ";
        $query .= ",:param1 ";
        $query .= ",:param2 ";
        $query .= ",:param3 ";
        $query .= ",:param4 ";
        $query .= ",:param5 ";
        $query .= ",:collect ";
        $query .= ",:incremental ";
        $query .= ",:unite ";
        $query .= ",:data_type ";
        $query .= ",:state_parameters ";
        $query .= ",:state_results ";
        $query .= ",:chart_formula ";
        $query .= ")";
        
        $params = array();
        $params[":name"] = $name;
        $params[":type"] = $type;
        $params[":state"] = $state;
        $params[":states"] = $states;
        $params[":last_update"] = $last_update;
        $params[":ip_address"] = $ip_address;
        $params[":model"] = $model;
        $params[":active"] = $active;
        $params[":parameters"] = $parameters;
        $params[":alert_lost_communication"] = $alert_lost_communication;
        $params[":last_alert"] = NULL;
        $params[":product_id"] = $product_id;
        $params[":param1"] = $param1;
        $params[":param2"] = $param2;
        $params[":param3"] = $param3;
        $params[":param4"] = $param4;
        $params[":param5"] = $param5;
        $params[":collect"] = $collect;
        $params[":incremental"] = $incremental;
        $params[":unite"] = $unite;
        $params[":data_type"] = $data_type;
        $params[":state_parameters"] = $state_parameters;
        $params[":state_results"] = $state_results;
        $params[":chart_formula"] = $chart_formula;

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
                throw new Exception("$query ERREUR : Impossible de créer le Device.");
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

        $tmpInstance = new Device($id, $name, $type, $state, $states, $last_update, $ip_address, $model, $active, $parameters, $alert_lost_communication, $last_alert, $product_id,$param1,$param2,$param3,$param4,$param5,$collect,$incremental,$unite,$data_type,$state_parameters,$state_results, $chart_formula);

        //Création des messages device si un product est renseigné
        if($product_id != "" && $id != 0){
            //Récupération du product
            $product = Product::getProduct($product_id);
            if(is_object($product)){
                switch(strtolower($product->name)){
                    case 'calaos_input':
                        $msgOn=MessageDevice::createMessageDevice($id, "on", 0, NULL, NULL, "on", 1,NULL,"on" );
                        $msgOff=MessageDevice::createMessageDevice($id, "off", 0, NULL, NULL, "off", 1, NULL, "off");
                        break;
                    case 'calaos_output':
                        $msgOn=MessageDevice::createMessageDevice($id, "on", 0, NULL, NULL, "on", 1,NULL,"on" );
                        $msgOff=MessageDevice::createMessageDevice($id, "off", 0, NULL, NULL, "off", 1, NULL, "off");
                        break;
                    case 'freebox':
                        $cmds = array();
                        $cmds["power"]="power";
                        $cmds["enter"]="ok";
                        $cmds["home"]="home";
                        $cmds["mute"]="mute";
                        $cmds["vol +"]="vol_inc";
                        $cmds["vol -"]="vol_dec";
                        $cmds["prog +"]="prog_inc";
                        $cmds["prog -"]="prog_dec";
                        foreach($cmds as $name=>$cmd){
                            $msg=MessageDevice::createMessageDevice($id, $name, 0, NULL, NULL, NULL, 1,NULL,$cmd);
                        }
                        break;
                    case 'myfox_alarm':
                        $msgArmed=MessageDevice::createMessageDevice($id, "MES", 0, NULL, NULL, NULL, 1, NULL, "armed");
                        $msgDisarmed=MessageDevice::createMessageDevice($id, "MHS", 0, NULL, NULL, NULL, 1, NULL,"disarmed");
                        $msgPartial=MessageDevice::createMessageDevice($id, "MES Partielle", 0, NULL, NULL, NULL, 1,NULL,"partial");
                        break;
                    case 'myfox_group':
                        $msgOn=MessageDevice::createMessageDevice($id, "on", 0, NULL, NULL, "on", 1,NULL,"on");
                        $msgOff=MessageDevice::createMessageDevice($id, "off", 0, NULL, NULL, "off", 1, NULL, "off");
                        break;
                    case 'myfox_light':
                        $msgOn=MessageDevice::createMessageDevice($id, "on", 0, NULL, NULL, "on", 1,NULL,"on");
                        $msgOff=MessageDevice::createMessageDevice($id, "off", 0, NULL, NULL, "off", 1, NULL, "off");
                        break;
                    case 'netatmo_meteo_temperature_int':
                        $sqlUpdate="UPDATE device SET param1='temperature' AND model='interieur' WHERE id=".$id;
                        $stmt = $GLOBALS['dbconnec']->prepare($sqlUpdate);
                        $stmt->execute(array());
                        break;
                    case 'netatmo_meteo_temperature_ext':
                        $sqlUpdate="UPDATE device SET param1='temperature' AND model='exterieur' WHERE id=".$id;
                        $stmt = $GLOBALS['dbconnec']->prepare($sqlUpdate);
                        $stmt->execute(array());
                        break;
                    case 'netatmo_meteo_humidite_ext':
                        $sqlUpdate="UPDATE device SET param1='humidite' AND model='exterieur' WHERE id=".$id;
                        $stmt = $GLOBALS['dbconnec']->prepare($sqlUpdate);
                        $stmt->execute(array());
                        break;
                    case 'popcorn':
                        $msg=MessageDevice::createMessageDevice($id, "power", 0, NULL, NULL, NULL, 1,NULL,"power");
                        $msgOff=MessageDevice::createMessageDevice($id, "home", 0, NULL, NULL, NULL, 1, NULL, "home");
                        $msgOff=MessageDevice::createMessageDevice($id, "vol +", 0, NULL, NULL, NULL, 1, NULL, "volup");
                        $msgOff=MessageDevice::createMessageDevice($id, "vol -", 0, NULL, NULL, NULL, 1, NULL, "voldown");
                        $msgOff=MessageDevice::createMessageDevice($id, "mute", 0, NULL, NULL, NULL, 1, NULL, "mut");
                        break;
                    case 'zibase_actuator':
                        $msgOn=MessageDevice::createMessageDevice($id, "on", 0, NULL, NULL, "on", 1,NULL,"on");
                        $msgOff=MessageDevice::createMessageDevice($id, "off", 0, NULL, NULL, "off", 1, NULL, "off");
                        break;
                    case 'zibase_scenario':
                        $msgPlay=MessageDevice::createMessageDevice($id, "play", 0, NULL, NULL, "on", 1,NULL,"on");
                        break;
                    default:
                }
            }
        }
        
        return $tmpInstance;
    }
    
    
    /**
    * @desc Met à jour le status du device
    */
    public static function updateState($id, $state, $last_update="NOW()") {
        $device=self::getDevice($id);
        if($device->state_parameters != ""){
            $deviceState = self::decodeState($state, $device->state_parameters, $device->state_results);
        } elseif($device->state_results != ""){
            $deviceState = self::decodeState($state, $device->state_parameters, $device->state_results);
        }
        if(isset($deviceState)){
            $state = $deviceState;
        }
        
        $last_update = ($last_update == "NOW()") ? "NOW()" : "'".$last_update."'";
        $query = "UPDATE device SET";
        $query .= " state='".$state."'";
        $query .= ", last_update=$last_update";
        $query .= " WHERE id=$id";
        
        $params = array();

        $stmt = $GLOBALS['dbconnec']->prepare($query);
        if (!$stmt->execute($params)) {
            throw new Exception("ERREUR : Impossible de mettre a jour le device '".$id."'.");
        }
        $stmt = NULL;
        
        //Vérifie si scénario conditionnel associé
        self::checkScenarioConditionnel($id);
        
        //Vérifie si alerte associée
        self::checkAlert($id);
        
        return TRUE;
    }
    
    
    
    /**
     *  Vérification des scénario conditionnel
     */
    function checkScenarioConditionnel($id){
        $now = new DateTime('now');
        //Récupère les alertes associés au device
        $conds = Cond::getCondsByDevice($id);
        
        //Pas de scenarios
        if(count($conds) == 0){
            return true;
        }
        
        //Parcours des scenarios
        foreach($conds as $cond){
            $conditions = Condition::getConditionForCond($cond->id);
            $condActions = CondAction::getCondActionForCond($cond->id);
            
            if(count($conditions) == 0){
                continue;
            }
            
            if(count($condActions) == 0){
                continue;
            }
            
            $check=true;
            foreach($conditions as $condition){
                $state="";
                //Récupération du status
                if(strtolower($condition->type) != "device" && strtolower($condition->type) != "hour" && strtolower($condition->type) != "variable"){
                    continue;
                }
                
                if(strtolower($condition->type) == "device"){
                    $deviceTmp=Device::getDevice($condition->objectId);
                    $state=$deviceTmp->state;
                }elseif(strtolower($condition->type) == "variable"){
                    $sqlVariable = "SELECT comment FROM config ";
                    $sqlVariable .= " WHERE name ='variable' AND id=".$condition->objectId;
                    $stmt = $GLOBALS["dbconnec"]->prepare($sqlVariable);
                    $stmt->execute(array());
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $state =$row["comment"];
                    }
                }elseif(strtolower($condition->type) == "hour"){
                    //Gestion Horaire
                    if($condition->value == ""){
                        continue;
                    }
                    $json=json_decode($condition->value);
                    if(!isset($json->days)){
                        continue;
                    }
                    //Vérification Jour
                    $acceptedDays=explode(",",$condition->value);
                    if(!in_array(date('N'),$acceptedDays)){
                        $check=false;
                        continue;
                    }
                    
                    if(!isset($json->beginHour) || !isset($json->beginMinute) || !isset($json->endHour) || !isset($json->endMinute)){
                        continue;
                    }
                    //Verification Heure
                    $currentDate=date('G')*60+date('i');
                    $beginHour=intval($json->beginHour)*60+intval($json->beginMinute);
                    $endHour=intval($json->endHour)*60+intval($json->endMinute);
                    if($currentDate < $beginHour){
                        $check=false;
                    }
                    if($currentDate > $endHour){
                        $check=false;
                    }
                }
                
                if($state==""){
                    continue;
                }
                
                switch(strtolower($condition->operator)){
                    case "<":
                        if($state >= $condition->value){
                            $check = false;
                        }
                        break;
                    case ">":
                        if($state <= $condition->value){
                            $check = false;
                        }
                        break;
                    case "=":
                        if(strtolower($state) != strtolower($condition->value)){
                            $check = false;
                        }
                        break;
                    case "!=":
                        if(strtolower($state) == strtolower($condition->value)){
                            $check = false;
                        }
                        break;
                    default:
                }
                
                if(!$check){
                    break;
                }
            } //End foreach conditions
            
            if(!$check){
                continue;
            }
            
            //Parcours des actions
            foreach($condActions as $condAction){
                $sqlVariable = "";
                
                switch (strtolower($condAction->type)){
                    case 'action_message':
                        executeMessage($condAction->action);
                        break;
                    case 'action_scenario':
                        executeScenario($condAction->action);
                        break;
                    case 'action_variable':
                        switch(strtolower($condAction->more)){
                            case 'inc':
                                $sqlVariable="UPDATE config SET comment=comment+".$condAction->value." WHERE id=".$condAction->action.";";
                                break;
                            case 'dec':
                                $sqlVariable="UPDATE config SET comment=comment-".$condAction->value." WHERE id=".$condAction->action.";";
                                break;
                            case 'set':
                                $sqlVariable="UPDATE config SET comment='".$condAction->value."' WHERE id=".$condAction->action.";";
                                break;
                        }
                        if(isset($sqlVariable) && $sqlVariable != ""){
                            $stmt = $GLOBALS["dbconnec"]->query($sqlVariable);
                        }
                        break;
                    case 'notification':
                        if(isset($condAction->action)){
                            $ch = curl_init('http://api.pushingbox.com/pushingbox?devid='.$condAction->action);
                            file_put_contents("/tmp/info", "PUSHING BOX = http://api.pushingbox.com/pushingbox?devid=".$condAction->action);
                            curl_exec ($ch);
                            curl_close ($ch);
                        }
                        break;
                    case 'commandline':
                        if(isset($condAction->action)){
                            $ch = exec($condAction->action);
                        }
                        break;
                    default:
                }
            }
            $log = Log::createLog("scenario_conditionnel", $cond->name, $now->format('Y-m-d H:i:s'), $cond->id, 40);
        }
    }
    
    /**
     *  Vérification des alertes
     */
    function checkAlert($id){
        $now = new DateTime('now');
        //Récupère les alertes associés au device
        $alerts = Alert::getAlertsByDevice($id);
        if(count($alerts) == 0){
            return TRUE;
        }
        foreach($alerts as $alert){
            $check=false;
            switch(strtolower($alert->operator)){
                case "<":
                    if($state < $alert->value){
                        $check = true;
                    }
                    break;
                case ">":
                    if($state > $alert->value){
                        $check = true;
                    }
                    break;
                case "=":
                    if(strtolower($state) == strtolower($alert->value)){
                        $check = true;
                    }
                    break;
                default:
            }
            if(!$check){
                $alert->sent = 0;
                $alert->last_sent = $now->format('Y-m-d H:i:s');
                $alert->update();
                continue;
            }
            if($alert->notificationId == ""){
                continue;
            }
            
            
            $toSend=true;
            if($alert->sent){
                if(is_null($alert->last_sent)){
                   $toSend=true; 
                }else{
                    $lastSent=new DateTime($alert->last_sent);
                    //LastSent > 1 jour
                    if(($now->getTimestamp() - $lastSent->getTimestamp()) > 86400){
                        $lastSent=true;
                    }
                }
            }
            
            if($toSend){
                $sqlNotifications = "SELECT * FROM config WHERE name='pushing_box' AND id=".$alert->notificationId;
                $stmt = $GLOBALS["dbconnec"]->prepare($sqlNotifications);
                $stmt->execute(array());
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $notificationId = $row["value"];
                }
                
                if(isset($notificationId)){
                    $ch = curl_init('http://api.pushingbox.com/pushingbox?devid='.$notificationId);
                    file_put_contents("/tmp/info", "PUSHING BOX = http://api.pushingbox.com/pushingbox?devid=".$notificationId);
                    curl_exec ($ch);
                    curl_close ($ch);

                    $alert->sent = 1;
                    $alert->last_sent = $now->format('Y-m-d H:i:s');
                    $alert->update();

                    $log = Log::createLog("Alerte déclenchée", $state, $now->format('Y-m-d H:i:s'), $id, 40);
                }
            }
        }
        return true;
    }
    
    /**
    * @desc Suppression d'une Device
    */
    public function delete() {		
        $query = "DELETE FROM device";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        $stmt->execute(array(":id" => $this->id));      
        $stmt=NULL;
        
        return TRUE;
    }
    
    public function showState($unite=TRUE){
        $state = self::showStateGeneric($this->state, $this->data_type, $this->unite,$unite);
        return $state;
    }
    
    public static function showStateGeneric($state, $data_type, $unite, $displayUnite=TRUE){
        $state = $state;
        switch($data_type){
            case '1':
                $state = number_format($state, 0, ',', ' ' );
                break;
            case '2':
                $state = number_format($state, 1, ',', ' ' );
                break;
            case '3':
                $state = number_format($state, 2, ',', ' ' );
                break;
            default:
        }
        if($unite != "" && $displayUnite){
            $state .= " ".$unite;
        }
        return utf8_decode($state);
    }
    
    public function getHistorique($from=0, $limit=20){
        $result = array();
        
        $sql="SELECT date, value ";
        $sql.=" FROM temperature ";
        $sql.=" WHERE deviceid=:deviceid ";
        $sql.=" ORDER BY date DESC ";
        $sql.=" LIMIT $from, $limit";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($sql);
        $params = array(":deviceid"	=> $this->id);
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = array(
                "date"=>$row["date"]
                ,"value"=>$row["value"]
                );
        }
        
        if(count($result) == 0){
            $sql="SELECT date, value ";
            $sql.=" FROM log ";
            $sql.=" WHERE deviceId=:deviceid ";
            $sql.=" ORDER BY date DESC ";
            $sql.=" LIMIT $from, $limit";
            $stmt = $GLOBALS["dbconnec"]->prepare($sql);
            $params = array(":deviceid"	=> $this->id);
            $stmt->execute($params);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = array(
                    "date"=>$row["date"]
                    ,"value"=>$row["value"]
                    );
            }
        }
        
        return $result;
    }
    
    public function getConsommation($from=0, $limit=20){
        $result = array();
        
        $sql="SELECT date, value ";
        $sql.=" FROM releve_".$this->id." ";
        //$sql.=" WHERE ";
        $sql.=" ORDER BY date DESC ";
        $sql.=" LIMIT $from, $limit";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($sql);
        $params = array();
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = array(
                "date"=>$row["date"]
                ,"value"=>$row["value"]
                );
        }
        
        return $result;
    }
    
    /*
     * Décode l'état brute d'un device en fonction des paramètres et tableau de renvoi
     */
    public static function decodeState($rawState,$stateParameters, $stateResults){
        $state = $rawState;
        $stateParameters = ($stateParameters != "") ? json_decode($stateParameters) : $stateParameters;
        $stateResults = ($stateResults != "") ? json_decode($stateResults,TRUE) : $stateResults;
        
        $entered=false;
        if($stateParameters != "" && count($stateParameters) > 0){
            if(isset($stateParameters->formula)){
                $entered=true;
                $fonction = str_replace("x", $state, $stateParameters->formula);
                @eval('$stateTemp='.$fonction.';');
                if(isset($stateTemp)){
                    $state = round($stateTemp, 1);
                }
            }
        }
        if(!$entered){
            if(count($stateResults) > 0){
                foreach($stateResults as $rawStateTmp=>$affectStateTmp){
                    if(strtolower($rawStateTmp) == strtolower($state)){
                        $state=strtolower($affectStateTmp);
                    }
                }
            }
        }
        
        return $state;
    }
    
    public function printDeviceName(){
        $type=($this->type != "") ? " - ".$this->type : "";
        return $this->name.$type;
    }
}
?>
