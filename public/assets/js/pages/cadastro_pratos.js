document.addEventListener('DOMContentLoaded', function ()
{
    const elements = {
        dropZone: document.getElementById('dropZone'),
        fotoReal: document.getElementById('fotoInput'),
        hiddenUrlInput: document.getElementById('urlImagem'),
        imagePreview: document.getElementById('previewImg'),
        previewContainer: document.getElementById('previewContainer'),
        statusText: document.getElementById('file-status-text'),
        imgUrlInput: document.getElementById('imgUrlInput'),
        btnApplyUrl: document.getElementById('btnApplyUrl'),
        imageModalElement: document.getElementById('imageModal')
    };

    if (!elements.dropZone) return;

    const imageModal = window.bootstrap?.Modal
        ? new bootstrap.Modal(elements.imageModalElement)
        : null;

    const updateVisualPreview = (src, filename) =>
    {
        elements.imagePreview.src = src;
        elements.previewContainer.style.display = 'block';
        elements.statusText.textContent = filename;
        imageModal?.hide();
    };

    const processLocalFile = (file) =>
    {
        if (!file || !file.type.startsWith('image/')) return;

        if (elements.hiddenUrlInput)
        {
            elements.hiddenUrlInput.value = '';
        }

        const reader = new FileReader();
        reader.onload = (event) => updateVisualPreview(event.target.result, file.name);
        reader.readAsDataURL(file);
    };

    elements.dropZone.addEventListener('click', () => elements.fotoReal.click());

    elements.fotoReal.addEventListener('change', (event) =>
    {
        const file = event.target.files[0];
        if (file)
        {
            processLocalFile(file);
        }
    });

    ['dragenter', 'dragover'].forEach((type) =>
    {
        elements.dropZone.addEventListener(type, (e) =>
        {
            e.preventDefault();
            elements.dropZone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach((type) =>
    {
        elements.dropZone.addEventListener(type, (e) =>
        {
            e.preventDefault();
            elements.dropZone.classList.remove('dragover');
        });
    });

    elements.dropZone.addEventListener('drop', (event) =>
    {
        const files = event.dataTransfer.files;
        if (!files.length) return;
        elements.fotoReal.files = files;
        processLocalFile(files[0]);
    });

    elements.btnApplyUrl.addEventListener('click', () =>
    {
        const url = elements.imgUrlInput.value.trim();
        if (!url) return;

        elements.fotoReal.value = '';
        if (elements.hiddenUrlInput)
        {
            elements.hiddenUrlInput.value = url;
        }

        updateVisualPreview(url, 'Imagem vinculada via URL');
    });

    const formCadastro = document.getElementById('formCadastroPrato');
    if (formCadastro)
    {
        formCadastro.addEventListener('submit', function (e)
        {
            e.preventDefault();
            Swal.fire({
                title: 'Cadastrar este prato? 🍽️',
                text: 'O prato será adicionado ao cardápio.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c90879',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, cadastrar!',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then(res =>
            {
                if (res.isConfirmed)
                {
                    formCadastro.submit();
                }
            });
        });
    }
});
