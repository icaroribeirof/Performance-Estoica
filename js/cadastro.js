// Cadastro JavaScript

document.getElementById('formCadastro').addEventListener('submit', async function(e) {
    e.preventDefault();

    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const senhaConfirm = document.getElementById('senhaConfirm').value;
    const mensagemErro = document.getElementById('mensagemErro');
    const mensagemSucesso = document.getElementById('mensagemSucesso');

    // Limpar mensagens anteriores
    mensagemErro.classList.remove('ativo');
    mensagemSucesso.classList.remove('ativo');
    mensagemErro.textContent = '';
    mensagemSucesso.textContent = '';

    // Validações básicas
    if (senha !== senhaConfirm) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'As senhas não conferem';
        return;
    }

    if (senha.length < 6) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'A senha deve ter no mínimo 6 caracteres';
        return;
    }

    // Desabilitar botão
    const botao = this.querySelector('button');
    botao.disabled = true;
    botao.textContent = 'Cadastrando...';

    try {
        const formData = new FormData();
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('senha', senha);
        formData.append('senha_confirm', senhaConfirm);

        const response = await fetch('api/auth.php?acao=cadastro', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.sucesso) {
            // Sucesso
            mensagemSucesso.classList.add('ativo');
            mensagemSucesso.textContent = 'Cadastro realizado com sucesso! Redirecionando para login...';
            
            // Limpar formulário
            this.reset();
            
            // Redirecionar após 2 segundos
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            // Erro
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem || 'Erro ao realizar cadastro';
            botao.disabled = false;
            botao.textContent = 'Cadastrar';
        }
    } catch (error) {
        console.error('Erro:', error);
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Erro ao conectar ao servidor';
        botao.disabled = false;
        botao.textContent = 'Cadastrar';
    }
});
