#!/bin/bash

echo "Simple deploy..."

# php -r "file_exists('./database/database.sqlite') || touch('./database/database.sqlite');"

# migrate database if needed
php artisan migrate --force