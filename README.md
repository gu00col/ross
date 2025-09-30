# Ross - Analista Jurídico de Contratos com IA

Sistema de análise contratual com inteligência artificial que analisa contratos, gera relatórios e recomendações práticas para negociação. Projeto open-source e hospedado.

## 🚀 Instalação

### Pré-requisitos

- Docker e Docker Compose instalados
- Git
- Portas 8080, 5432 e 5678 disponíveis

### 1. Clone o repositório

```bash
git clone https://github.com/gu00col/ross.git
cd ross
```

### 2. Configure as variáveis de ambiente

#### Arquivo `.env` do Docker (dockers/.env)

```bash
cp dockers/.env.example dockers/.env
```

**Configurações principais:**
- `POSTGRES_PASSWORD`: Senha do banco PostgreSQL
- `N8N_BASIC_AUTH_PASSWORD`: Senha do N8N
- `POSTGRES_DATA_PATH`: Caminho para dados do PostgreSQL
- `N8N_DATA_PATH`: Caminho para dados do N8N

#### Arquivo `.env` da aplicação (public_html/.env)

```bash
cp public_html/.env.example public_html/.env
```

**Configurações importantes:**
- `DB_HOST`: Nome do container PostgreSQL (padrão: `pgverctor`)
- `DB_PASSWORD`: Deve ser igual ao `POSTGRES_PASSWORD` do Docker
- `ADMIN_EMAIL`: Email do usuário administrador
- `ADMIN_PASSWORD`: Senha do usuário administrador
- `ADMIN_NAME`: Nome do usuário administrador

### 3. Inicie os containers

```bash
cd dockers
docker-compose up -d
```

### 4. Instale as dependências PHP

```bash
docker exec -it php-apache bash /var/www/html/dockers/php-apache/install.sh
```

### 5. Execute as migrações do banco

```bash
cd dockers
./migrate.sh
```

## 🔧 Configuração do Sistema

### Primeiro Acesso

1. **Acesse o sistema**: http://localhost:8080
2. **Login automático**: Na primeira execução, o sistema criará automaticamente um usuário administrador com as credenciais do arquivo `.env`
3. **Credenciais padrão** (se não configuradas):
   - Email: `teste@gmail.com`
   - Senha: `teste123`

### Configuração do N8N

1. **Acesse o N8N**: http://localhost:5678
2. **Credenciais**:
   - Usuário: `admin`
   - Senha: `admin123` (ou conforme configurado no `.env`)

### Importação dos Fluxos N8N

Os fluxos de automação ficam na pasta `n8n-fluxos/` e devem ser importados manualmente:

1. Acesse o N8N
2. Vá em **Workflows** → **Import from File**
3. Selecione os arquivos JSON da pasta `n8n-fluxos/`
4. Configure as credenciais necessárias em cada fluxo

**Fluxos disponíveis:**
- `contrato.json`: Análise de contratos
- `contratos-ia-processing.json`: Processamento com IA
- *(Outros fluxos serão adicionados conforme desenvolvimento)*

## 📁 Estrutura do Projeto

```
ross/
├── dockers/                 # Configurações Docker
│   ├── docker-compose.yml  # Orquestração dos containers
│   ├── .env.example        # Variáveis de ambiente Docker
│   ├── migrate.sh          # Script de migração do banco
│   ├── postgresql/         # Configurações PostgreSQL
│   ├── php-apache/         # Configurações PHP/Apache
│   ├── redis/              # Configurações Redis
│   └── n8n/                # Configurações N8N
├── public_html/            # Aplicação PHP principal
│   ├── App/                # Código da aplicação
│   ├── Assets/             # CSS, JS, imagens
│   ├── .env.example        # Variáveis de ambiente da aplicação
│   └── index.php           # Ponto de entrada
├── n8n-fluxos/             # Fluxos de automação N8N
├── landingpage/            # Site institucional
└── README.md               # Esta documentação
```

## 🗄️ Banco de Dados

### Estrutura Principal

- **users**: Usuários do sistema
- **contracts**: Contratos analisados
- **analysis_sections**: Seções de análise
- **analysis_data_points**: Pontos de dados das análises

### Extensões PostgreSQL

- **pgvector**: Para embeddings de IA
- **pgcrypto**: Para criptografia

### Migrações

As migrações são executadas automaticamente no `init.sql` do PostgreSQL. Para executar manualmente:

```bash
cd dockers
./migrate.sh
```

## 🔄 Comandos Úteis

### Gerenciamento dos Containers

```bash
# Iniciar todos os serviços
docker-compose up -d

# Parar todos os serviços
docker-compose down

# Ver logs
docker-compose logs -f

# Reiniciar um serviço específico
docker-compose restart php-apache
```

### Acesso aos Containers

```bash
# Acessar container PHP
docker exec -it php-apache bash

# Acessar banco PostgreSQL
docker exec -it pgverctor psql -U postgres -d ross

# Acessar Redis
docker exec -it redis redis-cli
```

### Backup e Restore

```bash
# Backup do banco
docker exec pgverctor pg_dump -U postgres ross > backup.sql

# Restore do banco
docker exec -i pgverctor psql -U postgres ross < backup.sql
```

## 🌐 URLs de Acesso

- **Sistema Principal**: http://localhost:8080
- **N8N (Automação)**: http://localhost:5678
- **Landing Page**: http://localhost:8080/landingpage/

## 🔐 Segurança

### Configurações de Senha

- Mínimo 8 caracteres
- Obrigatório: maiúscula, minúscula, números
- Opcional: símbolos

### Rate Limiting

- API: 60 requisições por minuto
- Login: 5 tentativas antes do bloqueio
- Bloqueio: 15 minutos

## 🐛 Troubleshooting

### Problemas Comuns

1. **Erro de conexão com banco**:
   - Verifique se o container PostgreSQL está rodando
   - Confirme as credenciais no `.env`

2. **Erro 404 no sistema**:
   - Verifique se o Apache está configurado corretamente
   - Execute o script de instalação PHP

3. **N8N não conecta ao banco**:
   - Verifique as variáveis `N8N_DB_*` no `.env`
   - Reinicie o container N8N

### Logs

```bash
# Logs da aplicação
docker exec php-apache tail -f /var/log/apache2/error.log

# Logs do PostgreSQL
docker logs pgverctor

# Logs do N8N
docker logs n8n
```

## 📝 Desenvolvimento

### Estrutura da Aplicação

- **Framework**: PHP puro com arquitetura MVC
- **Banco**: PostgreSQL com extensões pgvector e pgcrypto
- **Cache**: Redis
- **Automação**: N8N
- **Frontend**: Bootstrap 5 + JavaScript

### Adicionando Novos Fluxos N8N

1. Crie o fluxo no N8N
2. Exporte como JSON
3. Salve na pasta `n8n-fluxos/`
4. Documente o fluxo no README

## 📄 Licença

Este projeto é open-source. Consulte o arquivo LICENSE para mais detalhes.

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📞 Suporte

Para suporte técnico ou dúvidas:
- Abra uma issue no GitHub
- Consulte a documentação dos fluxos N8N
- Verifique os logs do sistema

---

**Ross - Analista Jurídico de Contratos com IA**  
*Sistema desenvolvido para análise contratual inteligente* 