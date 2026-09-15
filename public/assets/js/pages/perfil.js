document.addEventListener('DOMContentLoaded', function ()
{
    const pfpDropZone      = document.getElementById('pfpDropZone');
    const fotoPerfilReal   = document.getElementById('fotoPerfilReal');
    const pfpUrlInput      = document.getElementById('pfpUrlInput');
    const btnAplicarUrl    = document.getElementById('btnAplicarUrlPerfil');
    const statusText       = document.getElementById('pfp-status-text');
    const previewContainer = document.getElementById('pfpPreviewContainer');
    const previewEmpty     = document.getElementById('pfpPreviewEmpty');
    const previewImg       = document.getElementById('pfpPreviewImg');
    const hiddenFotoUrl    = document.getElementById('hiddenFotoUrl');
    const cropperImgEl     = document.getElementById('cropperImagePerfil');
    const btnSalvarCrop    = document.getElementById('btnSalvarCropPerfil');

    const perfilModalEl  = document.getElementById('perfilImageModal');
    const cropperModalEl = document.getElementById('cropperModal');
    const zoomModalEl    = document.getElementById('modalPfpZoom');

    const bsPerfilModal  = new bootstrap.Modal(perfilModalEl);
    const bsCropperModal = new bootstrap.Modal(cropperModalEl);
    const bsZoomModal    = new bootstrap.Modal(zoomModalEl);

    let cropperInstance = null;

    document.addEventListener('dragover', (e) => e.preventDefault());
    document.addEventListener('drop', (e) =>
    {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/'))
        {
            bsPerfilModal.hide();
            abrirCropper(URL.createObjectURL(file));
        }
    });

    if (pfpDropZone)
    {
        pfpDropZone.addEventListener('click', () => fotoPerfilReal.click());

        pfpDropZone.addEventListener('dragover', (e) =>
        {
            e.preventDefault();
            pfpDropZone.classList.add('dragover');
        });

        pfpDropZone.addEventListener('dragleave', () => pfpDropZone.classList.remove('dragover'));

        pfpDropZone.addEventListener('drop', (e) =>
        {
            e.preventDefault();
            pfpDropZone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/'))
            {
                abrirCropper(URL.createObjectURL(file));
            }
        });
    }

    if (fotoPerfilReal)
    {
        fotoPerfilReal.addEventListener('change', () =>
        {
            const file = fotoPerfilReal.files[0];
            if (file)
            {
                abrirCropper(URL.createObjectURL(file));
            }
        });
    }

    function abrirCropper(src)
    {
        bsPerfilModal.hide();
        cropperImgEl.src = src;
        cropperModalEl.addEventListener('shown.bs.modal', iniciarCropper, { once: true });
        bsCropperModal.show();
    }

    function iniciarCropper()
    {
        cropperInstance?.destroy();
        cropperInstance = new Cropper(cropperImgEl, {
            aspectRatio: 1,
            viewMode: 1,
            background: false,
            autoCropArea: 0.85
        });
    }

    if (btnSalvarCrop)
    {
        btnSalvarCrop.addEventListener('click', () =>
        {
            if (!cropperInstance) return;

            cropperInstance.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) =>
            {
                const url = URL.createObjectURL(blob);
                previewImg.src                 = url;
                previewContainer.style.display = 'block';
                previewEmpty.style.display     = 'none';
                statusText.textContent         = '✓ Imagem recortada e pronta para salvar';

                const dt   = new DataTransfer();
                const file = new File([blob], 'perfil_crop.jpg', { type: 'image/jpeg' });
                dt.items.add(file);
                fotoPerfilReal.files = dt.files;

                hiddenFotoUrl.value = '';
                pfpUrlInput.value   = '';

                bsCropperModal.hide();
                cropperInstance.destroy();
                cropperInstance = null;
            }, 'image/jpeg', 0.92);
        });
    }

    if (cropperModalEl)
    {
        cropperModalEl.addEventListener('hidden.bs.modal', () =>
        {
            cropperInstance?.destroy();
            cropperInstance = null;
        });
    }

    if (btnAplicarUrl)
    {
        btnAplicarUrl.addEventListener('click', () =>
        {
            const url = pfpUrlInput.value.trim();
            if (!url) return;

            btnAplicarUrl.disabled    = true;
            btnAplicarUrl.textContent = 'Carregando...';

            fetch('../../api/controllers/proxy_imagem.php?url=' + encodeURIComponent(url))
                .then(resp => resp.json())
                .then(dados =>
                {
                    if (!dados.sucesso)
                    {
                        throw new Error(dados.erro || 'Erro ao carregar imagem');
                    }

                    abrirCropper(dados.caminho);
                    hiddenFotoUrl.value = dados.arquivo;
                    fotoPerfilReal.value = '';
                })
                .catch(erro =>
                {
                    Swal.fire({
                        title: 'Erro ao carregar imagem (・_・;)',
                        text: erro.message || 'Não foi possível carregar a imagem da URL.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                })
                .finally(() =>
                {
                    btnAplicarUrl.disabled    = false;
                    btnAplicarUrl.textContent = 'Confirmar Link';
                });
        });
    }

    const pfpAtualImg = document.getElementById('pfpAtualImg');
    if (pfpAtualImg)
    {
        pfpAtualImg.addEventListener('click', function ()
        {
            document.getElementById('modalPfpImg').src = this.src;
            bsZoomModal.show();
        });
    }

    const formPerfil = document.getElementById('formPerfil');
    if (formPerfil)
    {
        formPerfil.addEventListener('submit', function (e)
        {
            const temMudanca = fotoPerfilReal.files.length > 0 || hiddenFotoUrl.value.trim() !== '';
            if (!temMudanca)
            {
                e.preventDefault();
                Swal.fire({
                    title: 'Nenhuma imagem selecionada (・_・;)',
                    text: 'Selecione ou arraste uma nova foto antes de salvar.',
                    icon: 'info',
                    confirmButtonColor: '#c90879',
                    customClass: { popup: 'rounded-4 shadow-lg' }
                });
                return;
            }

            e.preventDefault();
            Swal.fire({
                title: 'Salvar nova foto? ✨',
                text: 'Sua foto de perfil será atualizada.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c90879',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, salvar!',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then(res =>
            {
                if (res.isConfirmed)
                {
                    formPerfil.submit();
                }
            });
        });
    }

    const formDados = document.getElementById('formDadosUsuario');
    if (formDados)
    {
        formDados.addEventListener('submit', function (e)
        {
            const inputLogin = formDados.querySelector('input[name="login"]');
            const temMudanca = inputLogin && inputLogin.value.trim() !== inputLogin.placeholder.trim();

            if (!temMudanca)
            {
                e.preventDefault();
                Swal.fire({
                    title: 'Nenhuma alteração detectada (・_・;)',
                    text: 'Altere o nome de usuário antes de salvar.',
                    icon: 'info',
                    confirmButtonColor: '#c90879',
                    customClass: { popup: 'rounded-4 shadow-lg' }
                });
                return;
            }

            e.preventDefault();
            Swal.fire({
                title: 'Salvar dados da conta? 📝',
                text: 'Seu nome de usuário será atualizado.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c90879',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, salvar!',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then(res =>
            {
                if (res.isConfirmed)
                {
                    formDados.submit();
                }
            });
        });
    }

    const formSenha = document.getElementById('formSenha');
    if (formSenha)
    {
        formSenha.addEventListener('submit', function (e)
        {
            e.preventDefault();
            Swal.fire({
                title: 'Alterar sua senha? 🔒',
                text: 'Sua senha será atualizada. Certifique-se de que lembra da nova senha!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c90879',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, alterar!',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then(res =>
            {
                if (res.isConfirmed)
                {
                    formSenha.submit();
                }
            });
        });
    }

    const status       = new URLSearchParams(window.location.search).get('status');
    const dadosStatus  = new URLSearchParams(window.location.search).get('dados_status');
    const dadosErro    = new URLSearchParams(window.location.search).get('dados_erro');
    const senhaStatus  = new URLSearchParams(window.location.search).get('senha_status');
    const senhaErro    = new URLSearchParams(window.location.search).get('senha_erro');
    const erro         = new URLSearchParams(window.location.search).get('erro');

    if (status === 'sucesso')
    {
        Swal.fire({
            title: 'Foto Atualizada! ✨',
            text: 'Sua nova foto de perfil foi salva com sucesso.',
            icon: 'success',
            confirmButtonColor: '#c90879',
            confirmButtonText: 'Ótimo!',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }

    if (dadosStatus === 'sucesso')
    {
        Swal.fire({
            title: 'Dados Atualizados! (⌒‿⌒)',
            text: 'Suas informações de conta foram salvas com sucesso.',
            icon: 'success',
            confirmButtonColor: '#c90879',
            confirmButtonText: 'Ótimo!',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }

    if (dadosErro)
    {
        Swal.fire({
            title: 'Oops... (>-<)',
            text: decodeURIComponent(dadosErro),
            icon: 'error',
            confirmButtonColor: '#dc3545',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }

    if (senhaStatus === 'sucesso')
    {
        Swal.fire({
            title: 'Senha Alterada! 🔒',
            text: 'Sua senha foi atualizada com sucesso.',
            icon: 'success',
            confirmButtonColor: '#c90879',
            confirmButtonText: 'Ótimo!',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }

    if (senhaErro)
    {
        Swal.fire({
            title: 'Oops... (>-<)',
            text: decodeURIComponent(senhaErro),
            icon: 'error',
            confirmButtonColor: '#dc3545',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }

    if (erro)
    {
        Swal.fire({
            icon: 'error',
            title: 'Oops... (>-<)',
            text: decodeURIComponent(erro),
            confirmButtonColor: '#dc3545',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }
});

function limparParametrosUrl()
{
    if (window.history.replaceState)
    {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}
