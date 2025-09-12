# 🏗️ Sistema de Análise Contratual - Estrutura MVC

Este projeto implementa uma arquitetura MVC (Model-View-Controller) para o sistema de análise contratual automatizada.

## 📁 Estrutura do Projeto

```
public_html/
├── app/                          # Código da aplicação
│   ├── Controllers/              # Controladores
│   │   ├── HomeController.php    # Controller da página inicial
│   │   ├── AuthController.php    # Controller de autenticação
│   │   ├── ContractController.php # Controller de contratos
│   │   └── UserController.php    # Controller de usuários
│   ├── Models/                   # Modelos de dados
│   │   ├── Contract.php          # Model de contrato
│   │   ├── User.php              # Model de usuário
│   │   └── AnalysisPoint.php     # Model de ponto de análise
│   ├── Views/                    # Views (templates)
│   │   ├── layouts/              # Layouts base
│   │   │   └── main.php          # Layout principal
│   │   ├── partials/             # Partiais reutilizáveis
│   │   │   ├── header.php        # Cabeçalho
│   │   │   ├── footer.php        # Rodapé
│   │   │   └── sidebar.php       # Barra lateral
│   │   ├── errors/               # Páginas de erro
│   │   │   ├── 404.php           # Página não encontrada
│   │   │   └── 500.php           # Erro interno
│   │   └── home/                 # Views da página inicial
│   │       └── index.php         # Página inicial
│   ├── Core/                     # Classes principais
│   │   ├── Application.php       # Classe principal da aplicação
│   │   ├── Controller.php        # Controller base
│   │   ├── Database.php          # Conexão com banco
│   │   ├── Redis.php             # Conexão com Redis
│   │   ├── Session.php           # Gerenciamento de sessão
│   │   ├── Cache.php             # Sistema de cache
│   │   ├── Logger.php            # Sistema de log
│   │   └── Router.php            # Sistema de roteamento
│   └── Helpers/                  # Funções auxiliares
│       ├── functions.php         # Funções globais
│       └── validators.php        # Validadores
├── config/                       # Configurações
│   ├── config.php                # Configuração principal
│   ├── app.php                   # Configurações da aplicação
│   ├── database.php              # Configurações do banco
│   └── redis.php                 # Configurações do Redis
├── storage/                      # Armazenamento
│   ├── logs/                     # Logs da aplicação
│   ├── cache/                    # Cache da aplicação
│   ├── sessions/                 # Sessões (se usar arquivo)
│   └── uploads/                  # Uploads de arquivos
├── public/                       # Arquivos públicos
│   ├── assets/                   # Assets estáticos
│   │   ├── css/                  # Estilos CSS
│   │   ├── js/                   # Scripts JavaScript
│   │   ├── images/               # Imagens
│   │   └── fonts/                # Fontes
│   └── uploads/                  # Uploads públicos
├── .env.example                  # Exemplo de variáveis de ambiente
├── .env                          # Variáveis de ambiente (não versionado)
├── index.php                     # Ponto de entrada da aplicação
└── README.md                     # Este arquivo
```

## ⚙️ Configuração

### 1. **Variáveis de Ambiente**
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar configurações
nano .env
```

### 2. **Configurações Principais**
- **Banco de Dados**: PostgreSQL (padrão) ou MySQL
- **Cache**: Redis (padrão) ou arquivo
- **Sessão**: Redis (padrão) ou arquivo
- **Logs**: Arquivo (padrão) ou diário

### 3. **Dependências**
- PHP 8.0+
- PostgreSQL 15+ ou MySQL 8.0+
- Redis 6.0+
- Extensões PHP: PDO, Redis, JSON, OpenSSL

## 🚀 Como Usar

### 1. **Inicializar Aplicação**
```php
// No arquivo index.php
$app = \App\Core\Application::getInstance();
$app->run();
```

### 2. **Criar Controller**
```php
<?php
namespace App\Controllers;

