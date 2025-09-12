#!/bin/bash

# Script de instalação do ROSS - Analista Jurídico
# Para ser executado dentro do container PHP
# Só executa se vendor não existir

echo "🔍 Verificando se instalação é necessária..."

# Verificar se vendor já existe
if [ -d "vendor" ]; then
    echo "✅ Vendor já existe. Instalação não necessária."
    echo "ℹ️  Para reinstalar, remova a pasta vendor primeiro."
    exit 0
fi

echo "🚀 Instalando ROSS - Analista Jurídico no container PHP..."

# Verificar se o Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ Composer não encontrado. Instalando..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    echo "✅ Composer instalado com sucesso!"
else
    echo "✅ Composer já está instalado!"
fi

# Instalar dependências
echo "📦 Instalando dependências do Composer..."
cd /var/www/html

# Verificar se composer.json existe
if [ ! -f "composer.json" ]; then
    echo "❌ Arquivo composer.json não encontrado!"
    exit 1
fi

# Instalar dependências
if composer install --optimize-autoloader --no-dev; then
    echo "✅ Dependências instaladas com sucesso!"
else
    echo "❌ Erro ao instalar dependências!"
    exit 1
fi

# Criar arquivo .env se não existir
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env..."
    cp .env.example .env
    echo "✅ Arquivo .env criado! Configure as variáveis de ambiente."
fi

# Criar diretórios necessários
echo "📁 Criando diretórios necessários..."
mkdir -p storage/{logs,cache,uploads}
mkdir -p app/Console/Commands
mkdir -p public_html/storage/{logs,cache,uploads}

# Definir permissões
echo "🔐 Definindo permissões..."
chmod -R 755 /var/www/html/
chmod -R 777 /var/www/html/storage/
chmod -R 777 /var/www/html/public_html/storage/

# Instalar extensões PHP necessárias
echo "🔧 Instalando extensões PHP necessárias..."
apt-get update
apt-get install -y libpq-dev
docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite
echo "🔧 Habilitando mod_rewrite..."
a2enmod rewrite

# Configurar Apache
echo "🔧 Configurando Apache..."
cat > /etc/apache2/sites-available/000-default.conf << 'EOF'
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public_html
    
    <Directory /var/www/html/public_html>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Reiniciar Apache
echo "🔄 Reiniciando Apache..."
service apache2 restart

# Verificar configuração do PHP
echo "🔍 Verificando configuração do PHP..."
php -v

# Verificar extensões necessárias
echo "🔍 Verificando extensões PHP necessárias..."
php -m | grep -E "(pdo|pgsql|curl|json|mbstring|openssl|zip)"

# Verificar conectividade com o banco de dados
echo "🔍 Verificando conectividade com o banco de dados..."
if [ -f ".env" ]; then
    # Carregar variáveis do .env
    export $(grep -v '^#' .env | xargs)
    
    # Verificar se as variáveis de banco estão definidas
    if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ] && [ -n "$DB_PASSWORD" ]; then
        echo "📊 Testando conexão com PostgreSQL..."
        
        # Instalar cliente PostgreSQL para teste
        apt-get install -y postgresql-client
        
        # Testar conexão
        if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c "SELECT 1;" > /dev/null 2>&1; then
            echo "✅ Conexão com banco de dados estabelecida!"
            
            # Verificar se as tabelas já existem
            TABLE_COUNT=$(PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public';" 2>/dev/null | tr -d ' ')
            
            if [ "$TABLE_COUNT" -gt 0 ]; then
                echo "ℹ️  Banco de dados já possui $TABLE_COUNT tabela(s). Migrações não serão executadas automaticamente."
            else
                echo "ℹ️  Banco de dados vazio. Migrações podem ser executadas manualmente."
            fi
        else
            echo "⚠️  Não foi possível conectar ao banco de dados. Verifique as configurações no .env"
        fi
    else
        echo "⚠️  Variáveis de banco de dados não configuradas no .env"
    fi
else
    echo "⚠️  Arquivo .env não encontrado. Configure as variáveis de ambiente primeiro."
fi

echo "✅ Instalação concluída no container PHP!"
echo ""
echo "📋 Próximos passos:"
echo "1. Configure o arquivo .env com suas credenciais de banco"
echo "2. Execute as migrações manualmente: docker exec -it php-apache composer run migrate"
echo "3. Acesse: http://localhost:8080"
echo ""
echo "🌐 Sistema ROSS disponível em: http://localhost:8080"
