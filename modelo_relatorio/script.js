// JavaScript para o Laudo de Análise de Contrato
class ContractAnalysisApp {
    constructor() {
        this.data = [];
        this.init();
    }

    async init() {
        try {
            await this.loadData();
            this.setupEventListeners();
            this.renderAllSections();
            this.setCurrentDate();
            this.addScrollAnimations();
        } catch (error) {
            console.error('Erro ao inicializar aplicação:', error);
            this.showError('Erro ao carregar os dados do contrato');
        }
    }

    async loadData() {
        try {
            const response = await fetch('data.json');
            if (!response.ok) {
                throw new Error('Erro ao carregar dados');
            }
            this.data = await response.json();
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
            throw error;
        }
    }

    setupEventListeners() {
        // Navegação suave
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll para destacar seção ativa
        window.addEventListener('scroll', () => this.updateActiveNavLink());
    }

    renderAllSections() {
        this.renderHeroStats();
        this.renderBasicInfo();
        this.renderLeonineClauses();
        this.renderInconsistencies();
        this.renderRecommendations();
    }

    renderHeroStats() {
        const attentionClausesCount = this.data.filter(item => item.section_id === 2).length;
        const inconsistenciesCount = this.data.filter(item => item.section_id === 3).length;

        const recommendationsItem = this.data.find(item => item.section_id === 4);
        let recommendationsCount = 0;
        if (recommendationsItem && recommendationsItem.content) {
            const recommendationLines = recommendationsItem.content.split('\n');
            // Conta as linhas que começam com um número seguido de um ponto (ex: "1. Recomendação...")
            recommendationsCount = recommendationLines.filter(line => /^\s*\d+\.\s/.test(line)).length;
        }

        const attentionClausesEl = document.getElementById('attention-clauses-count');
        if (attentionClausesEl) {
            attentionClausesEl.textContent = attentionClausesCount;
        }

        const inconsistenciesEl = document.getElementById('inconsistencies-count');
        if (inconsistenciesEl) {
            inconsistenciesEl.textContent = inconsistenciesCount;
        }

        const recommendationsEl = document.getElementById('recommendations-count');
        if (recommendationsEl) {
            recommendationsEl.textContent = recommendationsCount;
        }
    }

    renderBasicInfo() {
        const container = document.getElementById('basic-info');
        if (!container) return;

        const basicData = this.data.filter(item => item.section_id === 1);
        container.innerHTML = basicData.map(item => this.createBasicInfoCard(item)).join('');
    }

    renderLeonineClauses() {
        const container = document.getElementById('leonine-clauses');
        if (!container) return;

        const leonineData = this.data.filter(item => item.section_id === 2);
        container.innerHTML = leonineData.map(item => this.createLeonineCard(item)).join('');
    }

    renderInconsistencies() {
        const container = document.getElementById('inconsistencies');
        if (!container) return;

        const inconsistencyData = this.data.filter(item => item.section_id === 3);
        container.innerHTML = inconsistencyData.map(item => this.createInconsistencyCard(item)).join('');
    }

    renderRecommendations() {
        const container = document.getElementById('recommendations');
        if (!container) return;

        const recommendationData = this.data.filter(item => item.section_id === 4);
        container.innerHTML = recommendationData.map(item => this.createRecommendationCard(item)).join('');
    }

