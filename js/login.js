// Login JavaScript

document.getElementById('formLogin').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const mensagemErro = document.getElementById('mensagemErro');

    mensagemErro.classList.remove('ativo');
    mensagemErro.textContent = '';

    const botao = document.getElementById('btnLogin');
    botao.disabled = true;
    botao.innerHTML = '<span class="btn-spinner"></span> Entrando...';

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
            window.location.href = 'dashboard.php';
        } else {
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
