#!/bin/sh

echo "Aguardando MySQL subir..."
until nc -z mysql 3306; do
  sleep 1
done

echo "MySQL está no ar - rodando migrations..."
php artisan migrate --force

echo "Recriando symlink do storage..."
if [ -L public/storage ] || [ -d public/storage ]; then
  rm -rf public/storage
fi
php artisan storage:link || true

echo "Otimizando cache do Laravel..."
php artisan config:clear
php artisan view:clear

echo "Iniciando o PHP-FPM..."
exec php-fpm