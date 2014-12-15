<?php
require('../../tools/config.php');
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

$elems=$outputs=array();

//Récupération des devices actifs de type raspberry
$sql = "SELECT d.id, d.name as dname, d.param1,p.name as pname ";
$sql .= " FROM device d,product p ";
$sql .= " WHERE p.name LIKE 'calaos%' ";
$sql .= " AND d.active=1";
$sql .= " AND d.product_id=p.id";
//echo $sql;
$stmt = $db->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch()){
    if($row["pname"] == "calaos_output"){
        $outputs[$row["param1"]] = $row["id"];
    }else{
        $elems[$row["param1"]] = $row["id"];
    }
}
if(count($elems) == 0 && count($outputs) == 0){
    exit;
}
$inputs=$outputsL="";
foreach($elems as $calaosId=>$metronicId){
    $inputs .= ($inputs == "") ? "" : ",";
    $inputs .= '"'.$calaosId.'"';
}

foreach($outputs as $calaosId=>$metronicId){
    $outputsL .= ($outputsL == "") ? "" : ",";
    $outputsL .= '"'.$calaosId.'"';
}

//Construction query JSON
$json='{';
$json.='"cn_user": "'.$login.'",';
$json.='"cn_pass": "'.$password.'",';
$json.='"action": "get_state",';
$json.='"inputs": ['.$inputs.'],';
$json.='"outputs": ['.$outputsL.']';
$json.='}';

file_put_contents("/var/www/metronic/scripts/calaos/state.json", $json);

//RECUPERATION INFO CALAOS
exec('wget --no-check-certificate --post-file /var/www/metronic/scripts/calaos/state.json --output-document /var/www/metronic/scripts/calaos/result.json https://'.$ipAddress.'/api.php',$response);
$results = file_get_contents('/var/www/metronic/scripts/calaos/result.json');
$results = json_decode($results,TRUE);
print_r($response);
$sql="";
foreach($results as $type=>$result){
            //print_r($result);
        if(count($result)==0){
            continue;
        }
        foreach($result as $calaosId => $value){
            //$sql .= "INSERT INTO temperature(name, date, value, deviceid, calaosid) VALUES ('".$calaos[$calaosId]."', NOW(), '".$value."',".$elems[$calaosId].",NULL );";
            $idDevice = (isset($elems[$calaosId])) ? $elems[$calaosId] : $outputs[$calaosId];
            if($value=="false"){$value="off";}
            if($value=="true"){$value="on";}
            if($value=="0"){$value="off";}
            if($value=="1"){$value="on";}
            
            $sql .= "UPDATE device SET state='".$value."', last_update=NOW() WHERE id=".$idDevice.";";
        }
}
echo $sql;
$db->query($sql);
?>