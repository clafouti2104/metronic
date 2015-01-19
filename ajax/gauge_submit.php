<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["gaugeDevice"])){
    echo "error";
    return "error";
}

if(!isset($_POST["gaugeMinimum"])){
    echo "error";
    return "error";
}

if(!isset($_POST["gaugeMaximum"])){
    echo "error";
    return "error";
}

if($_POST["gaugeId"] == ""){
    $query = "SELECT COUNT(*) ";
    $query .= " FROM config";
    $query .= " WHERE name='gauge'";
    $query .= " AND value='".$_POST["gaugeDevice"]."'";

    $stmt = $GLOBALS["dbconnec"]->prepare($query);

    $stmt->execute(array());
    $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
    $stmt = NULL;

    if(intval($row[0])){
        echo "exist";
        return "exist";
    }

    $sql ="INSERT INTO config (name, value, comment) VALUES ('gauge', '".$_POST["gaugeDevice"]."', '{\"minimum\":\"".$_POST["gaugeMinimum"]."\", \"maximum\":\"".$_POST["gaugeMaximum"]."\"}')";
} else {
    $sql ="UPDATE config SET value='".$_POST["gaugeDevice"]."', comment='{\"minimum\":\"".$_POST["gaugeMinimum"]."\", \"maximum\":\"".$_POST["gaugeMaximum"]."\"}' WHERE id=".$_POST["gaugeId"];
}
$stmt = $GLOBALS["dbconnec"]->exec($sql);

echo "success";
?>
