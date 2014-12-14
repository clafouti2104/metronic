<?php
if($ipAddress == ""){
    echo "error1";
    return false;
}
if($param1 == ""){
    echo "error2";
    return false;
}
if($command == ""){
    echo "error3";
    return false;
}
file_get_contents("http://".$ipAddress."/pub/remote_control?code=".$param1."&key=".$command);
?>
