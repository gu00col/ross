# Models - Sistema de Análise Contratual

Este diretório contém todos os models da aplicação, baseados na estrutura do banco de dados PostgreSQL com pgvector.

## 📁 Estrutura dos Models

### BaseModel.php
Model base que fornece funcionalidades comuns para todos os models:
- CRUD básico (Create, Read, Update, Delete)
- Validação de dados
- Filtros e paginação
- Transações de banco de dados

### User.php
Model para gerenciar usuários do sistema:
- **Campos**: id (UUID), nome, email, password, active, is_superuser, created_at, updated_at
- **Recursos especiais**:
  - Criptografia automática de senhas
  - Verificação de email único
  - Ativação/desativação de usuários
  - Busca por email
  - Verificação de senha
  - Gestão de superusuários

### Contract.php
Model principal para gerenciar contratos:
- **Campos**: id, user_id, original_filename, storage_path, raw_text, text_embedding, status, analyzed_at, created_at
- **Status**: pending, processing, processed, error
- **Recursos especiais**:
  - Busca semântica com pgvector
  - Atualização de embeddings
  - Análise completa por contrato
  - Estatísticas de status
  - Busca por usuário
  - Estatísticas por usuário

### AnalysisDataPoint.php
Model para pontos de análise da IA:
- **Campos**: id, contract_id, section_id, display_order, label, content, details, created_at
- **Seções**:
  - 1: Dados Essenciais
  - 2: Riscos e Cláusulas
  - 3: Brechas e Inconsistências
  - 4: Parecer Final
- **Recursos especiais**:
  - Agrupamento por seção
  - Validação de JSON details
  - Criação em lote

### ContractView.php
Model para acessar views do banco de dados:
- **v_contracts_summary**: Resumo de contratos com contagem de pontos
- **v_contract_analysis**: Análise completa agrupada
- **Recursos especiais**:
  - Estatísticas gerais
  - Tendências mensais
  - Contratos com problemas
  - Exportação de dados

### Report.php
Model para relatórios e estatísticas:
- Dashboard principal
- Relatórios de qualidade
- Performance de processamento
- Análise por seção
- Exportação completa

## 🚀 Como Usar

### Exemplo Básico
```php
<?php
require_once 'app/Models/index.php';

// Usar o helper Models
$user = Models::user();
$contract = Models::contract();
$analysis = Models::analysisDataPoint();

// Criar novo usuário
$newUser = $user->create([
    'nome' => 'João Silva',
    'email' => 'joao@exemplo.com',
    'password' => 'senha123',
    'is_superuser' => false
]);

// Verificar login
$loggedUser = $user->verifyPassword('joao@exemplo.com', 'senha123');

// Buscar contratos do usuário
$userContracts = $contract->getByUser($loggedUser['id']);

// Buscar todos os contratos
$contracts = $contract->getAll();

// Buscar contrato por ID
$contractData = $contract->findById('12345');

// Criar novo contrato
$newContract = $contract->create([
    'id' => 'uuid-12345',
    'user_id' => $loggedUser['id'],
    'original_filename' => 'contrato.pdf',
    'status' => 'pending'
]);
```

### Busca Semântica
```php
// Buscar contratos similares usando embedding
$embedding = [0.1, 0.2, 0.3, ...]; // Array de 1536 dimensões
$similar = $contract->semanticSearch($embedding, 10, 0.7);

// Atualizar embedding de um contrato
$contract->updateEmbedding('12345', $embedding);
```

### Análise de Contratos
```php
// Obter análise completa de um contrato
$analysis = $contract->getFullAnalysis('12345');

// Obter pontos agrupados por seção
$grouped = $analysis->getGroupedBySection('12345');

// Criar múltiplos pontos de análise
$points = [
    [
        'contract_id' => '12345',
        'section_id' => 1,
        'label' => 'Partes Envolvidas',
        'content' => 'Contratante: Empresa XYZ...'
    ],
    // ... mais pontos
];
$analysis->createMultiple($points);
```

### Relatórios
```php
// Obter dashboard completo
$dashboard = Models::report()->getDashboard();

// Obter estatísticas gerais
$stats = Models::report()->getGeneralStats();

// Obter tendências mensais
$trends = Models::report()->getMonthlyTrends(12);

// Exportar relatório completo
$export = Models::report()->exportFullReport([
    'status' => 'processed',
    'date_from' => '2024-01-01'
]);
```

## 🔧 Configuração

### Banco de Dados
Os models usam a classe `Database` configurada em `config/database.php`:
- Host: pgverctor
- Database: ross
- Extensão: pgvector habilitada

### Validação
Todos os models incluem validação automática:
- Campos obrigatórios
- Tipos de dados
- Valores permitidos para status e seções
- Validação de JSON para details

### Transações
Suporte completo a transações:
```php
$contract->beginTransaction();
try {
    $contract->create($data);
    $analysis->createMultiple($points);
    $contract->commit();
} catch (Exception $e) {
    $contract->rollback();
}
```

## 📊 Recursos Especiais

### pgvector
- Busca semântica com embeddings
- Índices otimizados para consultas vetoriais
- Suporte a 1536 dimensões (OpenAI embeddings)

### Views do Banco
- `v_contracts_summary`: Resumo com contagens
- `v_contract_analysis`: Análise completa agrupada

### Relatórios Avançados
- Dashboard em tempo real
- Tendências mensais
- Análise de qualidade
- Performance de processamento

## 🛠️ Manutenção

### Adicionar Novo Model
1. Criar arquivo em `app/Models/`
2. Estender `BaseModel`
3. Definir `$table` e `$fillable`
4. Adicionar métodos específicos
5. Atualizar `index.php`

### Atualizar Validação
1. Modificar método `validate()` no model
2. Adicionar regras específicas
3. Testar com dados inválidos

### Otimizar Consultas
1. Usar índices apropriados
2. Implementar paginação
3. Usar views para consultas complexas
4. Monitorar performance

## 📝 Notas Importantes

- Todos os models usam prepared statements
- Validação automática de dados
- Suporte completo a pgvector
- Transações para operações complexas
- Documentação JSDoc completa
- Tratamento de erros robusto
