#!/bin/bash

DB="cms"

MASK="http://.*/tickets/.*"

for tab in `mysql -B -u $MSU -p$MSP $DB -e 'SHOW TABLES;'`; do
	mysqldump -n -t -f --skip-opt --compact -u $MSU -p$MSP $DB $tab --where="id RLIKE '$MASK' OR value RLIKE '$MASK'" 2>/dev/null
done
