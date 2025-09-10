# 🐳 Docker Compose - Projeto ROSS

Este diretório contém a configuração Docker Compose para o projeto ROSS, incluindo N8N, PHP-Apache, PostgreSQL e Redis.

## 📁 Estrutura

```
dockers/
├── docker-compose.yml          # Compose principal (orquestra todos os serviços)
├── .env.example               # Variáveis de ambiente principais
├── n8n/
│   ├── docker-compose.yml     # Compose específico do N8N
│   ├── .env.example          # Variáveis do N8N
│   └── data/                 # Volume de dados do N8N
├── php-apache/
│   ├── docker-compose.yml     # Compose específico do PHP-Apache
│   └── .env.example          # Variáveis do PHP-Apache
├── postgresql/
│   ├── docker-compose.yml     # Compose específico do PostgreSQL
│   ├── .env.example          # Variáveis do PostgreSQL
│   └── data/                 # Volume de dados do PostgreSQL
└── redis/
    ├── docker-compose.yml     # Compose específico do Redis
    └── .env.example          # Variáveis do Redis
```

## 🚀 Como Usar

### 1. Configuração Inicial

1. **Copie os arquivos de exemplo:**
   ```bash
   # Na pasta principal
   cp .env.example .env
   
   # Em cada subpasta (opcional para uso individual)
   cp n8n/.env.example n8n/.env
   cp php-apache/.env.example php-apache/.env
   cp postgresql/.env.example postgresql/.env
   cp redis/.env.example redis/.env
   ```

2. **Edite as variáveis conforme necessário:**
   ```bash
   nano .env
   ```

### 2. Executando os Serviços

#### Opção A: Todos os serviços juntos (Recomendado)
```bash
cd /home/luisoliveira/desenvolvimento/ross/dockers
docker-compose up -d
```

#### Opção B: Serviços individuais
```bash
# N8N
cd n8n && docker-compose up -d

# PHP-Apache
cd php-apache && docker-compose up -d

# PostgreSQL
cd postgresql && docker-compose up -d

# Redis
cd redis && docker-compose up -d
```

### 3. Comandos Úteis

```bash
# Ver logs
docker-compose logs -f

# Parar todos os serviços
docker-compose down

# Parar e remover volumes
docker-compose down -v

# Rebuild dos serviços
docker-compose up -d --build

# Ver status dos containers
docker-compose ps
```

## 🌐 Acessos

| Serviço | URL | Credenciais |
|---------|-----|-------------|
| **N8N** | http://localhost:5678 | admin / admin123 |
| **PHP-Apache** | http://localhost:8080 | - |
| **PostgreSQL** | localhost:5432 | postgres / postgres123 |
| **Redis** | Interno apenas | - |

## ⚙️ Configurações Importantes

### Variáveis de Ambiente Principais

- **POSTGRES_DB**: Nome do banco de dados (padrão: ross)
- **POSTGRES_USER**: Usuário do PostgreSQL (padrão: postgres)
- **POSTGRES_PASSWORD**: Senha do PostgreSQL (padrão: postgres123)
- **N8N_BASIC_AUTH_USER**: Usuário do N8N (padrão: admin)
- **N8N_BASIC_AUTH_PASSWORD**: Senha do N8N (padrão: admin123)
- **PHP_APACHE_PORT**: Porta do Apache (padrão: 8080)
- **N8N_PORT**: Porta do N8N (padrão: 5678)

### Volumes

- **PostgreSQL**: `./postgresql/data` → `/var/lib/postgresql/data`
- **N8N**: `./n8n/data` → `/home/node/.n8n`
- **PHP**: `../public_html` → `/var/www/html`
- **Redis**: Volume interno para persistência

## 🔧 Troubleshooting

### Problema: Porta já em uso
```bash
# Verificar qual processo está usando a porta
sudo netstat -tulpn | grep :5432

# Parar o processo ou alterar a porta no .env
```

### Problema: Permissões de volume
```bash
# Ajustar permissões
sudo chown -R 999:999 postgresql/data
sudo chown -R 1000:1000 n8n/data
```

### Problema: Rede não encontrada
```bash
# Criar a rede manualmente
docker network create ross-network
```

## 📝 Notas

- O Redis não expõe porta externa por segurança
- Todos os serviços estão conectados na rede `ross-network`
- O N8N está configurado para usar PostgreSQL como banco de dados
- Os volumes são persistentes, mantendo dados entre reinicializações
