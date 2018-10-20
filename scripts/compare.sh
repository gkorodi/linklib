#!/bin/sh

echo "select count(*) from links; select count(*) from tobecurated;" > /tmp/a.a
echo "*** Local Counts ***"
mysql -u root --password=root links < /tmp/a.a

scp /tmp/a.a gaborkorodi:/tmp/
echo "*** Remote Counts ****"
ssh gaborkorodi "mysql -u root --password=Kaposvar-16 links < /tmp/a.a"

rm -f /tmp/a.a
ssh gaborkorodi "rm -f /tmp/a.a"