    createBasicInfoCard(item) {
        return `
            <div class="col-lg-6 mb-4">
                <div class="card-container">
                    <div class="card basic-info fade-in-up">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle me-2 text-info"></i>
                                <span class="badge basic me-2">Básico</span>
                                ${item.label}
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">${this.formatContent(item.content)}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    createLeonineCard(item) {
        const hasDetails = Object.keys(item.details).length > 0;
        return `
            <div class="col-lg-6 mb-4">
                <div class="card-container">
                    <div class="card leonine fade-in-up">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                                    <span class="badge leonine me-2">Leonina</span>
                                </div>
                                ${hasDetails ? '<button class="btn btn-sm btn-outline-danger" onclick="app.showDetails(\'' + this.escapeHtml(JSON.stringify(item)) + '\')"><i class="bi bi-eye"></i></button>' : ''}
                            </div>
                            <div class="card-title">${item.label}</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">${this.formatContent(item.content)}</p>
                            ${hasDetails ? this.createDetailsSection(item.details) : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    createInconsistencyCard(item) {
        const hasDetails = Object.keys(item.details).length > 0;
        return `
            <div class="col-lg-6 mb-4">
                <div class="card-container">
                    <div class="card inconsistency fade-in-up">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-circle me-2 text-warning"></i>
                                    <span class="badge inconsistency me-2">Inconsistência</span>
                                </div>
                                ${hasDetails ? '<button class="btn btn-sm btn-outline-warning" onclick="app.showDetails(\'' + this.escapeHtml(JSON.stringify(item)) + '\')"><i class="bi bi-eye"></i></button>' : ''}
                            </div>
                            <div class="card-title">${item.label}</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">${this.formatContent(item.content)}</p>
                            ${hasDetails ? this.createDetailsSection(item.details) : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    createRecommendationCard(item) {
        return `
            <div class="col-12 mb-4">
                <div class="card-container">
                    <div class="card recommendation fade-in-up">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-lightbulb me-2 text-success"></i>
                                <span class="badge recommendation me-2">Recomendação</span>
                                ${item.label}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="recommendation-content">
                                ${this.formatRecommendationContent(item.content)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    createDetailsSection(details) {
        let html = '<div class="details-section">';
        
        Object.entries(details).forEach(([key, value]) => {
            if (value && value.trim()) {
                html += `
                    <div class="mb-3">
                        <h6 class="text-uppercase fw-bold">${this.formatDetailKey(key)}</h6>
                        <p class="mb-0">${this.formatContent(value)}</p>
                    </div>
                `;
            }
        });
        
        html += '</div>';
        return html;
    }

    formatDetailKey(key) {
        const keyMap = {
            'Ponto de Atenção': 'Ponto de Atenção',
            'Descrição do Risco': 'Descrição do Risco',
            'Citação do Trecho Relevante': 'Citação do Trecho Relevante',
            'Localização': 'Localização',
            'Impacto Potencial': 'Impacto Potencial'
        };
        return keyMap[key] || key;
    }

    formatContent(content) {
        if (!content) return '';
        
        // Converter quebras de linha em <br>
        let formatted = content.replace(/\n/g, '<br>');
        
        // Destacar texto em negrito
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Destacar texto em itálico
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Destacar cláusulas específicas
        formatted = formatted.replace(/(Cláusula \d+[\.\d]*)/g, '<span class="highlight">$1</span>');
        
        return formatted;
    }

    formatRecommendationContent(content) {
        if (!content) return '';
        
        // Dividir por quebras de linha duplas para criar seções
        const sections = content.split('\n\n');
        
        let html = '';
        sections.forEach(section => {
            if (section.trim()) {
                // Verificar se é uma lista numerada
                if (section.match(/^\d+\./)) {
                    const listItems = section.split(/\n(?=\d+\.)/);
                    html += '<ol class="recommendation-list">';
                    listItems.forEach(item => {
                        if (item.trim()) {
                            const cleanItem = item.replace(/^\d+\.\s*/, '').trim();
                            html += `<li>${this.formatContent(cleanItem)}</li>`;
                        }
                    });
                    html += '</ol>';
                } else {
                    html += `<p class="mb-3">${this.formatContent(section)}</p>`;
                }
            }
        });
        
        return html;
    }

    showDetails(itemJson) {
        try {
            const item = JSON.parse(itemJson);
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            const modalTitle = document.getElementById('detailModalTitle');
            const modalBody = document.getElementById('detailModalBody');
            
            modalTitle.textContent = item.label;
            modalBody.innerHTML = `
                <div class="mb-3">
                    <h6 class="fw-bold">Descrição:</h6>
                    <p>${this.formatContent(item.content)}</p>
                </div>
                ${Object.keys(item.details).length > 0 ? this.createDetailsSection(item.details) : ''}
            `;
            
            modal.show();
        } catch (error) {
            console.error('Erro ao mostrar detalhes:', error);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    setCurrentDate() {
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            dateElement.textContent = now.toLocaleDateString('pt-BR', options);
        }
    }

    updateActiveNavLink() {
        const sections = ['resumo', 'clausulas-leoninas', 'inconsistencias', 'recomendacoes'];
        const navLinks = document.querySelectorAll('.nav-link');
        
        let current = '';
        sections.forEach(section => {
            const element = document.getElementById(section);
            if (element) {
                const rect = element.getBoundingClientRect();
                if (rect.top <= 100 && rect.bottom >= 100) {
                    current = section;
                }
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    }

    addScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observar cards quando forem adicionados
        setTimeout(() => {
            document.querySelectorAll('.card').forEach(card => {
                observer.observe(card);
            });
        }, 100);
    }

    showError(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
}

// Inicializar aplicação quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.app = new ContractAnalysisApp();
});