use App\Core\Controller;

class MeuController extends Controller
{
    public function index()
    {
        $data = ['title' => 'Minha Página'];
        $this->viewWithLayout('minha/view', $data);
    }
}
```

### 3. **Criar Model**
```php
<?php
namespace App\Models;

use App\Core\Controller;

class MeuModel extends Controller
{
    protected $table = 'minha_tabela';
    
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }
}
```

### 4. **Criar View**
```php
<!-- app/Views/minha/view.php -->
<div class="container">
    <h1><?php echo $title; ?></h1>
    <p>Conteúdo da página</p>
</div>
```

## 🔧 Funcionalidades

### **Sistema de Roteamento**
- Rotas baseadas em método HTTP e URI
- Suporte a parâmetros dinâmicos
- Middleware (em desenvolvimento)

### **Sistema de Banco de Dados**
- Suporte a PostgreSQL e MySQL
- Query builder simples
- Transações
- Prepared statements

### **Sistema de Cache**
- Cache em Redis
- TTL configurável
- Serialização automática

### **Sistema de Sessão**
- Sessões em Redis
- Flash messages
- Configurações de segurança

### **Sistema de Log**
- Logs em arquivo
- Rotação diária
- Diferentes níveis de log

## 📝 Exemplos de Uso

### **Controller com Validação**
```php
public function create()
{
    $data = $this->getRequestData();
    $errors = $this->validate($data, [
        'name' => 'required|min:3',
        'email' => 'required|email'
    ]);
    
    if (!empty($errors)) {
        $this->json(['errors' => $errors], 400);
    }
    
    // Criar registro
    $id = $this->db->insert('users', $data);
    $this->json(['id' => $id], 201);
}
```

### **Model com Relacionamentos**
```php
public function getWithAnalysis($id)
{
    $sql = "
        SELECT c.*, 
               COUNT(ap.id) as analysis_count
        FROM contracts c
        LEFT JOIN analysis_data_points ap ON c.id = ap.contract_id
        WHERE c.id = :id
        GROUP BY c.id
    ";
    
    return $this->db->fetch($sql, ['id' => $id]);
}
```

### **View com Layout**
```php
// Controller
$this->viewWithLayout('contracts/index', [
    'contracts' => $contracts,
    'title' => 'Lista de Contratos'
]);

// View: app/Views/contracts/index.php
<div class="row">
    <?php foreach ($contracts as $contract): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $contract['original_filename']; ?></h5>
                    <p class="card-text">Status: <?php echo $contract['status']; ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

## 🔒 Segurança

### **Validação de Dados**
- Validação automática em controllers
- Sanitização de inputs
- Prepared statements

### **Sessões Seguras**
- Cookies HTTPOnly
- Regeneração de ID
- Timeout configurável

### **Logs de Segurança**
- Log de tentativas de login
- Log de erros
- Log de ações sensíveis

## 📊 Monitoramento

### **Logs**
- Logs de aplicação em `storage/logs/`
- Rotação automática
- Diferentes níveis

### **Cache**
- Estatísticas de cache
- Limpeza automática
- TTL configurável

### **Banco de Dados**
- Logs de queries (em desenvolvimento)
- Monitoramento de conexões
- Backup automático (em desenvolvimento)

## 🚀 Deploy

### **Produção**
1. Configurar variáveis de ambiente
2. Desabilitar debug mode
3. Configurar HTTPS
4. Configurar backup do banco
5. Configurar monitoramento

### **Docker**
```bash
# Usar com Docker Compose
docker-compose up -d
```

## 📚 Documentação Adicional

- [Configuração do Banco](config/database.php)
- [Configuração do Redis](config/redis.php)
- [Sistema de Roteamento](app/Core/Router.php)
- [Sistema de Cache](app/Core/Cache.php)

---

**Versão**: 1.0.0  
**Última atualização**: Janeiro 2025  
**Compatibilidade**: PHP 8.0+
