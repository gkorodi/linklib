#!/bin/sh -x

cat > /etc/apk/repositories << EOF
http://dl-cdn.alpinelinux.org/alpine/v$(cat /etc/alpine-release | cut -d'.' -f1,2)/main
http://dl-cdn.alpinelinux.org/alpine/v$(cat /etc/alpine-release | cut -d'.' -f1,2)/community
EOF

apk add openrc --no-cache

apk update &&
apk add php7 php7-fpm php7-opcache &&
apk add php7-gd php7-mysqli php7-zlib php7-curl &&
php --version

apk add apache2 php7-apache2
#rc-service apache2 restart
  
