FROM php:8.2-apache-bullseye

# --------------------------
# Installer les dépendances système et PHP
# --------------------------
RUN apt-get update \
    && apt-get install -y \
        apt-transport-https \
        ca-certificates \
        libicu-dev \
        git \
        unzip \
        zip \
        libzip-dev \
        libonig-dev \
        && docker-php-ext-install intl mysqli pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# --------------------------
# Activer mod_rewrite pour Apache
# --------------------------
RUN a2enmod rewrite

# --------------------------
# Copier le projet et configurer les droits
# --------------------------
COPY . /var/www/html
WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# --------------------------
# Installer Composer
# --------------------------
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# --------------------------
# Installer les dépendances PHP du projet
# --------------------------
RUN composer install --ignore-platform-reqs --no-interaction --prefer-dist

# --------------------------
# Configurer Apache pour que public/ soit la racine
# --------------------------
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# --------------------------
# Exposer le port 8080
# --------------------------
EXPOSE 8080

# --------------------------
# Lancer Apache au démarrage
# --------------------------
CMD ["apache2-foreground"]
