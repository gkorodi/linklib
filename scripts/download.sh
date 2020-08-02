#!/bin/sh -x

ssh gaborkorodi "rm -f /tmp/*_tbldump.sql"
ssh gaborkorodi "mysqldump -u root --password=Kaposvar-16 links links > /tmp/links_tbldump.sql"
scp gaborkorodi:/tmp/*_tbldump.sql ./
mysql -u root --password=root links < links_tbldump.sql
[ $? -eq 0 ] && rm -f *_tbldump.sql
