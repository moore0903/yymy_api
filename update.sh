#!/bin/sh
cd `dirname $0`
git pull
php artisan migrate
php artisan optimize
chown -R www:www ./