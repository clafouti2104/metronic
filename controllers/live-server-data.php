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
$chartDevices=  ChartDevice::getChartDeviceForChart($_POST["chartId"]);
$i=0;
foreach($chartDevices as $chartDevice){
    $device=Device::getDevice($chartDevice->deviceid);
    if($device->product_id == ""){
        continue;
    }
    $product=Product::getProduct($device->product_id);
    if(strtolower(substr($product->name,0,4)) != "gce_" && strtolower($product->name) != "teleinfo"){
        continue;
    }
    
    if(strtolower($product->name) == "teleinfo"){
        if($lastIpAddress != $device->ip_address){
            $url = "http://192.168.1.14/teleinfo.php";
            $lastIpAddress="192.168.1.14";
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }elseif($lastType != "teleinfo"){
            $url = "http://192.168.1.14/teleinfo.php";
            $lastIpAddress="192.168.1.14";
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }
        $contentTeleinfo = json_decode(str_replace("'",'"', $contentTeleinfo), TRUE);
        //$xml = simplexml_load_file($contentTeleinfo);
        $value=$contentTeleinfo[$device->param1];
        $lastType = "teleinfo";
    }
    
    //GCE
    if(strtolower($product->name) == "gce_compteur"){
        if($lastIpAddress != $device->ip_address){
            $url = "http://".$device->ip_address."/status.xml";
            $lastIpAddress=$device->ip_address;
            $contentCompteur = @file_get_contents($url, false, $context);
        }elseif($lastType != "compteur"){
            $url = "http://".$device->ip_address."/status.xml";
            $lastIpAddress=$device->ip_address;
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }
        $xml = simplexml_load_file($contentCompteur);
        $param=$device->param1;
        $value=$xml->$param;
        $lastType = "compteur";
    }
    if(strtolower($product->name) == "gce_teleinfo"){
        if($lastIpAddress != $device->ip_address){
            $url = "http://".$device->ip_address."/protect/settings/teleinfo1.xml";
            $lastIpAddress=$device->ip_address;
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }elseif($lastType != "teleinfo"){
            $url = "http://".$device->ip_address."/protect/settings/teleinfo1.xml";
            $lastIpAddress=$device->ip_address;
            $contentTeleinfo = @file_get_contents($url, false, $context);
        }
        $xml = simplexml_load_file($contentTeleinfo);
        $param=$device->param1;
        $value=$xml->$param;
        $lastType = "teleinfo";
    }
    
    if($value != ""){
        //$output .= "";
        //$output .= "var series = chart".$_POST["itemId"].".series[0];";
        $ooutput .= "var shift = chart".$_POST["itemId"].".series[".$i."].data.length > 20;";
        $output .= "chart".$_POST["itemId"].".series[".$i."].addPoint([".date('U').", ".intval($value)."],true,shift);";
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