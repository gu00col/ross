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
});
