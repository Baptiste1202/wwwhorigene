#!/usr/bin/env bash
set -e

cd /var/www/html || exit 1

# Composer install (si vendor manquant ou forcé)
if [ ! -f vendor/autoload.php ] || [ "${COMPOSER_INSTALL:-0}" = "1" ]; then
  echo ">> composer install ..."
  composer install --no-interaction --prefer-dist --no-progress || true
fi

# Dossiers docs + droits (ajuste si tu veux éviter 777)
for d in public/docs/sequencing public/docs/phenotype public/docs/drugs; do
  mkdir -p "$d"
done
chown -R www-data:www-data public/docs || true
chmod -R 777 public/docs || true
# (plus strict éventuel)
# find public/docs -type d -exec chmod 775 {} \; ; find public/docs -type f -exec chmod 664 {} \;

# Migrations (sans question) — désactivable avec SKIP_MIGRATIONS=1
if [ -f bin/console ] && [ "${SKIP_MIGRATIONS:-0}" != "1" ]; then
  php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true
fi

# FOS Elastica populate si la commande existe — désactivable avec SKIP_POPULATE=1
if [ -f bin/console ] && [ "${SKIP_POPULATE:-0}" != "1" ] && php bin/console list 2>/dev/null | grep -q 'fos:elastica:populate'; then
  php bin/console fos:elastica:populate --no-interaction || true
fi

# Cache dev/prod
if [ -f bin/console ]; then
  php bin/console cache:clear -n || true
  php bin/console cache:clear --env=prod -n || true
fi

# Lance Apache
exec apache2-foreground

