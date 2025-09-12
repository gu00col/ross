#!/bin/bash

# Script principal de instalação do ROSS
# Instala docker-compose e executa se não existir vendor

echo "🚀 Instalando ROSS - Analista Jurídico..."

# Verificar se o Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não encontrado. Instale o Docker primeiro."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não encontrado. Instalando..."
    
    # Instalar Docker Compose
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    
    # Verificar instalação
    if docker-compose --version; then
        echo "✅ Docker Compose instalado com sucesso!"
    else
        echo "❌ Erro ao instalar Docker Compose"
        exit 1
    fi
fi

# Verificar se vendor existe
if [ ! -d "../public_html/vendor" ]; then
    echo "📦 Vendor não encontrado. Executando instalação completa..."
    
    # Parar containers existentes
    echo "🛑 Parando containers existentes..."
    docker-compose down 2>/dev/null || true
    
    # Construir e iniciar containers
    echo "🏗️  Construindo e iniciando containers..."
    docker-compose up -d --build
    
    # Aguardar containers iniciarem
    echo "⏳ Aguardando containers iniciarem..."
    sleep 15
    
    # Instalar dependências PHP
    echo "📦 Instalando dependências PHP..."
    if docker exec php-apache /usr/local/bin/install-ross.sh; then
        echo "✅ Instalação PHP concluída!"
    else
        echo "❌ Erro na instalação PHP. Verificando logs..."
        docker logs php-apache --tail=20
        exit 1
    fi
    
    echo "✅ Instalação completa concluída!"
else
    echo "📦 Vendor encontrado. Apenas iniciando containers..."
    docker-compose up -d
fi

# Verificar status
echo "🔍 Verificando status dos containers..."
docker-compose ps

echo ""
echo "✅ Sistema ROSS instalado e rodando!"
echo ""
echo "🌐 Serviços disponíveis:"
echo "  - ROSS (PHP): http://localhost:8080"
echo "  - N8N: http://localhost:5678"
echo "  - PostgreSQL: localhost:5432"
echo ""
echo "📋 Comandos úteis:"
echo "  - Parar: docker-compose down"
echo "  - Ver logs: docker-compose logs -f"
echo "  - Migrar banco: ./migrate.sh"

