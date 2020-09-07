#!/bin/sh -x

HOSTNAME=$(hostname)
if [ "${HOSTNAME}" = "Gabe-MBP062" ];
then
  export LINKLIB_HOME=/Users/gabork/PhpstormProjects/linklib
fi

source $LINKLIB_HOME/.env
alias dcom='$DCOM_HOME/docker-compose -f $LINKLIB_HOME/scripts/stack/docker-compose.yml'
TIMESTAMP=$(date +%Y-%m-%d_%H-%M-%S)

$LINKLIB_HOME/scripts/compare.sh

dcom exec db sh -c "mysqldump --user=root --password=${MARIADB_PASSWORD} links" > $LINKLIB_HOME/scripts/data/links_db_local.sql
scp $LINKLIB_HOME/scripts/data/links_db_local.sql gaborkorodi:/tmp/links_db_local.sql
ssh gaborkorodi "mysqldump links > /tmp/links_db_backup_${TIMESTAMP}.sql"
ssh gaborkorodi "mysql --user=root --password=Kaposvar-16 links < /tmp/links_db_local.sql"
ssh gaborkorodi "rm -f /tmp/links_db_local.sql"

$LINKLIB_HOME/scripts/compare.sh
