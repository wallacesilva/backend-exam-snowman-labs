#!/bin/bash

echo "Simple deploy..."

php artisan cache:clear && php artisan config:clear && php artisan clear-compiled

# migrate database if needed
# php artisan migrate --force