FROM php:8.2-apache-bullseye

# Forcer apt à utiliser HTTPS et installer dépendances
RUN apt-get update \
    && apt-get install -y apt-transport-https ca-certificates \
    && sed -i 's|http://deb.debian.org/debian|https://deb.debian.org/debian|g' /etc/apt/sources.list \
    && apt-get update && apt-get install -y \
    libicu-dev \
    git \
    unzip \
    && docker-php-ext-install intl mysqli pdo pdo_mysql

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html
WORKDIR /var/www/html

# Droits sur writable/
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Installer les dépendances PHP du projet
RUN composer install --ignore-platform-reqs

# Configurer Apache pour que public/ soit la racine
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80