<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/CondAction.php";

if(!isset($_POST["condId"]) && !isset($_POST["commandId"])){
    echo "error";
    return "error";
}

$type="";
$value=$more=NULL;
switch(strtolower($_POST["type"])){
    case 'action':
        $type="action_message";
        break;
    case 'scenario':
        $type="action_scenario";
        break;
    case 'variable':
        $type="action_variable";
        $more=$_POST["action"];
        $value=$_POST["value"];
        break;
    case 'notification':
        $type="notification";
        break;
    default:
        return true;
}

if($_POST["condActionId"] != ""){
    $condAction = CondAction::getCondAction($_POST["condActionId"]);
    $condAction->type=$type;
    $condAction->action=$_POST["commandId"];
    $condAction->more=$more;
    $condAction->value=$value;
    $condAction->update();
} else {
    $condAction = CondAction::createCondAction($_POST["condId"], $type, $_POST["commandId"],$value,$more);
}

echo $condAction->id;
?>
