// script.js - JavaScript funcional para o Sistema de Biblioteca
// Adiciona validação de formulários, filtro de busca em tabelas e interatividade básica
// Compatível com navegadores modernos (ES6+), sem dependências externas

document.addEventListener('DOMContentLoaded', function() {
    // Validação de formulários (ex.: para cadastro de cliente ou livro)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error-input'); // Adicione classe para estilo (defina no CSS: .error-input { border-color: red; })
                    showError(input, 'Campo obrigatório');
                } else {
                    input.classList.remove('error-input');
                    clearError(input);
                    // Validações específicas
                    if (input.type === 'email' && !validateEmail(input.value)) {
                        isValid = false;
                        input.classList.add('error-input');
                        showError(input, 'E-mail inválido');
                    }
                    if (input.type === 'tel' && !validatePhone(input.value)) {
                        isValid = false;
                        input.classList.add('error-input');
                        showError(input, 'Telefone inválido (ex: 11 91234-5678)');
                    }
                }
            });

            if (!isValid) {
                event.preventDefault(); // Impede envio se inválido
                alert('Por favor, corrija os erros no formulário.');
            }
        });
    });

    // Filtro de busca em tabelas (ex.: buscar livros ou clientes na tabela)
    const searchInputs = document.querySelectorAll('.search-input'); // Assuma que há um input com class="search-input" acima da tabela
    searchInputs.forEach(search => {
        search.addEventListener('input', function() {
            const table = document.querySelector(search.dataset.tableId); // Use data-table-id no input para linkar à tabela
            const filter = search.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });

    // Toggle de modo escuro (opcional, para tema com tons de marrom)
    const darkModeToggle = document.querySelector('#dark-mode-toggle'); // Botão com id="dark-mode-toggle"
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            // Salva preferência no localStorage
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });

        // Carrega preferência salva
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    }

    // Funções auxiliares
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validatePhone(phone) {
        const re = /^\d{2} \d{5}-\d{4}$/; // Formato brasileiro simples, ajuste se necessário
        return re.test(phone);
    }

    function showError(input, message) {
        let error = input.nextElementSibling;
        if (!error || !error.classList.contains('error')) {
            error = document.createElement('span');
            error.classList.add('error');
            input.parentNode.insertBefore(error, input.nextSibling);
        }
        error.textContent = message;
    }

    function clearError(input) {
        const error = input.nextElementSibling;
        if (error && error.classList.contains('error')) {
            error.textContent = '';
        }
    }
});

