#!/bin/bash

DB="WWW"
HOST="www.avias.local"

MASK="^http://news.aviaport.ru/"

for tab in `mysql -B -u $MSU -p$MSP $DB -e 'SHOW TABLES;'`; do
	res=`mysqldump -h $HOST -n -t -f --skip-opt --compact -u $MSU -p$MSP $DB $tab --where="id RLIKE '$MASK' OR value RLIKE '$MASK'"` 2>/dev/null
	if [[ "$res." !=  "." ]]; then
		mysqldump -h $HOST -n -f --skip-opt --compact -u $MSU -p$MSP $DB $tab --where="id RLIKE '$MASK' OR value RLIKE '$MASK'" 2>/dev/null
	fi
done
