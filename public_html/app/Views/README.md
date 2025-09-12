# Layout Principal do Sistema

Este diretório contém o layout principal e exemplos de uso para páginas internas do sistema ROSS.

## Arquivos

- `layout.php` - Layout principal com sidebar fixa
- `example_page.php` - Exemplo de página usando o layout
- `README.md` - Esta documentação

## Como Usar

### 1. Página Simples

```php
<?php
// Configurar dados da página
$page_title = "Minha Página";
$page_subtitle = "Descrição da página";

// Conteúdo da página
ob_start();
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5>Conteúdo da página</h5>
                <p>Seu conteúdo aqui...</p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

// Incluir o layout
include 'layout.php';
?>
```

### 2. Página com Breadcrumbs

```php
<?php
$page_title = "Contratos";
$page_subtitle = "Gerenciar contratos";
$breadcrumbs = [
    ['title' => 'Contratos', 'url' => '/contratos'],
    ['title' => 'Detalhes', 'url' => null]
];

ob_start();
?>
<!-- Seu conteúdo aqui -->
<?php
$content = ob_get_clean();
include 'layout.php';
?>
```

### 3. Página com CSS/JS Customizados

```php
<?php
$page_title = "Página Customizada";
$page_css = ['assets/css/custom.css'];
$page_js = ['assets/js/custom.js'];

ob_start();
?>
<!-- Seu conteúdo aqui -->
<?php
$content = ob_get_clean();
include 'layout.php';
?>
```

## Variáveis Disponíveis

### Obrigatórias
- `$content` - Conteúdo HTML da página (gerado com ob_start/ob_get_clean)

### Opcionais
- `$page_title` - Título da página (padrão: "ROSS - Analista Jurídico")
- `$page_subtitle` - Subtítulo da página (padrão: "Sistema de análise jurídica")
- `$breadcrumbs` - Array de breadcrumbs
- `$page_css` - Array de arquivos CSS adicionais
- `$page_js` - Array de arquivos JS adicionais

## Estrutura do Layout

```
┌─────────────────────────────────────────┐
│ Sidebar (280px) │ Main Content          │
│                 │ ┌─────────────────────┐│
│ - Logo          │ │ Page Header         ││
│ - Menu          │ │ - Title             ││
│ - User Info     │ │ - Breadcrumbs       ││
│ - Logout        │ └─────────────────────┘│
│                 │ ┌─────────────────────┐│
│                 │ │ Page Content        ││
│                 │ │ - Your content here ││
│                 │ └─────────────────────┘│
└─────────────────────────────────────────┘
```

## CSS Classes Disponíveis

### Cards
- `.card` - Card básico
- `.card-header` - Cabeçalho do card
- `.card-body` - Corpo do card
- `.card-title` - Título do card

### Botões
- `.btn-primary` - Botão primário (azul ROSS)
- `.btn-secondary` - Botão secundário (beige ROSS)
- `.btn-outline-primary` - Botão outline

### Alertas
- `.alert` - Alerta básico
- `.alert-primary` - Alerta primário
- `.alert-success` - Alerta de sucesso
- `.alert-warning` - Alerta de aviso
- `.alert-danger` - Alerta de erro

### Badges
- `.badge` - Badge básico
- `.badge-primary` - Badge primário
- `.badge-success` - Badge de sucesso
- `.badge-warning` - Badge de aviso
- `.badge-danger` - Badge de erro

## JavaScript Disponível

### Funções Globais
- `showToast(message, type, duration)` - Mostrar notificação
- `confirmAction(message, callback)` - Confirmar ação
- `updatePageTitle(title, subtitle)` - Atualizar título
- `addBreadcrumb(title, url)` - Adicionar breadcrumb
- `clearBreadcrumbs()` - Limpar breadcrumbs
- `showConfirmModal(title, message)` - Modal de confirmação

### Exemplo de Uso do JavaScript

```javascript
// Mostrar notificação
showToast('Operação realizada com sucesso!', 'success');

// Confirmar ação
confirmAction('Tem certeza?', function() {
    // Ação a ser executada
});

// Atualizar título
updatePageTitle('Nova Página', 'Descrição da página');

// Adicionar breadcrumb
addBreadcrumb('Nova Seção', '/nova-secao');
```

## Responsividade

O layout é totalmente responsivo:
- **Desktop**: Sidebar fixa à esquerda
- **Mobile**: Sidebar oculta com botão de menu
- **Tablet**: Adaptação automática

## Páginas que NÃO usam este Layout

- `landing.php` - Página inicial (independente)
- `login.php` - Página de login (independente)
- `password_recovery.php` - Recuperação de senha (independente)
- Páginas de erro (404, 403, 500) - Independentes
