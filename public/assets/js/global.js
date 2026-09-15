document.addEventListener('DOMContentLoaded', () =>
{
    iniciarBotaoSobre();
    iniciarLogout();
    tratarAlertasAuth();
});

function iniciarBotaoSobre()
{
    const btn = document.createElement('button');
    btn.id                 = 'btnSobreOSite';
    btn.title              = 'Sobre o Site';
    btn.style.width        = '56px';
    btn.style.height       = '56px';
    btn.style.padding      = '0';
    btn.style.overflow     = 'hidden';
    btn.style.borderRadius = '50%';
    btn.style.display       = 'flex';
    btn.style.alignItems    = 'center';
    btn.style.justifyContent = 'center';

    const img       = document.createElement('img');
    img.src         = '../assets/img/global/botao.gif';
    img.alt         = 'Sobre o Site';
    img.style.width        = '100%';
    img.style.height       = '100%';
    img.style.objectFit    = 'cover';
    img.style.borderRadius = '50%';
    btn.appendChild(img);
    document.body.appendChild(btn);

    btn.addEventListener('click', () =>
    {
        Swal.fire({
            title: 'API Chuchu',
            imageUrl: '../assets/img/global/sobre_api.gif',
            imageWidth: 150,
            imageHeight: 150,
            imageAlt: 'Sobre o projeto e os desenvolvedores',
            html: `
                <p class="mb-1">Projeto de API para restaurantes 💖</p>
                <hr>
                <p class="small text-muted mb-0">Desenvolvido por:</p>
                <br>
                <p class="small text-muted mb-0">Matheus Guedes - N°33</p>
            `,
            confirmButtonText: 'Fechar',
            confirmButtonColor: '#c90879',
            customClass: { popup: 'rounded-4 shadow-lg' },
            didOpen: (popup) =>
            {
                const img = popup.querySelector('.swal2-image');
                if (img)
                {
                    img.classList.add('swal2-image-round');
                }
            }
        }).then(() =>
        {
            limparParametrosUrl();
        });
        return;
    });
}

function iniciarLogout()
{
    const btnSair = document.getElementById('btnDeslogarSistema');
    if (!btnSair) return;

    btnSair.addEventListener('click', function (e)
    {
        e.preventDefault();
        Swal.fire({
            title: 'Deseja realmente sair? (・_・;)',
            text: 'Sua sessão atual no painel será encerrada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sair',
            cancelButtonText: 'Permanecer',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(res =>
        {
            if (res.isConfirmed)
            {
                window.location.href = '../../api/controllers/logout.php';
            }
        });
    });
}

function tratarAlertasAuth()
{
    const urlParams   = new URLSearchParams(window.location.search);
    const loginStatus = urlParams.get('login');
    const cadastro    = urlParams.get('cadastro');
    const logout      = urlParams.get('logout');
    const erro        = urlParams.get('erro');

    if (loginStatus === 'sucesso')
    {
        Swal.fire({
            title: 'Bem-vindo! (≧∇≦)ノ',
            text: 'Login efetuado com sucesso!',
            imageUrl: '../assets/img/global/login_sucesso.gif',
            imageWidth: 150,
            imageHeight: 150,
            imageAlt: 'Login realizado',
            confirmButtonText: 'Começar',
            confirmButtonColor: '#c90879',
            customClass: { popup: 'rounded-4 shadow-lg' },
            didOpen: (popup) =>
            {
                const img = popup.querySelector('.swal2-image');
                if (img)
                {
                    img.classList.add('swal2-image-round');
                }
            }
        }).then(() =>
        {
            limparParametrosUrl();
        });
        return;
    }

    if (cadastro === 'sucesso')
    {
        Swal.fire({
            title: 'Conta criada! (⌒‿⌒)',
            text: 'Cadastro realizado com sucesso! Faça login para continuar.',
            imageUrl: '../assets/img/global/cadastro_sucesso.gif',
            imageWidth: 150,
            imageHeight: 150,
            imageAlt: 'Cadastro realizado',
            confirmButtonText: 'Ir para Login',
            confirmButtonColor: '#c90879',
            customClass: { popup: 'rounded-4 shadow-lg' },
            didOpen: (popup) =>
            {
                const img = popup.querySelector('.swal2-image');
                if (img)
                {
                    img.classList.add('swal2-image-round');
                }
            }
        }).then(() =>
        {
            limparParametrosUrl();
        });
        return;
    }

    if (logout === 'sucesso')
    {
        Swal.fire({
            title: 'Sessão encerrada! (╥﹏╥)',
            text: 'Você saiu do sistema. Até logo!',
            imageUrl: '../assets/img/global/logout.gif',
            imageWidth: 150,
            imageHeight: 150,
            imageAlt: 'Até logo',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#6c757d',
            customClass: { popup: 'rounded-4 shadow-lg' },
            didOpen: (popup) =>
            {
                const img = popup.querySelector('.swal2-image');
                if (img)
                {
                    img.classList.add('swal2-image-round');
                }
            }
        }).then(() =>
        {
            limparParametrosUrl();
        });
        return;
    }

    if (erro)
    {
        Swal.fire({
            icon: 'error',
            title: 'Oops... (>-<)',
            text: decodeURIComponent(erro),
            confirmButtonColor: '#dc3545',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(() =>
        {
            limparParametrosUrl();
        });
    }
}

function limparParametrosUrl()
{
    if (window.history.replaceState)
    {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}
