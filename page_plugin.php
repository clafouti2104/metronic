<?php
$name=$color=$params="";
$params = $item->params;
$params = json_decode($params);
$size = (isset($params->size)) ? $params->size : 4;
$size = (isset($params->width)) ? $params->width : $params->size;
$bgcolor = (isset($params->color)) ? $params->color : 'grey-gallery';

$resultats=$GLOBALS["dbconnec"]->query("SELECT value,comment FROM config WHERE id=".$params->id);
$resultats->setFetchMode(PDO::FETCH_OBJ);
if( $resultat = $resultats->fetch() ){
    $name=$resultat->value;
    $details = json_decode($resultat->comment);
}

switch($params->plugin){
    case 'gauge':
        include "page_plugin_gauge.php";
        break;
    case 'meteo':
        include "page_plugin_meteo.php";
        break;
    case 'camera':
        include "page_plugin_camera.php";
        break;
    case 'website':
        include "page_plugin_website.php";
        break;
    default:
}


?>