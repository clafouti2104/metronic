<?php
/* 
 * Exécute la tâche planifiée spécifiée en paraamètre
 */

include("../tools/config.php");
include("../tools/action.php");
include("../models/Schedule.php");
include("../models/ScheduleAction.php");
include("../models/Report.php");
$GLOBALS["dbconnec"] = connectDB();

parse_str(implode('&', array_slice($argv, 1)), $_GET);
if(!isset($_GET["scheduleid"])){
    addLog(LOG_ERR, "SCH_TASK: no scheduleid given");
    exit;
}

$schedule = Schedule::getSchedule($_GET["scheduleid"]);
$scheduleActions = ScheduleAction::getScheduleActionForSchedule($_GET["scheduleid"]);

if(count($scheduleActions) == 0){
    addLog(LOG_INFO, "[SCH_TASK] ".$schedule->name.": no action to execute");
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
        case 'rapport':
            $report = Report::getReport($scheduleAction->action);
            $pdfName=str_replace(" ","_",utf8_decode($report->name));
            //Recuperation adresse IP
            $ipAddress = exec("/sbin/ifconfig eth0 | grep 'inet adr:' | cut -d: -f2 | awk '{ print $1}'"); 

            //Generation du rapport en PDF
            exec('wkhtmltopdf --javascript-delay 1000 "http://'.$ipAddress.'/metronic/show_report.php?idReport='.$scheduleAction->action.'&pdf=1" /tmp/rapport_'.$pdfName.'.pdf');
            //Attente de la fin de la generation
            sleep(10);

            //Envoi du mail avec le PDF en PJ
            $subject="[DOMOKINE] Rapport";
            $title="Rapport";
            $content="Vous trouverez le rapport ".utf8_decode($report->name)." en piece jointe";
            $filename="/tmp/rapport_".$pdfName.".pdf";
            exec("chmod 0777 /tmp/rapport_".$pdfName.".pdf");
            if($content != ""){
                include("../controllers/mail.php");
            }
            break;
        default:
    }
}
addLog(LOG_INFO, "SCH_TASK".$_GET["scheduleid"].": executed");
?>