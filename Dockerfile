FROM php:8.3-cli

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nodejs \
    npm

# Instalar extensões PHP necessárias
RUN docker-php-ext-configure intl && \
    docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /app

# Copiar apenas composer files primeiro (cache layer)
COPY composer.json composer.lock ./

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copiar package.json
COPY package*.json ./

# Instalar dependências Node
RUN npm ci

# Copiar resto do projeto
COPY . .

# Build assets (IMPORTANTE: antes de otimizar)
RUN npm run build

# Rodar comandos do composer após copiar tudo
RUN composer run post-autoload-dump

# Criar diretórios necessários e dar permissões
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Criar link simbólico do storage
RUN php artisan storage:link || true

# Expor porta
EXPOSE 8080

# Script de inicialização
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8080