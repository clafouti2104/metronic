<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Liste.php";

if(!isset($_POST["listeId"])){
    echo "error";
    return "error";
}

$Liste=Liste::getListe($_POST["listeId"]);
$Liste->delete();

echo "done";
?>
