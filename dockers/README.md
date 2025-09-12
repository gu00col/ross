# ROSS - Analista Jurídico (Docker)

Sistema de Análise Contratual Automatizada usando Docker e tecnologias modernas.

## 🚀 Instalação Rápida

### Instalação Completa
```bash
cd dockers
./install.sh
```

### Migração do Banco (Manual)
```bash
cd dockers
./migrate.sh
```

## ⚙️ Como Funciona

### **install.sh** (Principal)
- **Instala Docker Compose** se não existir
- **Verifica se vendor existe** antes de instalar
- **Executa docker-compose** automaticamente
- **Instala PHP** apenas se necessário (sem vendor)
- **Nunca executa migrações** automaticamente

### **migrate.sh** (Manual)
- **Verifica containers** rodando
- **Testa conexão** com banco
- **Lista tabelas existentes**
- **Pede confirmação** se banco tem dados
- **Executa migrações** com segurança

### **Dockerfile PHP**
- **Executa install.sh** automaticamente no container
- **Só instala** se vendor não existir
- **Preserva alterações** manuais do usuário

## 🏗️ Arquitetura do Sistema

### Containers
- **php-apache**: Servidor web PHP 8.2 + Apache
- **postgresql**: Banco de dados PostgreSQL com pgvector
- **redis**: Cache e sessões
- **n8n**: Automação de workflows

### Estrutura de Pastas
```
dockers/
├── docker-compose.yml          # Orquestração dos containers
├── install-all.sh             # Script de instalação completa
├── php-apache/
│   ├── install.sh             # Script de instalação do PHP
│   └── docker-compose.yml     # Configuração específica do PHP
├── postgresql/
│   └── init.sql               # Script de inicialização do banco
├── n8n/
│   └── data/                  # Dados do N8N
└── redis/
    └── data/                  # Dados do Redis
```

## 🔧 Configuração

### Variáveis de Ambiente
As configurações estão no arquivo `../public_html/.env`:

```env
# Banco de Dados
DB_HOST=postgresql
DB_DATABASE=ross
DB_USERNAME=postgres
DB_PASSWORD=postgres123

# Cache e Sessões
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Aplicação
APP_URL=http://localhost:8080
```

### Portas
- **8080**: ROSS (PHP/Apache)
- **5432**: PostgreSQL
- **5678**: N8N
- **6379**: Redis (interno)

## 📋 Comandos Úteis

### Gerenciamento de Containers
```bash
# Iniciar todos os containers
docker-compose up -d

# Parar todos os containers
docker-compose down

# Reiniciar containers
docker-compose restart

# Ver logs
docker-compose logs -f

# Ver logs de um container específico
docker-compose logs -f php-apache
```

### Comandos Úteis
```bash
# Acessar container PHP
docker exec -it php-apache bash

# Ver logs
docker-compose logs -f

# Parar sistema
docker-compose down

# Reinstalar (remove vendor primeiro)
rm -rf ../public_html/vendor
./install.sh
```

### Comandos no Banco de Dados
```bash
# Acessar PostgreSQL
docker exec -it pgverctor psql -U postgres -d ross

# Backup do banco
docker exec pgverctor pg_dump -U postgres ross > backup.sql

# Restaurar backup
docker exec -i pgverctor psql -U postgres -d ross < backup.sql
```

## 🛠️ Desenvolvimento

### Estrutura do Projeto
```
public_html/
├── app/                       # Código da aplicação
│   ├── Controllers/           # Controladores
│   ├── Models/               # Modelos
│   ├── Services/             # Serviços
│   ├── Middleware/           # Middlewares
│   └── Config/               # Configurações
├── assets/                   # Assets estáticos
├── storage/                  # Arquivos de armazenamento
├── .env                      # Variáveis de ambiente
├── composer.json             # Dependências PHP
└── index.php                 # Ponto de entrada
```

### Tecnologias Utilizadas
- **PHP 8.2**: Linguagem principal
- **Composer**: Gerenciamento de dependências
- **League Route**: Sistema de roteamento
- **PostgreSQL**: Banco de dados principal
- **Redis**: Cache e sessões
- **Apache**: Servidor web
- **Docker**: Containerização

## 🔍 Troubleshooting

### Problemas Comuns

#### Container PHP não inicia
```bash
# Verificar logs
docker-compose logs php-apache

# Reconstruir container
docker-compose up -d --build php-apache
```

#### Erro de permissões
```bash
# Corrigir permissões
docker exec -it php-apache chmod -R 777 /var/www/html/storage/
```

#### Banco de dados não conecta
```bash
# Verificar se PostgreSQL está rodando
docker-compose ps postgresql

# Verificar logs do banco
docker-compose logs postgresql
```

#### Mod_rewrite não funciona
```bash
# Habilitar mod_rewrite
docker exec -it php-apache a2enmod rewrite
docker exec -it php-apache service apache2 restart
```

### Logs e Debug
```bash
# Ver todos os logs
docker-compose logs

# Ver logs em tempo real
docker-compose logs -f

# Ver logs de erro do PHP
docker exec -it php-apache tail -f /var/log/apache2/error.log
```

## 📊 Monitoramento

### Status dos Containers
```bash
# Ver status
docker-compose ps

# Ver uso de recursos
docker stats
```

### Health Checks
```bash
# Verificar PHP
curl http://localhost:8080

# Verificar N8N
curl http://localhost:5678

# Verificar PostgreSQL
docker exec -it pgverctor pg_isready
```

## 🚀 Deploy em Produção

### 1. Configurar Variáveis de Produção
```bash
# Editar .env
APP_ENV=production
APP_DEBUG=false
DB_PASSWORD=senha_forte_aqui
JWT_SECRET=chave_secreta_forte_aqui
```

### 2. Otimizar Containers
```bash
# Instalar dependências de produção
docker exec -it php-apache composer install --optimize-autoloader --no-dev

# Limpar cache
docker exec -it php-apache php artisan cache:clear
```

### 3. Backup
```bash
# Backup do banco
docker exec pgverctor pg_dump -U postgres ross > backup_$(date +%Y%m%d).sql

# Backup dos volumes
docker run --rm -v ross_postgresql_data:/data -v $(pwd):/backup alpine tar czf /backup/postgresql_backup.tar.gz -C /data .
```

## 📚 Documentação Adicional

- [Composer](https://getcomposer.org/doc/)
- [League Route](https://route.thephpleague.com/)
- [PostgreSQL](https://www.postgresql.org/docs/)
- [Redis](https://redis.io/documentation)
- [Docker](https://docs.docker.com/)

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.