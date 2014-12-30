<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Alert.php";

if(!isset($_POST["alertId"])){
    echo "error";
    return "error";
}

//Récupération du Alert
$alert=Alert::getAlert($_POST["alertId"]);
$alert->delete();
echo "success";

?>
