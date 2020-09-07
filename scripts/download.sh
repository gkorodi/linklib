#!/bin/sh -x

source $LINKLIB_HOME/.env
alias dcom='$DCOM_HOME/docker-compose -f $LINKLIB_HOME/scripts/stack/docker-compose.yml'

dcom stop db
ssh gaborkorodi "mysqldump links" > $LINKLIB_HOME/scripts/data/links_db.sql
dcom up -d db
sleep 5
dcom exec db sh -c "mysql --user=root --password=${MARIADB_PASSWORD} links < /docker-entrypoint-initdb.d/links_links.sql"

$LINKLIB_HOME/scripts/compare.sh