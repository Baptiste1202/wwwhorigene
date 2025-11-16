# Dockerfile pour Render — Symfony + Apache + PHP 8.3
FROM php:8.3-apache

# --------------------------
# 1) Extensions PHP nécessaires
# --------------------------
RUN apt-get update && apt-get install -y libpq-dev zip unzip git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# --------------------------
# 2) Outils utiles
# --------------------------
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# --------------------------
# 3) Apache
# --------------------------
RUN a2enmod rewrite

# Copier le VHost
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2dissite 000-default || true && a2ensite 000-default

# --------------------------
# 4) Copier le code Symfony
# --------------------------
COPY dev/ /var/www/html/

# Définir le dossier de travail
WORKDIR /var/www/html

# --------------------------
# 5) Installer les dépendances et préparer le dossier public/docs
# --------------------------
RUN composer install --no-interaction --optimize-autoloader \
    && mkdir -p /var/www/html/public/docs/phenotype \
    && chown -R www-data:www-data /var/www/html/public/docs/phenotype \
    && chmod 755 /var/www/html/public/docs/phenotype \
    && chown -R www-data:www-data /var/www/html/public/docs/drugs \
    && chmod 755 /var/www/html/public/docs/drugs \
    && chown -R www-data:www-data /var/www/html/public/docs/sequencing \
    && chmod 755 /var/www/html/public/docs/sequencing \
    

# Exposer le port
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
