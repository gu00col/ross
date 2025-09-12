# Partials - Componentes Reutilizáveis

Este diretório contém os componentes reutilizáveis do sistema ROSS.

## Componentes Disponíveis

### 1. Sidebar (`sidebar.php`)
Menu lateral com navegação principal do sistema.

**Características:**
- Logo ROSS com círculo
- Menu de navegação (Contratos, Configurações, Minha Conta)
- Informações do usuário logado
- Botão de logout
- Design responsivo

### 2. Navbar (`navbar.php`)
Barra de navegação superior com ações do usuário.

**Nota:** Este componente não será usado no sistema atual. Mantido apenas para referência futura.

## Como Usar

### Incluindo o Sidebar

```php
<?php
// No topo da página (apenas sidebar)
include 'partials/sidebar.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Página - ROSS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'partials/sidebar.php'; ?>
    
    <!-- Conteúdo principal -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Seu conteúdo aqui -->
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### CSS Necessário para Layout

```css
/* Adicione este CSS na sua página */
.main-content {
    margin-left: 280px;
    padding: 2rem;
    min-height: 100vh;
    background: #f8fafc;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
    }
}
```

### Notas Importantes

- **Landing Page**: Não usa partials, é independente
- **Sistema Interno**: Usa apenas o sidebar
- **Sem Navbar**: Não será implementado no sistema atual
- **Sem Rodapé**: Não será usado no sistema

## Estrutura de Arquivos

```
partials/
├── sidebar.php      # Menu lateral
├── navbar.php       # Barra superior
└── README.md        # Esta documentação
```

## Dependências

- Bootstrap 5.3.0
- Font Awesome 6.4.0
- Google Fonts (Inter)

## Notas de Desenvolvimento

- Os partials são independentes e podem ser usados separadamente
- O CSS está incluído dentro de cada partial para facilitar o uso
- JavaScript está incluído no navbar para funcionalidades interativas
- Design responsivo incluído para mobile e desktop
