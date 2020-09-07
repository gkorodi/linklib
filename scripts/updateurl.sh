#!/bin/sh

URL=$1
cat <<EOB > req.json
{
  "url": "${URL}"
}
EOB

curl --silent \
	-H "Authorization: testToken" \
	-X POST \
	--data @req.json \
		"http://localhost:99/api/refreshLink.php" 

rm -f req.json