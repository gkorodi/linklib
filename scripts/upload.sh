#!/bin/sh -x

mysqldump -u root --password=root links links > links_tbldump.sql
scp *_tbldump.sql gaborkorodi:/tmp/
[ $? -eq 0 ] && rm -f *_tbldump.sql
ssh gaborkorodi "mysql -u root --password=Kaposvar-16 links < /tmp/links_tbldump.sql"
ssh gaborkorodi "rm -f /tmp/*_tbldump.sql"
