#!/bin/sh

source $LINKLIB_HOME/.env
alias dcom='$DCOM_HOME/docker-compose -f $LINKLIB_HOME/scripts/stack/docker-compose.yml'

echo "*** Remote Counts ****"
ssh gaborkorodi "mysql links -e 'select count(*) from links'"

echo "*** Local Docker Count ***"
dcom exec db sh -c "mysql --user=root --password=${MARIADB_PASSWORD} links -e 'select count(*) from links'"