<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
if(!isset($_POST["type"]) || !isset($_POST["messageId"])){
    echo "error";
    return false;
}

//Recuperation du nom de produit
$sql="SELECT md.command, d.ip_address, d.param1, d.param2, p.name ";
$sql.=" FROM messagedevice md, device d, product p ";
$sql.=" WHERE md.deviceId=d.id AND d.product_id=p.id ";
$sql.=" AND md.id=".$_POST["messageId"];
//echo $sql;
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $productName = $row["name"];
    $command = $row["command"];
    $ipAddress = $row["ip_address"];
    $param1 = $row["param1"];
    $param2 = $row["param2"];
}

if(!isset($productName)){
    echo "error";
    return false;
}

switch(strtolower($productName)){
    case "freebox":
        include("action/freebox.php");
        break;
    case "myfox_light":
        include("action/myfox_light.php");
        break;
    case "myfox_alarm":
        include("action/myfox_alarm.php");
        break;
    default:
        echo "error";
        return false;
}

return true;
?>
