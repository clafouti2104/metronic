<?php
$ipRpi="192.168.1.67";
$ipPopcorn="192.168.1.15";
$server="192.168.1.23/domo";
$urlFreebox="http://hd1.freebox.fr/pub/remote_control?code=79232598";

$GLOBALS['path']="..";

function connectDB(){
    $db = new PDO('mysql:host=l-pma;dbname=domo', 'root', 'pAss4dom');
    //$db = new PDO('mysql:host=192.168.1.23;dbname=domo', 'root', 'triplm');
    return $db;
}

function getToken(){
    $login=$password="";
    //Récupération du login & mot de passe
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    foreach($ini as $title => $value){
        switch($title){
            case "myfox_login":
                $login = $value;
                break;
            case "myfox_password":
                $password = $value;
                break;
            default:
        }
    }
    
    if($login == "" || $password==""){
        return false;
    }
    
    $response=exec("curl -u a5633ca0f6c65183c1a5f7bd755de7e6:CCFnhIpX0TIPaMDEcOfVF7u52UW2BxUG https://api.myfox.me/oauth2/token -d 'grant_type=password&username=".$login."&password=".$password."'");
    $json=json_decode($response);
    
    if(!isset($json->access_token)){
        return false;
    }
    
    //Enregistrement du token dans le fichier de configuration
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content = "[parameters]";
    foreach($ini as $title => $value){
        switch($title){
            case "myfox_token":
                $value = $json->access_token;
                break;
            default:
        }
        $content .="\n\t".$title."=\"".$value."\"";
        
    }
    file_put_contents("/var/www/metronic/tools/parameters.ini", $content);
    $token = $json->access_token;
    print_r($json);
    return $token;
}

$colors=array();
$colors["bleu"]=array(
    "blue"=>"bleu",
    "blue-hoki"=>"bleu gris",
    "blue-steel"=>"bleu métal",
    "blue-madison"=>"bleu madison",
    "blue-chambray"=>"bleu chambray"
);
$colors["vert"]=array(
    "green"=>"vert",
    "green-meadow"=>"vert meadow",
    "green-seagreen"=>"vert mer",
    "green-turquoise"=>"vert turquoise"
);
$colors["rouge"]=array(
    "red"=>"rouge",
    "red-ping"=>"rouge rose",
    "red-sunglo"=>"rouge sunglo",
    "red-intense"=>"rouge intense",
    "red-thunderbird"=>"rouge thunderbird"
);
$colors["jaune"]=array(
    "yellow"=>"jaune",
    "yellow-gold"=>"jaune or",
    "yellow-casablanca"=>"jaune casablanca",
    "yellow-crusta"=>"jaune crusta",
    "yellow-lemon"=>"jaune citron"
);
$colors["violet"]=array(
    "purple"=>"violet",
    "purple-plum"=>"violet plum",
    "purple-medium"=>"violet medium",
    "purple-studio"=>"violet studio"
);
$colors["gris"]=array(
    "grey"=>"gris",
    "grey-gallery"=>"gris foncé",
    "grey-cascade"=>"gris cascade",
    "grey-silver"=>"gris argent",
    "grey-steel"=>"gris metal"
);

$db=connectDB();
?>