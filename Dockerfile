FROM php:8.2-cli

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /app

# Copier le projet
COPY . .

# Installer Laravel
RUN composer install --no-dev --optimize-autoloader

# Donner permissions
RUN chmod -R 777 storage bootstrap/cache

# Générer clé Laravel
RUN php artisan key:generate

# Lancer serveur
CMD php artisan serve --host=0.0.0.0 --port=10000
