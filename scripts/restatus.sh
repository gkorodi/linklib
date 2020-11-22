#!/bin/sh

ROWS=$(mysql links --skip-column-names --quick --silent -e "select count(*) from links where level is not null and status != 200;")

for ROW_ID in $ROWS
do
	#echo $ROW_ID
	ROW_LINK=$(mysql links --skip-column-names --quick --silent -e "SELECT link FROM links WHERE id = ${ROW_ID};")
	echo "id: ${ROW_ID}" >> /data/links/${ROW_ID}.details
	echo "link: ${ROW_LINK}" >> /data/links/${ROW_ID}.details
	curl -I -L --silent "${ROW_LINK}" >> /data/links/${ROW_ID}.details 2>&1 &
done
exit

for FILENAME in /data/links/*.details
do
	ROW_ID=$(basename $FILENAME ".details")
	NEW_STATUS=$(grep "HTTP/" $FILENAME | tail -1 | cut -d" " -f 2)
	mysql links --silent -e "UPDATE links SET status = ${NEW_STATUS} WHERE id = ${ROW_ID};"
	#RC=$?
	#if [ $RC -ne 0 ];
	#then
#		echo "Could not update ${ROW_ID} with new status ${NEW_STATUS}"
	#fi
done


