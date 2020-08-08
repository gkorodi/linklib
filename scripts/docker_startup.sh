#!/bin/sh -x

MYSQL_ROOT_PASSWORD=root

/usr/local/bin/docker stop mdb
/usr/local/bin/docker run -d --rm \
	--name mdb \
	--publish 3310:3306 \
	--env MYSQL_ROOT_PASSWORD="$MYSQL_ROOT_PASSWORD" \
	--env MYSQL_DATABASE=linksdb \
	mariadb:latest

