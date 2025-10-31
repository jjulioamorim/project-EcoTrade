let currentUserType = 'empresa';

// Gerenciar abas
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remover active de todas as abas
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        
        // Adicionar active na aba clicada
        this.classList.add('active');
        
        // Atualizar tipo de usuário
        currentUserType = this.dataset.type;
        
        // Mostrar/ocultar campos
        document.querySelector('.empresa-fields').style.display = 
            currentUserType === 'empresa' ? 'block' : 'none';
        document.querySelector('.produtor-fields').style.display = 
            currentUserType === 'produtor' ? 'block' : 'none';
        document.querySelector('.admin-fields').style.display = 
            currentUserType === 'admin' ? 'block' : 'none';
            
        // Limpar mensagens de erro
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    });
});

// Validação de CNPJ
function validarCNPJ(cnpj) {
    return /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/.test(cnpj);
}

// Validação de CPF
function validarCPF(cpf) {
    return /^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(cpf);
}

// Mostrar toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Formulário de cadastro
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Limpar mensagens de erro anteriores
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    
    let hasError = false;
    
    // Validações básicas
    if (data.nome.length < 3) {
        showError('nome', 'Nome deve ter no mínimo 3 caracteres');
        hasError = true;
    }
    
    if (!data.email.includes('@')) {
        showError('email', 'Email inválido');
        hasError = true;
    }
    
    if (data.senha.length < 6) {
        showError('senha', 'Senha deve ter no mínimo 6 caracteres');
        hasError = true;
    }
    
    if (data.senha !== data.confirmarSenha) {
        showError('confirmarSenha', 'As senhas não coincidem');
        hasError = true;
    }
    
    // Validações por tipo de usuário
    if (currentUserType === 'empresa') {
        if (!validarCNPJ(data.cnpj)) {
            showError('cnpj', 'CNPJ inválido (formato: 00.000.000/0000-00)');
            hasError = true;
        }
        if (!data.razaoSocial || data.razaoSocial.length < 3) {
            showError('razaoSocial', 'Razão social é obrigatória');
            hasError = true;
        }
        if (!data.setorAtuacao || data.setorAtuacao.length < 3) {
            showError('setorAtuacao', 'Setor de atuação é obrigatório');
            hasError = true;
        }
    } else if (currentUserType === 'produtor') {
        if (!validarCPF(data.cpf)) {
            showError('cpf-produtor', 'CPF inválido (formato: 000.000.000-00)');
            hasError = true;
        }
        if (!data.nomeFazenda || data.nomeFazenda.length < 3) {
            showError('nomeFazenda', 'Nome da fazenda é obrigatório');
            hasError = true;
        }
        if (!data.localizacao || data.localizacao.length < 3) {
            showError('localizacao', 'Localização é obrigatória');
            hasError = true;
        }
    } else if (currentUserType === 'admin') {
        if (!validarCPF(data.cpf)) {
            showError('cpf-admin', 'CPF inválido (formato: 000.000.000-00)');
            hasError = true;
        }
    }
    
    if (!hasError) {
        console.log({ ...data, tipo: currentUserType });
        showToast('Cadastro realizado com sucesso!', 'success');
        
        // Redirecionar após 1.5 segundos
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 1500);
    }
});

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorElement = field.parentElement.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = message;
    }
}