# Sistema de Importação de CSS - ROSS Analista Jurídico

## Visão Geral

O sistema ROSS utiliza um mecanismo automático de carregamento de CSS baseado no **nome do controller**, proporcionando organização e modularidade no desenvolvimento frontend.

## Como Funciona

### 1. Estrutura do Sistema

```
public_html/
├── app/
│   ├── Controllers/
│   │   ├── ContractController.php    # Controller para /contract/{id}
│   │   ├── ContractsController.php   # Controller para /contracts
│   │   └── DashboardController.php   # Controller para /home
│   └── Views/
│       └── layout.php                # Layout principal
├── assets/
│   ├── css/
│   │   ├── contract.css             # CSS para ContractController
│   │   ├── contracts.css            # CSS para ContractsController
│   │   ├── dashboard.css            # CSS para DashboardController
│   │   └── layout.css               # CSS base do layout
│   └── js/
│       ├── contract.js              # JS para ContractController
│       └── layout.js                # JS base do layout
└── contract.php                     # View incluída pelo ContractController
```

### 2. Fluxo de Carregamento

1. **Requisição**: Usuário acessa `/contract/123`
2. **Roteamento**: Sistema identifica `ContractController`
3. **Controller**: `ContractController::show()` processa a requisição
4. **Layout**: `layout.php` é carregado com o conteúdo
5. **CSS Automático**: Sistema carrega `assets/css/contract.css` automaticamente

### 3. Mapeamento Controller → CSS

O arquivo `app/Views/layout.php` contém a lógica de mapeamento:

```php
/**
 * Função para detectar o controller atual baseado na URL
 */
function getControllerName() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);
    
    // Mapear rotas para controllers
    $routeMap = [
        '/home' => 'dashboard',
        '/dashboard' => 'dashboard', 
        '/contracts' => 'contracts',
        '/contract' => 'contract',        # ← /contract/123 → contract.css
        '/profile' => 'profile',
        '/settings' => 'settings'
    ];
    
    return $routeMap[$path] ?? 'dashboard';
}
```

### 4. Carregamento Automático de CSS

```php
<!-- CSS específico da página (se existir) -->
<?php if (isset($page_css)): ?>
    <?php foreach ($page_css as $css): ?>
        <link href="<?php echo htmlspecialchars($css); ?>" rel="stylesheet">
    <?php endforeach; ?>
<?php else: ?>
    <!-- CSS automático baseado no controller -->
    <?php
    $controller_name = getControllerName();
    $css_file = "assets/css/{$controller_name}.css";
    if (file_exists($css_file)):
    ?>
        <link href="<?php echo $css_file; ?>" rel="stylesheet">
    <?php endif; ?>
<?php endif; ?>
```

## Convenções de Nomenclatura

### Controllers
- **Arquivo**: `app/Controllers/ContractController.php`
- **Rota**: `/contract/{id}`
- **CSS**: `assets/css/contract.css`
- **JS**: `assets/js/contract.js`

### Exemplos
| Controller | Rota | CSS | JavaScript |
|------------|------|-----|------------|
| `ContractController` | `/contract/123` | `contract.css` | `contract.js` |
| `ContractsController` | `/contracts` | `contracts.css` | `contracts.js` |
| `DashboardController` | `/home` | `dashboard.css` | `dashboard.js` |

## Ordem de Carregamento de CSS

1. **Bootstrap CSS** (CDN)
2. **Font Awesome** (CDN) 
3. **Google Fonts** (CDN)
4. **layout.css** (sempre carregado)
5. **{controller}.css** (específico da página)

```html
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome para ícones -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- CSS customizado do layout -->
<link href="assets/css/layout.css" rel="stylesheet">

<!-- CSS específico da página (automático) -->
<link href="assets/css/contract.css" rel="stylesheet">
```

## Variáveis CSS Globais

O `layout.css` define variáveis CSS globais que podem ser utilizadas em qualquer arquivo:

```css
:root {
    --ross-blue: #0D2149;
    --ross-light-blue: #1a3a7a;
    --ross-beige: #F9EBE0;
    --ross-dark-beige: #f2e0c7;
    --ross-text-dark: #1e293b;
    --ross-text-muted: #64748b;
    --sidebar-width: 280px;
}
```

