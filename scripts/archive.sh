#!/bin/sh +x

log() {
	php -r "echo date('c');"
	echo " "$1
}
log "Starting"

DATESTAMP=`date +%Y-%m-%d`

mysqldump -u root --password=<DONTPUBLISHINSOURCECONTROL> links > /tmp/links_dbdump_${DATESTAMP}.sql
aws s3 cp /tmp/links_dbdump_${DATESTAMP}.sql s3://www.gaborkorodi.com/
RC=$?

if [ ${RC} -eq 0 ] ;
then
  log "Archived dbdump to S3 bucket."
else
  log "ERROR: Could not archive dbdump to S3 bucket."
  zip -m /var/tmp/links_dbdump_archive_${DATESTAMP}.zip /tmp/links_dbdump_${DATESTAMP}.sql
fi

log "Finished"
