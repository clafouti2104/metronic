<?php
include("../../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();

exec('mysqldump --defaults-file=/home/manager/.my.cnf -u domoManager '.dbName.' > /var/www/save/domo_'.date('Ymd').'.sql',$output);

if(file_exists('/var/www/save/domo_'.date('Ymd').'.sql')){
    echo "success";
}

?>
