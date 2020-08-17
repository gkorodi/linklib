#!/bin/zsh 

cd $HOME/Desktop
for FN in *.webloc
do
	/usr/bin/plutil -convert json $FN
	/usr/bin/plutil -replace title -string "${FN}" "${FN}"
	
	tmpfile=$(mktemp linkload.XXXXXX)
	curl -k --silent --data @"${FN}" --output $tmpfile https://gaborkorodi.com/linklib/api_link.php
	
	CURL_STATUS=`/usr/local/bin/jq -r .status $tmpfile`
	if [ "${CURL_STATUS}" = "ok" ]; then
	  rm -f $tmpfile
	  rm -f "${FN}"
	else
		grep 'Duplicate entry ' $tmpfile >/dev/null
		GREP_STATUS=$?
		if [ $GREP_STATUS -ne 0 ];
		then
			/usr/local/bin/jq . $tmpfile > "${FN}-error"
		else
			rm -f "${FN}"
		fi
		rm -f $tmpfile
	fi
	
done
echo "Finished"

