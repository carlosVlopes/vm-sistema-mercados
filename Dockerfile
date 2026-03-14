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
    npm \
    supervisor

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

# Copiar configuração do Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar script de entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expor porta
EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]