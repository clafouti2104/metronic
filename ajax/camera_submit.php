<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["name"])){
    echo "error";
    return "error";
}

$query = "SELECT COUNT(*) ";
$query .= " FROM config";
$query .= " WHERE name='camera'";
$query .= " AND value='".$_POST["name"]."'";

$stmt = $GLOBALS["dbconnec"]->prepare($query);

$stmt->execute(array());
$row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
$stmt = NULL;

if(intval($row[0])){
    echo "exist";
    return "exist";
}

$comment=array(
    "ip"=>$_POST["ip"],
    "cameraStream"=>$_POST["stream"],
    "cameraStreamImage"=>$_POST["streamImage"]
);

$sql ="INSERT INTO config (name, value, comment) VALUES ('camera', '".$_POST["name"]."', '".  json_encode($comment)."')";
$stmt = $GLOBALS["dbconnec"]->exec($sql);

echo "success";
?>
