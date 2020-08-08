#!/bin/sh

MYSQL_ROOT_PASSWORD=root

/usr/local/bin/docker exec -i mdb \
	sh -c 'exec mysql --user=root --password="'$MYSQL_ROOT_PASSWORD'" linksdb ' < /Library/WebServer/Documents/linklib/scripts/links_db.sql