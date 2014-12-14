<?php
$token=getToken();
file_get_contents("https://api.myfox.me:443/v2/site/10562/device/".$param1."/socket/".$command."?access_token=".$token);
        
?>
