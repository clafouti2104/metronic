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

exec("/etc/init.d/eibnetmux ".$_POST["action"]);
echo "success";

?>
