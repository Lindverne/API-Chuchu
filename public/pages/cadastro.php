<?php
$erro = $_GET['erro'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../assets/img/favicon/itsuki_icone.ico">
    <title>Cadastro | API Chuchu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/pages/cadastro.css">
</head>

<body data-erro="<?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>">

    <div class="register-card text-center">
        <div class="brand-logo"><i class="fa-solid fa-user-plus"></i></div>
        <h3 class="fw-bold text-dark mb-1">Criar Conta</h3>
        <p class="text-muted small mb-4">Junte-se a nós! (ノ°∀°)ノ</p>

        <form action="../../api/controllers/doCadastro.php" method="POST" id="formCadastro">
            <div class="mb-3 position-relative">
                <i class="fa-solid fa-user input-group-icon"></i>
                <input type="text" class="form-control" name="login" id="login" placeholder="Escolha um usuário" required minlength="3" />
            </div>

            <div class="mb-3 position-relative">
                <i class="fa-solid fa-lock input-group-icon"></i>
                <input type="password" class="form-control" name="senha" id="senha" placeholder="Sua senha" autocomplete="new-password" required minlength="4" />
                <button type="button" class="password-toggle" onclick="toggleSenha('senha', 'eyeIcon1')">
                    <i class="fa-solid fa-eye" id="eyeIcon1"></i>
                </button>
            </div>

            <button class="btn btn-register w-100 mb-3" type="submit">
                Criar Conta <i class="fa-solid fa-check ms-1"></i>
            </button>

            <div class="d-flex justify-content-center px-1">
                <a href="login.php" class="text-decoration-none link-success" style="font-size: 0.85rem;">Já possui conta? Fazer Login</a>
            </div>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/pages/cadastro.js"></script>

</body>
</html>
