#!/bin/bash

git clone http://git.oschina.net/redraiment/PinkRaiment.git
cd PinkRaiment
echo 'bin/' > .gitignore
rm -rf .git
git init
git add .
git commit -m 'init project with PinkRaiment'
