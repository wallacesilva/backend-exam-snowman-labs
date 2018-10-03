#!/bin/bash

echo "Simple deploy..."

# migrate database if needed
php artisan migrate --force