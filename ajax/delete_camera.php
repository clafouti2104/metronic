<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["camearId"])){
    echo "error";
    return "error";
}

$sql="DELETE FROM pageitem WHERE params LIKE '%\"id\":\"".$_POST["camearId"]."\"%';";
$sql="DELETE FROM config WHERE id='".$_POST["camearId"]."';";
$stmt = $GLOBALS["dbconnec"]->exec($sql);
echo "success";

?>
