/*
 * Este arquivo está pronto para adicionar interatividade e
 * manipulação do DOM ao site Ross Jurídico.
*/
document.addEventListener('DOMContentLoaded', function() {
    console.log('Documento carregado e pronto!');

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
});
