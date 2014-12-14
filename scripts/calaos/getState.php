<?php
//PARAMETRES
$host='localhost';
$dbName='domo';
$login='root';
$password='domokine';
$sql="";

$matching=array(
    "input_95"=>"Sonde",
    "input_96"=>"Sonde",
    "input_98"=>"Sonde",
    "input_103"=>"Sonde",
    "input_89"=>"Sonde",
    "input_104"=>"Sonde Toiture",
    "input_105"=>"Sonde Nord",
    "input_106"=>"Sonde Sud",
    "input_102"=>"Sonde",
    "input_90"=>"Sonde",
    "input_101"=>"Sonde baie informatique",
    "input_107"=>"Sonde",
    "input_86"=>"Sonde",
    "input_97"=>"Sonde",
    "input_99"=>"Sonde",
    "input_100"=>"Sonde"
);

//Connexion a la BDD
$db = new PDO('mysql:host='.$host.';dbname='.$dbName, $login, $password);

//RECUPERATION INFO CALAOS
exec('wget --no-check-certificate --post-file /var/www/metronic/scripts/calaos/state.json --output-document /var/www/metronic/scripts/calaos/result.json https://192.168.1.100/api.php',$response);

$results = file_get_contents('/var/www/metronic/scripts/calaos/result.json');
$results = json_decode($results,TRUE);

foreach($results as $type=>$result){
        //print_r($result);
        if(count($result)==0){
                continue;
        }
        foreach($result as $calaosId => $value){
                $sql .= "INSERT INTO temperature(name, date, value, deviceid, calaosid) VALUES ('".$matching[$calaosId]."', NOW(), '".$value."',NULL, '".$calaosId."' );";
        }
}
$db->query($sql);
?>