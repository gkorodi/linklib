#!/bin/sh

HOSTNAME=$(hostname)
if [ "${HOSTNAME}" = "Gabe-MBP062" ];
then
  export LINKLIB_HOME=/Users/gabork/PhpstormProjects/linklib
fi
source $LINKLIB_HOME/.env
alias dcom='$DCOM_HOME/docker-compose -f $LINKLIB_HOME/scripts/stack/docker-compose.yml'

echo "*** Remote Counts ****"
ssh gaborkorodi "mysql links -e 'select count(*) from links'"
curl -H "Authorization: testToken" --silent "https://gaborkorodi.com/linklibrary/api/stats.php" | head -10
echo " "
echo "*** Local Docker Count ***"
dcom exec db sh -c "mysql --user=root --password=${MARIADB_PASSWORD} links -e 'select count(*) from links'"
curl -H "Authorization: testToken"  --silent "http://localhost:99/api/stats.php" | head -10