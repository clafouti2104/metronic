<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["websiteId"])){
    echo "error";
    return "error";
}

$sql="DELETE FROM pageitem WHERE params LIKE '%\"id\":\"".$_POST["websiteId"]."\"%';";
$sql="DELETE FROM config WHERE id='".$_POST["websiteId"]."';";
$stmt = $GLOBALS["dbconnec"]->exec($sql);
echo "success";

?>
