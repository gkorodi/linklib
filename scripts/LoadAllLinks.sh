#!/bin/sh

echo "***** "$(date)" *****"
mv $HOME/Desktop/*.webloc /Users/Shared/staging/
find /Users/Shared/staging/ -name "*.webloc" -exec plutil -convert xml1 {} \;
php LoadAllLinks.php
