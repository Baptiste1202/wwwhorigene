# Dockerfile pour Render — Symfony + Apache + PHP 8.3 + Elasticsearch
FROM php:8.3-apache

# --------------------------
# 1) Extensions PHP nécessaires
# --------------------------
RUN apt-get update && apt-get install -y libpq-dev openjdk-17-jdk wget unzip \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# --------------------------
# 2) Outils utiles
# --------------------------
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN apt-get install -y --no-install-recommends zip \
    && rm -rf /var/lib/apt/lists/*

# --------------------------
# 3) Apache
# --------------------------
RUN a2enmod rewrite
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2dissite 000-default || true && a2ensite 000-default

# --------------------------
# 4) Installer Elasticsearch
# --------------------------
RUN wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.17.16-linux-x86_64.tar.gz \
    && tar -xzf elasticsearch-7.17.16-linux-x86_64.tar.gz -C /opt/ \
    && rm elasticsearch-7.17.16-linux-x86_64.tar.gz

# Définir le dossier de données Elastic
RUN mkdir -p /usr/share/elasticsearch/data
VOLUME /usr/share/elasticsearch/data

# --------------------------
# 5) Copier le code Symfony
# --------------------------
COPY dev/ /var/www/html/
WORKDIR /var/www/html

# --------------------------
# 6) Exposer les ports
# --------------------------
EXPOSE 80 9200 9300

# --------------------------
