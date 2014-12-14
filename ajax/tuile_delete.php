<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Tuile.php";

if(!isset($_POST["tuileId"])){
    echo "error";
    return "error";
}

$tuile=Tuile::getTuile($_POST["tuileId"]);
$tuile->delete();

echo "done";
?>
