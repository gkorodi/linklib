#!/bin/sh +x

log() {
	php -r "echo date('c');"
	echo " "$1
}
log "Starting"

mysqldump -u root --password=Kaposvar-16 links > /tmp/links_dbdump.sql
aws s3 cp /tmp/links_dbdump.sql s3://www.gaborkorodi.com/
RC=$?

if [ ${RC} -eq 0 ] ;
then
  log "Archived dbdump to S3 bucket."
else
  log "ERROR: Could not archive dbdump to S3 bucket."
  zip /var/tmp/links_dbdump_archive.zip /tmp/links_dbdump.sql
fi

log "Finished"
