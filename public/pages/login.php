<?php

$erro     = $_GET['erro'] ?? '';
$success  = $_GET['success'] ?? '';
$cadastro = $_GET['cadastro'] ?? '';
$logout   = $_GET['logout'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../assets/img/favicon/itsuki_icone.ico">
    <title>Login | API Chuchu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/pages/login.css">
</head>

<body>

    <div class="login-card text-center">
        <div class="brand-logo"><i class="fa-solid fa-utensils"></i></div>
        <h3 class="fw-bold text-dark mb-1">API Chuchu</h3>
        <p class="text-muted small mb-4">Seja bem-vindo de volta! (⌒‿⌒)</p>

        <form action="../../api/controllers/doLogin.php" method="POST">
            <div class="mb-3 position-relative">
                <i class="fa-solid fa-user input-group-icon"></i>
                <input type="text" class="form-control" name="login" placeholder="Seu usuário" required />
            </div>

            <div class="mb-4 position-relative">
                <i class="fa-solid fa-lock input-group-icon"></i>
                <input type="password" class="form-control" name="senha" id="senha" placeholder="Sua senha" autocomplete="current-password" required />
                <button type="button" class="password-toggle" onclick="toggleSenha()">
                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                </button>
            </div>

            <button class="btn btn-login w-100 mb-3" type="submit">
                Entrar <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
            </button>

            <div class="d-flex justify-content-between small px-1">
                <a href="#" id="btnEsqueciSenha" class="text-decoration-none fw-semibold link-danger">Esqueci a Senha</a>
                <a href="cadastro.php" class="text-decoration-none fw-semibold link-success">Criar conta</a>
            </div>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/pages/login.js"></script>
<script src="../assets/js/global.js"></script>

</body>
</html>
