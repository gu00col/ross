# Assets - Sistema ROSS Analista Jurídico

Esta pasta contém todos os recursos estáticos do sistema ROSS.

## Estrutura de Pastas

```
assets/
├── css/                    # Arquivos de estilo
│   └── login.css          # Estilos específicos da página de login
├── js/                    # Arquivos JavaScript
│   └── login.js           # Funcionalidades da página de login
├── images/                # Imagens e logos
│   └── logo-placeholder.txt  # Especificações do logo
└── README.md              # Este arquivo
```

## Arquivos CSS

### login.css
- Estilos customizados para a página de login
- Layout responsivo com painel esquerdo (logo) e direito (formulário)
- Cores do sistema: azul ROSS (#1e3a8a) e bege (#f5f5dc)
- Animações e efeitos visuais
- Compatível com Bootstrap 5.3.0

## Arquivos JavaScript

### login.js
- Validação de formulário em tempo real
- Efeitos visuais e animações
- Estados de carregamento
- Compatível com ES6+

## Imagens

### Logo
- **Especificações**: Consulte `logo-placeholder.txt`
- **Formato recomendado**: SVG
- **Cores**: Branco para fundo azul
- **Responsivo**: Adaptável a diferentes tamanhos

## Dependências Externas

- **Bootstrap**: 5.3.0 (CSS e JS)
- **Font Awesome**: 6.4.0 (Ícones)
- **Google Fonts**: Inter (Tipografia)

## Uso

### CSS
```html
<link href="assets/css/login.css" rel="stylesheet">
```

### JavaScript
```html
<script src="assets/js/login.js"></script>
```

## Desenvolvimento

### Adicionando novos estilos
1. Crie um novo arquivo CSS em `css/`
2. Importe no HTML correspondente
3. Siga o padrão de nomenclatura: `componente.css`

### Adicionando novas funcionalidades JS
1. Crie um novo arquivo JS em `js/`
2. Importe no HTML correspondente
3. Use ES6+ e documente com JSDoc

### Adicionando imagens
1. Salve na pasta `images/`
2. Use formatos otimizados (SVG, WebP, PNG)
3. Mantenha nomes descritivos

## Notas

- Todos os arquivos seguem as convenções do projeto
- CSS usa variáveis CSS para cores do sistema
- JavaScript é modular e reutilizável
- Imagens são otimizadas para web

