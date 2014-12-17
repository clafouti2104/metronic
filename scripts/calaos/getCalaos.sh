#!/bin/bash
for x in $(seq 1 12)
do
    cd /var/www/metronic/scripts/calaos
    php getStateB.php > /dev/null 2>&1
    
    sleep 5
done