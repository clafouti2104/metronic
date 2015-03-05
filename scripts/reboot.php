<?php
/* 
 * Redémarrage de la machine
 */
exec("python /var/www/metronic/scripts/reboot.py"); 
echo 'rebooting';

?>