<?php
include_once "../tools/config.php";
include_once "../models/Chart.php";
include_once "../models/ChartDevice.php";
include_once "../models/Device.php";

$GLOBALS["dbconnec"]=connectDB();
if(!isset($_POST["chartId"])){
    die('Aucun chart donné');
    exit;
}
if(!isset($_POST["itemId"])){
    die('Aucun item donné');
    exit;
}

$timeout = array('http' => array('timeout' => 10));
$context = stream_context_create($timeout);

$contentCompteur=$contentTeleinfo=$lastIpAddress=$lastType=$output="";
$sql = "SELECT p.name, d.product_id, d.param1, d.ip_address, d.incremental, d.id as deviceId ";
$sql .= " FROM chartdevice cd, device d, product p ";
$sql .= " WHERE cd.deviceid=d.id AND d.product_id=p.id ";
$sql .= " AND cd.chartid=".$_POST["chartId"];
//$sql .= " ORDER BY ";

$resultats=$GLOBALS["dbconnec"]->query($sql);
$resultats->setFetchMode(PDO::FETCH_OBJ);
$i=0;
while( $resultat = $resultats->fetch() ){
    if($resultat->product_id == ""){
        continue;
    }
    if(strtolower(substr($resultat->name,0,4)) != "gce_" && strtolower($resultat->name) != "teleinfo"){
        continue;
    }
    
    if(strtolower($resultat->name) == "teleinfo"){
        if($lastIpAddress != $resultat->ip_address){
            $url = "http://".$resultat->ip_address."/teleinfo.php";
            $lastIpAddress=$resultat->ip_address;
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }elseif($lastType != "teleinfo"){
            $url = "http://".$resultat->ip_address."/teleinfo.php";
            $lastIpAddress=$resultat->ip_address;
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }
        
        $contentTeleinfo = json_decode(str_replace("'",'"', $contentTeleinfo), TRUE);
        $value=$contentTeleinfo[$resultat->param1];
        $lastType = "teleinfo";
    }
    
    //GCE
    if(strtolower($resultat->name) == "gce_compteur"){
        if($lastIpAddress != $resultat->ip_address){
            $url = "http://".$resultat->ip_address."/status.xml";
            $lastIpAddress=$resultat->ip_address;
            $contentCompteur = @file_get_contents($url, false, $context);
        }elseif($lastType != "compteur"){
            $url = "http://".$resultat->ip_address."/status.xml";
            $lastIpAddress=$resultat->ip_address;
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }
        $xml = simplexml_load_file($contentCompteur);
        $param=$resultat->param1;
        $value=$xml->$param;
        $lastType = "compteur";
    }
    if(strtolower($resultat->name) == "gce_teleinfo"){
        if($lastIpAddress != $resultat->ip_address){
            $url = "http://".$resultat->ip_address."/protect/settings/teleinfo1.xml";
            $contentTeleinfo = simplexml_load_file($url);
            $lastIpAddress=$resultat->ip_address;
            //$contentTeleinfo = @file_get_contents($url, false, $context);
        }elseif($lastType != "teleinfo"){
            $url = "http://".$resultat->ip_address."/protect/settings/teleinfo1.xml";
            $contentTeleinfo = simplexml_load_file($url);
            $lastIpAddress=$resultat->ip_address;
            //$contentTeleinfo = @file_get_contents($url, false, $context);
        }
        //$xml = simplexml_load_file($contentTeleinfo);
        $param=$resultat->param1;
        $value=$xml->$param;
        $lastType = "teleinfo";
    }
    
    if($resultat->incremental=="1"){
        $systemid="8".$resultat->deviceId;
        $mode="c";
        $permission=0755;
        $size=10;
        $shmid = shmop_open($systemid, $mode, $permissions, $size);
        $valSize = strlen($value);
        $size = shmop_size($shmid);
        $oldValue = shmop_read($shmid, 0, $valSize);
        shmop_write($shmid, $value, 0);
        shmop_close($shmid);
        if($oldValue == ""){
            $value="";
        }else{
            $value = ($value - $oldValue) * 60 * 6 ;                
        }
    }
    
    if($value != ""){
        //$output .= "";
        //$output .= "var series = chart".$_POST["itemId"].".series[0];";
        $output .= "var shift = chart".$_POST["itemId"].".series[".$i."].data.length > 50;";
        $output .= "var dateTmp=Date.now();";
        $output .= "dateTmp += 3600;";
        $output .= "chart".$_POST["itemId"].".series[".$i."].addPoint([dateTmp, ".intval($value)."],true,shift);";
    }
    $value="";
    $i++;
}    
echo $output;
/*
var series = chart56.series[0];
shift = series.data.length > 20; // shift if the series is longer than 20
// add the point
chart.series[0].addPoint(eval(point), true, shift);
// call it again after one second
setTimeout(requestData, 10000);
 */
?>