#!/bin/zsh

if [ ! -d $LINKLIB_HOME/scripts ]; then echo "Cannot find LINKLIB_HOME/scripts directory."; exit 225; fi

/usr/bin/ssh gaborkorodi "mysqldump links links" > links_links_$(date +%Y-%m-%d).sql

/usr/bin/ssh gaborkorodi "mysqldump links" > $LINKLIB_HOME/scripts/data/links_db.sql

