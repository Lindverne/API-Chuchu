document.addEventListener('DOMContentLoaded', function ()
{
    const imagensProdutos = document.querySelectorAll('.produto-img-table');
    const estruturaModal  = document.getElementById('previewFotoModal');

    if (estruturaModal && imagensProdutos.length > 0)
    {
        const bsModal      = new bootstrap.Modal(estruturaModal);
        const elementoNome = document.getElementById('modalNomeProduto');
        const elementoImg  = document.getElementById('modalImgPrato');

        imagensProdutos.forEach(img =>
        {
            img.addEventListener('click', function ()
            {
                elementoNome.textContent = this.getAttribute('data-product-name');
                elementoImg.src          = this.getAttribute('data-product-src') || this.src;
                bsModal.show();
            });
        });
    }

    window.confirmarExclusao = function (id, nome)
    {
        Swal.fire({
            title: 'Tem certeza? (º _ º)',
            html: `Deseja realmente excluir <strong>${nome}</strong>?<br><small class="text-muted">Esta ação não pode ser desfeita!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, deletar!',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(res =>
        {
            if (res.isConfirmed)
            {
                window.location.href = '../../api/controllers/excluir.php?id=' + encodeURIComponent(id);
            }
        });
    };

    const status = new URLSearchParams(window.location.search).get('status');

    if (status === 'cadastrado')
    {
        Swal.fire({
            title: 'Sucesso! (⌒‿⌒)',
            text: 'Prato cadastrado perfeitamente!',
            icon: 'success',
            confirmButtonColor: '#c90879',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }
    else if (status === 'atualizado')
    {
        Swal.fire({
            title: 'Atualizado! (ノ°∀°)ノ',
            text: 'As alterações foram salvas com sucesso!',
            icon: 'success',
            confirmButtonColor: '#c90879',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }
    else if (status === 'deletado')
    {
        Swal.fire({
            title: 'Deletado! (>=<)',
            text: 'O prato foi removido com sucesso!',
            icon: 'success',
            confirmButtonColor: '#c90879',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() => limparParametrosUrl());
    }
});
