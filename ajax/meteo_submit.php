<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["meteoVille"])){
    echo "error";
    return "error";
}

if(!isset($_POST["meteoPays"])){
    echo "error";
    return "error";
}

if($_POST["meteoId"] == ""){
    $query = "SELECT COUNT(*) ";
    $query .= " FROM config";
    $query .= " WHERE name='meteo'";
    $query .= " AND value='".$_POST["meteoVille"].",".$_POST["meteoPays"]."'";

    $stmt = $GLOBALS["dbconnec"]->prepare($query);

    $stmt->execute(array());
    $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
    $stmt = NULL;

    if(intval($row[0])){
        echo "exist";
        return "exist";
    }

    $sql ="INSERT INTO config (name, value, comment) VALUES ('meteo', '".$_POST["meteoVille"].",".$_POST["meteoPays"]."', '')";
} else {
    $sql ="UPDATE config SET value='".$_POST["meteoVille"].",".$_POST["meteoPays"]."' WHERE id=".$_POST["meteoId"];
}
$stmt = $GLOBALS["dbconnec"]->exec($sql);

exec('cd /var/www/metronic/controllers;php meteo.php');

echo "success";
?>
