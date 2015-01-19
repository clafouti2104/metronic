<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["gaugeId"])){
    echo "error";
    return "error";
}

$sql="DELETE FROM pageitem WHERE params LIKE '%\"id\":\"".$_POST["gaugeId"]."\"%';";
$sql="DELETE FROM config WHERE id='".$_POST["gaugeId"]."';";
$stmt = $GLOBALS["dbconnec"]->exec($sql);
echo "success";

?>
