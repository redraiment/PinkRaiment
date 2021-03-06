#!/bin/bash

git clone --depth 1 http://git.oschina.net/redraiment/PinkRaiment.git
cd PinkRaiment

rm bin/get.pink.sh

git clone --depth 1 http://git.oschina.net/redraiment/phpActiveRecord.git
mv phpActiveRecord app/models
rm -rf app/models/.git* app/models/*test* app/models/zombie.php app/models/README.md

echo 'bin/' > .gitignore
echo 'db/versions.sqlite3' >> .gitignore
rm -rf .git README.md
git init
git add .
git commit -m 'init project with PinkRaiment'
