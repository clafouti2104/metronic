<?php
$login="maisonkling";
$password="lamaison";

$timeout = array('http' => array('timeout' => 10));
$context = stream_context_create($timeout);
$contentToken=file_get_contents("https://zibase.net/api/get/ZAPI.php?login=".$login."&password=".$password."&service=get&target=token", false, $context);
if(is_null($contentToken)){
    die('Error getting token');
}
if($contentToken == ""){
    die('Error getting token');
}

$jsonToken = json_decode($contentToken);
if(!isset($jsonToken->body->token)){
    die('Error getting token');
}
if(!isset($jsonToken->body->zibase)){
    die('Error getting zibase');
}

$zibase=$jsonToken->body->zibase;
$token=$jsonToken->body->token;

//$contentHome=file_get_contents("https://zibase.net/api/get/ZAPI.php?zibase=".$zibase."&token=".$token."&service=get&target=home", false, $context);
$contentHome=file_get_contents("https://zibase.net/api/get/ZAPI.php?zibase=".$zibase."&token=".$token."&service=get&target=probe&id=OS706330880", false, $context);
print_r($contentHome);



?>
