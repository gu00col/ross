FROM php:8.2-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN a2enmod rewrite
RUN a2enmod headers

# Configurações de segurança do Apache
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf

# Configurar PHP
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/memory.ini
RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/upload.ini
RUN echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/upload.ini
RUN echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/execution.ini

# Configurar OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Configurar Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar script de instalação
COPY install.sh /usr/local/bin/install-ross.sh
RUN chmod +x /usr/local/bin/install-ross.sh

# Expor porta
EXPOSE 80

# Comando de inicialização que executa instalação se necessário
CMD ["/bin/bash", "-c", "/usr/local/bin/install-ross.sh && apache2-foreground"]
