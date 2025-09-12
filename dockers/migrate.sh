#!/bin/bash

# Script de migração do banco de dados ROSS
# Execução MANUAL - nunca automática

echo "🔍 Verificando estado do banco de dados antes da migração..."

# Verificar se os containers estão rodando
if ! docker ps | grep -q php-apache; then
    echo "❌ Container php-apache não está rodando!"
    echo "Execute: ./install.sh primeiro"
    exit 1
fi

if ! docker ps | grep -q pgverctor; then
    echo "❌ Container PostgreSQL não está rodando!"
    echo "Execute: ./install.sh primeiro"
    exit 1
fi

# Carregar variáveis de ambiente
if [ -f "../public_html/.env" ]; then
    export $(grep -v '^#' ../public_html/.env | xargs)
else
    echo "❌ Arquivo .env não encontrado!"
    exit 1
fi

# Verificar se as variáveis estão definidas
if [ -z "$DB_HOST" ] || [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ]; then
    echo "❌ Variáveis de banco de dados não configuradas!"
    echo "Configure o arquivo ../public_html/.env"
    exit 1
fi

# Testar conexão
echo "🔌 Testando conexão com o banco..."
if ! docker exec php-apache bash -c "cd /var/www/html && PGPASSWORD='$DB_PASSWORD' psql -h '$DB_HOST' -U '$DB_USERNAME' -d '$DB_DATABASE' -c 'SELECT 1;'" > /dev/null 2>&1; then
    echo "❌ Não foi possível conectar ao banco de dados!"
    echo "Verifique as configurações no arquivo .env"
    exit 1
fi

echo "✅ Conexão com banco estabelecida!"

# Verificar se existem tabelas
echo "🔍 Verificando tabelas existentes..."
TABLE_COUNT=$(docker exec php-apache bash -c "cd /var/www/html && PGPASSWORD='$DB_PASSWORD' psql -h '$DB_HOST' -U '$DB_USERNAME' -d '$DB_DATABASE' -t -c 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '\''public'\'';'" 2>/dev/null | tr -d ' ')

if [ "$TABLE_COUNT" -gt 0 ]; then
    echo "ℹ️  Banco de dados já possui $TABLE_COUNT tabela(s)."
    
    # Listar tabelas existentes
    echo "📋 Tabelas existentes:"
    docker exec php-apache bash -c "cd /var/www/html && PGPASSWORD='$DB_PASSWORD' psql -h '$DB_HOST' -U '$DB_USERNAME' -d '$DB_DATABASE' -c 'SELECT table_name FROM information_schema.tables WHERE table_schema = '\''public'\'' ORDER BY table_name;'"
    
    echo ""
    read -p "⚠️  Deseja executar as migrações mesmo assim? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "❌ Migração cancelada pelo usuário."
        exit 0
    fi
else
    echo "ℹ️  Banco de dados vazio. Executando migrações..."
fi

# Executar migrações
echo "🚀 Executando migrações..."
if docker exec php-apache composer run migrate; then
    echo "✅ Migrações executadas com sucesso!"
else
    echo "❌ Erro ao executar migrações!"
    exit 1
fi

# Verificar resultado
echo "🔍 Verificando resultado das migrações..."
FINAL_COUNT=$(docker exec php-apache bash -c "cd /var/www/html && PGPASSWORD='$DB_PASSWORD' psql -h '$DB_HOST' -U '$DB_USERNAME' -d '$DB_DATABASE' -t -c 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '\''public'\'';'" 2>/dev/null | tr -d ' ')

echo "📊 Total de tabelas após migração: $FINAL_COUNT"

if [ "$FINAL_COUNT" -gt "$TABLE_COUNT" ]; then
    echo "✅ Novas tabelas criadas com sucesso!"
else
    echo "ℹ️  Nenhuma nova tabela foi criada."
fi

echo "🎉 Processo de migração concluído!"

