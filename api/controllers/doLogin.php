<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

require_once __DIR__ . '/../config/conexao.php';

$login = trim($_POST['login'] ?? '');
$senha = $_POST['senha'] ?? '';

processarAutenticacao($con, $login, $senha);

function processarAutenticacao(mysqli $con, string $login, string $senha): void
{
    if (empty($login) || empty($senha))
    {
        redirecionarComErro('Preencha todos os campos obrigatórios! (>:-<)');
    }

    $usuario = buscarUsuarioPorLogin($con, $login);

    if (!$usuario || !password_verify($senha, $usuario['senha']))
    {
        redirecionarComErro('Usuário ou senha incorretos! (x_x)');
    }

    registrarSessaoUsuario($usuario);
    redirecionarParaPainel();
}

function buscarUsuarioPorLogin(mysqli $con, string $login): ?array
{
    $sql  = 'SELECT id, login, senha, foto_perfil FROM usuarios WHERE login = ? LIMIT 1';
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt)
    {
        return null;
    }

    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario   = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    return $usuario ?: null;
}

function registrarSessaoUsuario(array $usuario): void
{
    $_SESSION['id_usuario']  = $usuario['id'];
    $_SESSION['login']       = $usuario['login'];
    $_SESSION['foto_perfil'] = !empty($usuario['foto_perfil'])
        ? $usuario['foto_perfil']
        : 'avatar_padrao.png';
}

function redirecionarComErro(string $mensagem): void
{
    header('Location: ../../public/pages/login.php?erro=' . urlencode($mensagem));
    exit;
}

function redirecionarParaPainel(): void
{
    header('Location: ../../public/pages/lista.php?login=sucesso');
    exit;
}
