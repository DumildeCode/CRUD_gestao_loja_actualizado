// Validações de formulário e animações

document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('edit-modal');
    const deleteModal = document.getElementById('delete-modal');
    const closeButtons = document.querySelectorAll('.close-modal');

    // Botões de editar
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-nome');

            // Preenche os campos do formulário no modal
            document.getElementById('edit-id').value = userId;
            document.getElementById('edit-nome').value = userName;

            // Exibe o modal
            editModal.style.display = 'block';
        });
    });

    // Botões de deletar
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');
            document.getElementById('delete-id').value = userId;
            document.getElementById('delete-modal').style.display = 'block';
        });
    });
    

    // Fechar modais
    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            editModal.style.display = 'none';
            deleteModal.style.display = 'none';
        });
    });

    // Fecha o modal ao clicar fora dele
    window.addEventListener('click', function (event) {
        if (event.target === editModal) {
            editModal.style.display = 'none';
        }
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    });

    // Validação de formulário
    const forms = document.querySelectorAll('form');
    forms.forEach((form) => {
        form.addEventListener('submit', function (e) {
            let isValid = true;
            const inputs = form.querySelectorAll('input, textarea');

            inputs.forEach((input) => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                    input.nextElementSibling?.remove();
                    const error = document.createElement('small');
                    error.style.color = 'red';
                    error.innerText = 'Este campo é obrigatório';
                    input.insertAdjacentElement('afterend', error);
                } else {
                    input.style.borderColor = '#ccc';
                    input.nextElementSibling?.remove();
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha os campos obrigatórios.');
            }
        });
    });

    // Animações de hover para botões
    const buttons = document.querySelectorAll('button');
    buttons.forEach((button) => {
        button.addEventListener('mouseover', () => {
            button.style.transform = 'scale(1.05)';
        });

        button.addEventListener('mouseout', () => {
            button.style.transform = 'scale(1)';
        });
    });
});
