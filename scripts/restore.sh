#!/bin/ksh

LINKLIB_HOME=${PWD}
MARIADB_PASSWORD=$1
alias dcom='docker-compose -f ${LINKLIB_HOME}/scripts/stack/docker-compose.yml '

logger() {
	MSG=$1
	TIMESTAMP=$(date +%Y-%m-%d)
	echo "${TIMESTAMP} ${MSG}"
}

logger "Starting"

FILENAME=links_links_$(date +%Y-%m-%d).sql	
logger "Filename ${FILENAME}"

logger "RowCount"
dcom exec db sh -c "mysql -u root --password="${MARIADB_PASSWORD}" links -e 'SELECT COUNT(*) FROM links;'"

logger 'Dump `links` table'
dcom exec db sh -c "mysqldump --user=root --password="${MARIADB_PASSWORD}" links" > $FILENAME

logger "Send dumpfile to remote server"
scp ${FILENAME} gaborkorodi:/tmp/${FILENAME}

logger "Get remote rowcount"
ssh gaborkorodi "mysql links -e 'SELECT COUNT(*) FROM links;'"

logger "Apply new data to database"
ssh gaborkorodi "mysql links < /tmp/${FILENAME}"

logger "Get remote rowcount, again"
ssh gaborkorodi "mysql links -e 'SELECT COUNT(*) FROM links;'"

logger "Finished"

