/*
 * Este arquivo está pronto para adicionar interatividade e
 * manipulação do DOM ao site Ross Jurídico.
*/
document.addEventListener('DOMContentLoaded', function() {
    console.log('Documento carregado e pronto!');

    // Lógica para o formulário de Upload
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(event) {
            const contractFile = document.getElementById('contractFile');
            // Validação simples para garantir que um arquivo foi selecionado
            if (!contractFile || contractFile.files.length === 0) {
                // Previne o envio do formulário se nenhum arquivo for selecionado
                event.preventDefault(); 
                // (Opcional) Adicionar um alerta ou feedback visual para o usuário
                alert("Por favor, selecione um arquivo para enviar.");
                return;
            }

            const submitButton = document.getElementById('submitUploadButton');
            if (submitButton) {
                // Desabilita o botão para evitar cliques duplos
                submitButton.disabled = true;

                // Mostra o spinner e atualiza o texto
                const spinner = submitButton.querySelector('.spinner-border');
                const buttonText = submitButton.querySelector('.button-text');

                if (spinner) {
                    spinner.classList.remove('d-none');
                }
                if (buttonText) {
                    buttonText.textContent = 'Enviando...';
                }
            }
        });
    }

    // Modal de Exclusão de Contrato: preencher nome dinamicamente
    const deleteContractModalEl = document.getElementById('deleteContractModal');
    if (deleteContractModalEl) {
        deleteContractModalEl.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget; // botão/link que abriu o modal
            if (!trigger) {
                return;
            }
            const contractName = trigger.getAttribute('data-contract-name') || '';
            const contractId = trigger.getAttribute('data-contract-id') || '';
            const userId = trigger.getAttribute('data-user-id') || '';
            const nameTarget = deleteContractModalEl.querySelector('#deleteContractName');
            const inputContractId = deleteContractModalEl.querySelector('#deleteContratoId');
            const inputUserId = deleteContractModalEl.querySelector('#deleteUserId');
            if (nameTarget) {
                nameTarget.textContent = contractName;
            }
            if (inputContractId) {
                inputContractId.value = contractId;
            }
            if (inputUserId) {
                inputUserId.value = userId;
            }
        });

        // Garantir que o botão "Sim" envie o formulário explicitamente
        const confirmDeleteButton = deleteContractModalEl.querySelector('#confirmDeleteButton');
        if (confirmDeleteButton) {
            confirmDeleteButton.addEventListener('click', function () {
                const form = deleteContractModalEl.querySelector('#deleteContractForm');
                if (form) {
                    form.submit();
                }
            });
        }
    }

    // Lógica para limpar filtros na página de contratos
    const clearFiltersBtn = document.getElementById('clear-filters-btn');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            const filtersForm = this.closest('form');
            if (filtersForm) {
                // Reseta todos os campos do formulário
                filtersForm.reset();
                
                // Redireciona para a URL base sem os filtros
                window.location.href = window.location.pathname;
            }
        });
    }

    // Lógica para a página de detalhes da análise
    const analysisNav = document.getElementById('analysis-nav');
    if (analysisNav) {
        // Ativa o scrollspy
        const scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#analysis-nav',
            offset: 120 // Ajustado para o novo layout com uma única barra no topo
        });

        // Rolagem suave para os links de navegação
        const navLinks = analysisNav.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 110, // Ajuste de offset
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // Lógica para o botão "Voltar ao Topo"
    const backToTopButton = document.getElementById('back-to-top');
    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        backToTopButton.addEventListener('click', (event) => {
            event.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
