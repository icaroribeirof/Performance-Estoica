// Login JavaScript

document.getElementById('formLogin').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const mensagemErro = document.getElementById('mensagemErro');

    // Limpar mensagem anterior
    mensagemErro.classList.remove('ativo');
    mensagemErro.textContent = '';

    // Desabilitar botão
    const botao = this.querySelector('button');
    botao.disabled = true;
    botao.textContent = 'Entrando...';

    try {
        const formData = new FormData();
        formData.append('email', email);
        formData.append('senha', senha);

        const response = await fetch('api/auth.php?acao=login', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.sucesso) {
            // Sucesso - redirecionar
            window.location.href = 'dashboard.php';
        } else {
            // Erro
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem || 'Erro ao fazer login';
            botao.disabled = false;
            botao.textContent = 'Entrar';
        }
    } catch (error) {
        console.error('Erro:', error);
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Erro ao conectar ao servidor';
        botao.disabled = false;
        botao.textContent = 'Entrar';
    }
});

// Pressionar Enter para enviar
document.getElementById('senha').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('formLogin').submit();
    }
});
