<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}

if(!isset($_POST["name"])){
    echo "name";
    return "error";
}

$query = "SELECT COUNT(*) ";
$query .= " FROM config";
$query .= " WHERE name='pushing_box'";
$query .= " AND value='".$_POST["deviceId"]."'";

$stmt = $GLOBALS["dbconnec"]->prepare($query);

$stmt->execute(array());
$row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
$stmt = NULL;

if(intval($row[0])){
    echo "exist";
    return "exist";
}

$sql ="INSERT INTO config (name, value, comment) VALUES ('pushing_box', '".$_POST["deviceId"]."', '".$_POST["name"]."')";
$stmt = $GLOBALS["dbconnec"]->exec($sql);

echo "success";
?>
