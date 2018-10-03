#!/bin/bash

echo "Installing dependencies..."

composer install 

echo "Laravel basic configurations..."

composer run-script post-root-package-install

composer run-script post-create-project-cmd

echo "Creating database default using sqlite ..."
read -p "Continue with Sqlite(y/n)?" dbchoice
case "$dbchoice" in 
  y|Y ) touch database/database.sqlite;;
  n|N ) echo "Configure database in .env, later run migrate";;
  * ) echo "Configure database in .env, later run migrate";;
esac

read -p "Continue with Migrations(y/n)?" choice
case "$choice" in 
  y|Y ) php artisan migrate --seed;;
  n|N ) echo "Later run command: php artisan migrate --seed";;
  * ) echo "Later run command: php artisan migrate --seed";;
esac