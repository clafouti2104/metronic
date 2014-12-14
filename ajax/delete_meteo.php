<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["meteoId"])){
    echo "error";
    return "error";
}

$sql="DELETE FROM pageitem WHERE params LIKE '%\"id\":\"".$_POST["meteoId"]."\"%';";
$sql="DELETE FROM config WHERE id='".$_POST["meteoId"]."';";
$stmt = $GLOBALS["dbconnec"]->exec($sql);
echo "success";

?>
