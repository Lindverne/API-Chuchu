document.addEventListener('DOMContentLoaded', function ()
{
    const btnEsqueciSenha = document.getElementById('btnEsqueciSenha');

    if (btnEsqueciSenha)
    {
        btnEsqueciSenha.addEventListener('click', function (e)
        {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Aviso! ＼(〇_〇)／',
                text: 'O mecanismo de recuperação de senhas não está implementado ainda.',
                confirmButtonColor: '#c90879'
            });
        });
    }
});

function toggleSenha()
{
    const input = document.getElementById('senha');
    const icon  = document.getElementById('eyeIcon');

    if (input.type === 'password')
    {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
    else
    {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
