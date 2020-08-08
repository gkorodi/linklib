#!/bin/zsh

/usr/bin/ssh gaborkorodi "mysqldump links links" > links_links_$(date +%Y-%m-%d).sql

/usr/bin/ssh gaborkorodi "mysqldump links" > links_db.sql

