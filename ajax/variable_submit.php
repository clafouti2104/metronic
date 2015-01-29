<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["name"])){
    echo "error";
    return "error";
}

if($_POST["variableId"] == ""){
    $query = "SELECT COUNT(*) ";
    $query .= " FROM config";
    $query .= " WHERE name='variable'";
    $query .= " AND value='".$_POST["name"]."'";

    $stmt = $GLOBALS["dbconnec"]->prepare($query);

    $stmt->execute(array());
    $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
    $stmt = NULL;

    if(intval($row[0])){
        return "exist";
    }

    $sql ="INSERT INTO config (name, value, comment) VALUES ('variable', '".$_POST["name"]."', '')";
} else {
    $sql ="UPDATE config SET value='".$_POST["name"]."' WHERE id=".$_POST["variableId"];
}
$stmt = $GLOBALS["dbconnec"]->exec($sql);

$output ="$('#variableDevice option').remove();";

//Récupération des variables
$query = "SELECT id,value ";
$query .= " FROM config";
$query .= " WHERE name='variable'";
$stmt = $GLOBALS["dbconnec"]->prepare($query);

$output .= "var opt='';";
$stmt->execute(array());
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $output .= "var opt=opt + '<option value=\"".$row["id"]."\">".$row["value"]."</option>';";
}
$output .="$('#variableDevice').append(opt);";

echo $output;
?>
