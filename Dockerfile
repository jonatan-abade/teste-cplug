# Imagem base com PHP 8.3 + Composer + Extensões necessárias
FROM php:8.3-fpm

# Instala dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev sqlite3 libsqlite3-dev supervisor \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do Laravel
COPY . .

# Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Copia configuração do Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Permissões para storage e bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

CMD ["/usr/bin/supervisord"]
