// Cadastro JavaScript

document.getElementById('formCadastro').addEventListener('submit', async function(e) {
    e.preventDefault();

    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const senhaConfirm = document.getElementById('senhaConfirm').value;
    const mensagemErro = document.getElementById('mensagemErro');
    const mensagemSucesso = document.getElementById('mensagemSucesso');

    mensagemErro.classList.remove('ativo');
    mensagemSucesso.classList.remove('ativo');
    mensagemErro.textContent = '';
    mensagemSucesso.textContent = '';

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

    const botao = document.getElementById('btnCadastro');
    botao.disabled = true;
    botao.innerHTML = '<span class="btn-spinner"></span> Criando conta...';

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
            mensagemSucesso.classList.add('ativo');
            mensagemSucesso.textContent = 'Conta criada com sucesso! Redirecionando...';
            this.reset();
            setTimeout(() => { window.location.href = 'login.php'; }, 2000);
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem || 'Erro ao realizar cadastro';
            botao.disabled = false;
            botao.textContent = 'Criar conta';
        }
    } catch (error) {
        console.error('Erro:', error);
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Erro ao conectar ao servidor';
        botao.disabled = false;
        botao.textContent = 'Criar conta';
    }
});

// Password strength indicator
const senhaInput = document.getElementById('senha');
if (senhaInput) {
    senhaInput.addEventListener('input', function() {
        const strength = document.getElementById('passwordStrength');
        const fill = strength?.querySelector('.strength-fill');
        const label = strength?.querySelector('.strength-label');
        if (!strength || !fill || !label) return;

        const val = this.value;
        if (val.length === 0) { strength.style.display = 'none'; return; }
        strength.style.display = 'flex';

        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { text: 'Muito fraca', color: '#ef4444', width: '20%' },
            { text: 'Fraca', color: '#f59e0b', width: '40%' },
            { text: 'Razoável', color: '#f59e0b', width: '60%' },
            { text: 'Boa', color: '#10b981', width: '80%' },
            { text: 'Forte', color: '#10b981', width: '100%' }
        ];

        const level = levels[Math.min(score, 4)];
        fill.style.width = level.width;
        fill.style.background = level.color;
        label.textContent = level.text;
        label.style.color = level.color;
    });
}
