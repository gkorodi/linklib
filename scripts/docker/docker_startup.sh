#!/bin/sh -x

MYSQL_ROOT_PASSWORD=root

/usr/local/bin/docker stop linklib_mdb
/usr/local/bin/docker run -d --rm \
	--name linklib_mdb \
	--publish 3310:3306 \
	--env MYSQL_ROOT_PASSWORD="$MYSQL_ROOT_PASSWORD" \
	--env MYSQL_DATABASE=linksdb \
	mariadb:latest

/usr/local/bin/docker stop linklib_web
/usr/local/bin/docker run -d --rm \
	--name linklib_web \
	--publish 88:80 \
	--publish 443:443 \
	httpd

