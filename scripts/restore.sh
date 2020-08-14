#!/bin/ksh

logger() {
	MSG=$1
	TIMESTAMP=$(date +%Y-%m-%d)
	echo "${TIMESTAMP} ${MSG}"
}

logger "Starting"

FILENAME=links_links_$(date +%Y-%m-%d).sql	
logger "Filename ${FILENAME}"

logger "RowCount"
docker exec linksdb sh -c "mysql -u root --password=root links -e 'SELECT COUNT(*) FROM links;'"

logger 'Dump `links` table'
docker exec linksdb sh -c "mysqldump -u root --password=root links links" > $FILENAME

logger "Send dumpfile to remote server"
scp ${FILENAME} gaborkorodi:/tmp/${FILENAME}

logger "Get remote rowcount"
ssh gaborkorodi "mysql links -e 'SELECT COUNT(*) FROM links;'"

logger "Apply new data to database"
ssh gaborkorodi "mysql links < /tmp/${FILENAME}"

logger "Get remote rowcount, again"
ssh gaborkorodi "mysql links -e 'SELECT COUNT(*) FROM links;'"

logger "Finished"

