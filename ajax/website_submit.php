<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["urlSite"])){
    echo "error";
    return "error";
}

if($_POST["websiteId"] == ""){
    $query = "SELECT COUNT(*) ";
    $query .= " FROM config";
    $query .= " WHERE name='website'";
    $query .= " AND value='".$_POST["urlSite"]."'";

    $stmt = $GLOBALS["dbconnec"]->prepare($query);

    $stmt->execute(array());
    $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
    $stmt = NULL;

    if(intval($row[0])){
        echo "exist";
        return "exist";
    }

    $sql ="INSERT INTO config (name, value, comment) VALUES ('website', '".$_POST["urlSite"]."', '')";   
} else {
    $sql ="UPDATE config SET value='".$_POST["urlSite"]."' WHERE id=".$_POST["websiteId"];
}

$stmt = $GLOBALS["dbconnec"]->exec($sql);

echo "success";
?>
