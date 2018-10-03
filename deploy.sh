#!/bin/bash

echo "Simple deploy..."

git pull origin master

# migrate database if needed
php artisan migrate --force