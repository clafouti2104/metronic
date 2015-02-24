<?php
/* 
 * Exécute la tâche planifiée spécifiée en paraamètre
 */

include("../tools/config.php");
include("../models/Schedule.php");
include("../models/ScheduleAction.php");

if(!isset($_GET["scheduleid"])){
    addLog(LOG_ERR, "SCH_TASK: no scheduleid given");
    exit;
}

$schedule = Schedule::getSchedule($_GET["scheduleid"]);
$scheduleActions = ScheduleAction::getScheduleActionForSchedule($_GET["scheduleid"]);

if(count($scheduleActions) == 0){
    addLog(LOG_INFO, "SCH_TASK".$_GET["scheduleid"].": no action to execute");
    exit;
}
foreach($scheduleActions as $scheduleAction){
    $sqlVariable = "";
                
    switch (strtolower($scheduleAction->type)){
        case 'action_message':
            executeMessage($scheduleAction->action);
            break;
        case 'action_scenario':
            executeScenario($scheduleAction->action);
            break;
        case 'action_variable':
            switch(strtolower($scheduleAction->more)){
                case 'inc':
                    $sqlVariable="UPDATE config SET comment=comment+".$scheduleAction->value." WHERE id=".$scheduleAction->action.";";
                    break;
                case 'dec':
                    $sqlVariable="UPDATE config SET comment=comment-".$scheduleAction->value." WHERE id=".$scheduleAction->action.";";
                    break;
                case 'set':
                    $sqlVariable="UPDATE config SET comment='".$scheduleAction->value."' WHERE id=".$scheduleAction->action.";";
                    break;
            }
            if(isset($sqlVariable) && $sqlVariable != ""){
                $stmt = $GLOBALS["dbconnec"]->query($sqlVariable);
            }
            break;
        case 'notification':
            if(isset($scheduleAction->action)){
                $ch = curl_init('http://api.pushingbox.com/pushingbox?devid='.$scheduleAction->action);
                file_put_contents("/tmp/info", "PUSHING BOX = http://api.pushingbox.com/pushingbox?devid=".$scheduleAction->action);
                curl_exec ($ch);
                curl_close ($ch);
            }
            break;
        case 'commandline':
            if(isset($scheduleAction->action)){
                $ch = exec($scheduleAction->action);
            }
            break;
        default:
    }
}
addLog(LOG_INFO, "SCH_TASK".$_GET["scheduleid"].": executed");
?>