## Sobrescrevendo o CSS Automático

### Método 1: Variável `$page_css` no Controller

```php
// No controller
$page_css = [
    'assets/css/custom-contract.css',
    'assets/css/additional-styles.css'
];
```

### Método 2: CSS Inline (não recomendado)

```php
// Na view
<style>
    .custom-style { ... }
</style>
```

## Estrutura Recomendada para CSS Específico

### contract.css (exemplo)
```css
/* Estilos específicos para análise de contrato */

/* Usar variáveis globais quando possível */
.hero-section {
    background: linear-gradient(135deg, var(--ross-blue) 0%, var(--ross-light-blue) 100%);
    color: white;
}

/* Estilos específicos do módulo */
.contract-analysis-card {
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Responsividade */
@media (max-width: 768px) {
    .hero-section {
        padding: 2rem 0;
    }
}
```

## Debugging CSS

### Verificar se o CSS está sendo carregado

1. **Inspecionar elemento** no navegador
2. **Network tab** → verificar se `contract.css` foi carregado
3. **Console** → verificar erros 404

### Verificar mapeamento de controller

```php
// Adicionar debug temporário no layout.php
<?php
$controller_name = getControllerName();
$css_file = "assets/css/{$controller_name}.css";
echo "<!-- Debug: Controller = {$controller_name}, CSS = {$css_file} -->";
?>
```

## JavaScript Automático

O sistema também carrega JavaScript automaticamente seguindo a mesma lógica:

```php
<!-- JavaScript específico da página (se existir) -->
<?php if (isset($page_js)): ?>
    <?php foreach ($page_js as $js): ?>
        <script src="<?php echo htmlspecialchars($js); ?>"></script>
    <?php endforeach; ?>
<?php else: ?>
    <!-- JavaScript automático baseado no controller -->
    <?php
    $controller_name = getControllerName();
    $js_file = "assets/js/{$controller_name}.js";
    if (file_exists($js_file)):
    ?>
        <script src="<?php echo $js_file; ?>"></script>
    <?php endif; ?>
<?php endif; ?>
```

## Exemplo Prático: Página de Contrato

### 1. Estrutura de Arquivos
```
app/Controllers/ContractController.php
assets/css/contract.css
assets/js/contract.js
contract.php
```

### 2. Fluxo Completo

1. **URL**: `/contract/123`
2. **Controller**: `ContractController::show()`
3. **View**: `contract.php` é incluída
4. **Layout**: `layout.php` envolve o conteúdo
5. **CSS**: `contract.css` é carregado automaticamente
6. **JS**: `contract.js` é carregado automaticamente

### 3. Dados do PHP para JavaScript

```php
<!-- contract.php -->
<script>
    window.contractAnalysisData = <?php echo json_encode($analysisData, JSON_UNESCAPED_UNICODE); ?>;
</script>
```

```javascript
// contract.js
class ContractAnalysisApp {
    constructor() {
        this.data = window.contractAnalysisData || [];
        this.init();
    }
    // ...
}
```

## Boas Práticas

### ✅ Fazer
- Seguir a convenção de nomenclatura: `{controller}.css`
- Usar variáveis CSS globais do `layout.css`
- Manter CSS específico no arquivo do controller
- Testar responsividade em diferentes dispositivos

### ❌ Evitar
- CSS inline nas views
- Importações manuais de CSS nas views
- Sobrescrever estilos globais desnecessariamente
- CSS específico no `layout.css`

## Troubleshooting

### CSS não está carregando
1. Verificar se o arquivo existe: `assets/css/{controller}.css`
2. Verificar permissões do arquivo
3. Verificar mapeamento no `getControllerName()`
4. Verificar console do navegador para erros 404

### Conflitos de CSS
1. Usar especificidade CSS adequada
2. Verificar ordem de carregamento
3. Usar `!important` apenas quando necessário
4. Testar isoladamente

### Problemas de cache
1. Forçar refresh: `Ctrl+F5`
2. Desabilitar cache no DevTools
3. Adicionar versioning: `contract.css?v=1.0`

---

**Nota**: Este sistema facilita a manutenção e organização do código, mantendo cada módulo com seus próprios estilos enquanto compartilha estilos globais através do `layout.css`.
