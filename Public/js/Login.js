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

// Formulário de login
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Limpar mensagens de erro anteriores
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    
    let hasError = false;
    
    // Validações
    if (!data.email.includes('@')) {
        showError('email', 'Email inválido');
        hasError = true;
    }
    
    if (data.senha.length < 6) {
        showError('senha', 'Senha deve ter no mínimo 6 caracteres');
        hasError = true;
    }
    
    if (!hasError) {
        console.log('Dados de login:', data);
        
        // Aqui você implementaria a lógica de autenticação
        // Por enquanto, vamos simular um login bem-sucedido
        
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.textContent = 'Entrando...';
        submitButton.disabled = true;
        
        setTimeout(() => {
            showToast('Login realizado com sucesso!', 'success');
            
            // Redirecionar após 1 segundo
            setTimeout(() => {
                // Aqui você redirecionaria para a página principal
                window.location.href = '/';
            }, 1000);
        }, 500);
    }
});

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorElement = field.parentElement.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = message;
    }
}