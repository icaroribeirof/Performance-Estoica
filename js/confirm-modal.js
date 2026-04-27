/**
 * confirmarAcao – pop-up de confirmação no padrão do sistema.
 *
 * @param {string}   titulo    – Título do modal (ex: "Deletar Meta")
 * @param {string}   mensagem  – Corpo da mensagem
 * @param {Function} onConfirm – Callback executado ao confirmar
 * @param {string}   [icone]   – Emoji/ícone (padrão: 🗑️)
 */
function confirmarAcao(titulo, mensagem, onConfirm, icone) {
    icone = icone || '🗑️';

    // Cria o overlay se ainda não existir
    let overlay = document.getElementById('confirmModalOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'confirmModalOverlay';
        overlay.innerHTML = `
            <div id="confirmModalBox" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
                <span id="confirmModalIcon"></span>
                <div id="confirmModalTitle"></div>
                <div id="confirmModalMessage"></div>
                <div class="confirm-modal-actions">
                    <button id="confirmModalBtnCancel">Cancelar</button>
                    <button id="confirmModalBtnConfirm">Confirmar</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        // Fechar ao clicar no backdrop
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) _fecharConfirmModal();
        });

        // Fechar com Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('ativo')) {
                _fecharConfirmModal();
            }
        });
    }

    // Preencher conteúdo
    document.getElementById('confirmModalIcon').textContent    = icone;
    document.getElementById('confirmModalTitle').textContent   = titulo;
    document.getElementById('confirmModalMessage').textContent = mensagem;

    // Botão Cancelar
    const btnCancelar = document.getElementById('confirmModalBtnCancel');
    const btnConfirmar = document.getElementById('confirmModalBtnConfirm');

    // Clonar para remover listeners antigos
    const novoCancelar = btnCancelar.cloneNode(true);
    const novoConfirmar = btnConfirmar.cloneNode(true);
    btnCancelar.parentNode.replaceChild(novoCancelar, btnCancelar);
    btnConfirmar.parentNode.replaceChild(novoConfirmar, btnConfirmar);

    document.getElementById('confirmModalBtnCancel').addEventListener('click', _fecharConfirmModal);
    document.getElementById('confirmModalBtnConfirm').addEventListener('click', function() {
        _fecharConfirmModal();
        if (typeof onConfirm === 'function') onConfirm();
    });

    // Abrir
    overlay.classList.add('ativo');
}

function _fecharConfirmModal() {
    const overlay = document.getElementById('confirmModalOverlay');
    if (overlay) overlay.classList.remove('ativo');
}
