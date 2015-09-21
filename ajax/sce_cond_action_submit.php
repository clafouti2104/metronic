<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/CondAction.php";
include "../models/ScheduleAction.php";

if(!isset($_POST["condId"]) && !isset($_POST["commandId"]) && !isset($_POST["scheduleId"])){
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
    case 'commandline':
        $type="commandline";
        break;
    case 'report':
        $type="Rapport";
        break;
    default:
        return true;
}

if($_POST["condId"] != "" or $_POST["condActionId"] != ""){
    
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
}

if($_POST["scheduleId"] != ""){
    
    /*if($_POST["condActionId"] != ""){
        $condAction = CondAction::getCondAction($_POST["condActionId"]);
        $condAction->type=$type;
        $condAction->action=$_POST["commandId"];
        $condAction->more=$more;
        $condAction->value=$value;
        $condAction->update();
    } else {*/
        $scheduleAction = ScheduleAction::createScheduleAction($_POST["scheduleId"], $type, $_POST["commandId"],$value,$more);
    //}

    echo $scheduleAction->id;
}
?>
