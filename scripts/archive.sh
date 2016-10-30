#!/bin/sh +x

log() {
	/usr/local/opt/php55/bin/php -r "echo date('c');"
	echo " "$1
}
log "Starting"

/usr/local/mysql/bin/mysqldump test > test_dbdump.sql
/usr/local/bin/aws s3 cp test_dbdump.sql s3://www.gaborkorodi.com/

log "Finished"
