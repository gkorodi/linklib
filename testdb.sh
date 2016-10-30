#!/bin/bash

ssh -fNg -L 3307:127.0.0.1:3306 -i ~/.ssh/gaborkorodicom-keypair.pem ec2-user@gaborkorodi.com

mysql -h 127.0.0.1 -P 3307 -u root -p links
