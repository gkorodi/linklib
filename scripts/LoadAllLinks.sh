#!/bin/sh

echo "***** "$(date)" *****"
mv $HOME/Desktop/${1}*.webloc /Users/Shared/staging/
find /Users/Shared/staging/ -name "${1}*.webloc" -exec plutil -convert xml1 {} \;
php LoadAllLinks.php
