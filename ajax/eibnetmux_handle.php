<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["action"])){
    echo "error";
    return "error";
}
if($_POST["action"] != "start" && $_POST["action"] != "stop"){
	echo "error";
    return "error";
}

shell_exec("/usr/bin/nohup /etc/init.d/eibnetmux ".$_POST["action"]." > /dev/null 2>&1 &");
echo "success";

?>
