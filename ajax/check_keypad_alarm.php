<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["action"])){
    echo "error";
    return "error";
}
if(!isset($_POST["pin"])){
    echo "error";
    return "error";
}

$query = "SELECT value ";
$query .= " FROM config";
$query .= " WHERE name='pin_alarm'";

$stmt = $GLOBALS["dbconnec"]->prepare($query);
$stmt->execute(array());
if($row = $stmt->fetch(PDO::FETCH_ASSOC, 0)){
    if($row["value"] != $_POST["pin"]){
        echo "codePin";
        return false;
    }
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    foreach($ini as $title => $value){
        if($title == "myfox_token"){
            $token=$value;
            break;
        }
    }
    if($token == ""){
        $token=getToken();
    }
    
    $token=getToken();
    $response=file_get_contents("https://api.myfox.me:443/v2/site/10562/security/set/".$_POST['action']."?access_token=".$token);
    $response=json_decode($response);
    if($response->status == "KO"){
        echo 'ERROR request';
    }
    echo "success";
} else{
    echo "error";
}

?>
