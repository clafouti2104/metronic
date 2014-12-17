#!/bin/bash
PIDshell=`ps -eaf | grep getCalaos.sh | grep -v "grep" | cut -c10-14`
if [ -z $PIDshell ]
        then
                cd /var/www/metronic/scripts/calaos
                bash getCalaos.sh &
        else
                kill -9 $PIDshell
                cd /var/www/metronic/scripts/calaos
                bash getCalaos.sh &
fi