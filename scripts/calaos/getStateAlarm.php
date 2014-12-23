<?php
require('../../tools/config.php');
require('../../models/Device.php');
$db = connectDB();

$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
$content = "[parameters]";
$login=$password=$ipAddress="";
foreach($ini as $title => $value){
    if($title == "calaos_login"){
        $login=$value;
    }
    if($title == "calaos_password"){
        $password=$value;
    }
    if($title == "calaos_ip_address"){
        $ipAddress=$value;
    }
}

$elems=$outputs=$domokine=array();

$inputs=$outputsL="";
foreach($elems as $calaosId=>$metronicId){
    $inputs .= ($inputs == "") ? "" : ",";
    $inputs .= '"'.$calaosId.'"';
}

foreach($outputs as $calaosId=>$metronicId){
    $outputsL .= ($outputsL == "") ? "" : ",";
    $outputsL .= '"'.$calaosId.'"';
}

$inputs = '"intern_4","intern_5","intern_6"';
//Construction query JSON
$json='{';
$json.='"cn_user": "'.$login.'",';
$json.='"cn_pass": "'.$password.'",';
$json.='"action": "get_state",';
$json.='"inputs": ['.$inputs.']';
$json.='}';

file_put_contents("/var/www/metronic/scripts/calaos/state_alarm.json", $json);

//RECUPERATION INFO CALAOS
exec('wget --no-check-certificate --post-file /var/www/metronic/scripts/calaos/state_alarm.json --output-document /var/www/metronic/scripts/calaos/result_alarm.json https://'.$ipAddress.'/api.php',$response);
$results = file_get_contents('/var/www/metronic/scripts/calaos/result_alarm.json');
$results = json_decode($results,TRUE);

return $results;
/*
foreach($results as $type=>$result){
        if(count($result)==0){
            continue;
        }
        foreach($result as $calaosId => $value){
            //$sql .= "INSERT INTO temperature(name, date, value, deviceid, calaosid) VALUES ('".$calaos[$calaosId]."', NOW(), '".$value."',".$elems[$calaosId].",NULL );";
            $idDevice = (isset($elems[$calaosId])) ? $elems[$calaosId] : $outputs[$calaosId];
            
            if(isset($domokine[$calaosId]["state_parameters"]) || $domokine[$calaosId]["state_results"]){
                $value=Device::decodeState($value, $domokine[$calaosId]["state_parameters"], $domokine[$calaosId]["state_results"]);
            }
            $sql .= "UPDATE device SET state='".$value."', last_update=NOW() WHERE id=".$idDevice.";";
        }
}*/
?>