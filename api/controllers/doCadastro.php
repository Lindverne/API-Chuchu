<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

require_once __DIR__ . '/../config/conexao.php';

$login = trim($_POST['login'] ?? '');
$senha = $_POST['senha'] ?? '';

processarCadastro($con, $login, $senha);

function processarCadastro(mysqli $con, string $login, string $senha): void
{
    if (empty($login) || empty($senha))
    {
        redirecionarCadastroComErro('Preencha todos os campos obrigatórios! (>:-<)');
    }

    if (strlen($senha) < 4)
    {
        redirecionarCadastroComErro('A senha deve ter pelo menos 4 caracteres! (º _ º)');
    }

    if (existeUsuario($con, $login))
    {
        redirecionarCadastroComErro('Esse usuário já existe! Tente outro (・_・;)');
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql  = 'INSERT INTO usuarios (login, senha) VALUES (?, ?)';
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt)
    {
        redirecionarCadastroComErro('Erro interno ao criar conta! (╥﹏╥)');
    }

    mysqli_stmt_bind_param($stmt, 'ss', $login, $senhaHash);
    $sucesso = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($sucesso)
    {
        header('Location: ../../public/pages/login.php?cadastro=sucesso');
        exit;
    }

    redirecionarCadastroComErro('Erro ao criar conta! Tente novamente (╥﹏╥)');
}

function existeUsuario(mysqli $con, string $login): bool
{
    $sql  = 'SELECT id FROM usuarios WHERE login = ? LIMIT 1';
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt)
    {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe    = mysqli_num_rows($resultado) > 0;
    mysqli_stmt_close($stmt);

    return $existe;
}

function redirecionarCadastroComErro(string $mensagem): void
{
    header('Location: ../../public/pages/cadastro.php?erro=' . urlencode($mensagem));
    exit;
}
