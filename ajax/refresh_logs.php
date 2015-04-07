<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

$output="";

$logfile = '/var/log/domokine.log';
$numlines = "20";
$cmd = "tail -$numlines '$logfile'";
$showLogs = shell_exec($cmd);
$showLogs = explode("\n", $showLogs);
$output.="var logs='';";
foreach($showLogs as $showLog){
    $output.="var logs=logs+'".$showLog."<br/>';";
}

$output.="$('.tailLogs').empty();";
$output.="$('.tailLogs').append(logs);";
echo $output;
?>
