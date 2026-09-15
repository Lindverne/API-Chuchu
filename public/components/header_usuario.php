<?php

if (!isset($_SESSION))
{
    session_start();
}

$fotoUsuario = $_SESSION['foto_perfil'] ?? 'avatar_padrao.png';
$nomeUsuario = $_SESSION['login'] ?? 'Usuário';

$fotoPath = !empty($fotoUsuario) && $fotoUsuario !== 'avatar_padrao.png'
    ? '../assets/img/usuarios/' . $fotoUsuario
    : '../assets/img/usuarios/avatar_padrao.png';

?>

<div class="user-panel">
    <a href="perfil.php">
        <img src="<?= htmlspecialchars($fotoPath) ?>"
             class="user-avatar"
             alt="Foto de perfil"
             onerror="this.src='../assets/img/usuarios/avatar_padrao.png';">
    </a>
    <div>
        <div class="small">Olá, <strong><?= htmlspecialchars($nomeUsuario) ?></strong></div>
        <small class="text-muted">Master Chef</small>
    </div>
    <div class="ms-auto">
        <a href="#" id="btnDeslogarSistema"
           class="btn btn-outline-danger btn-sm rounded-3 d-flex align-items-center fw-semibold"
           style="font-size: 0.8rem; line-height: 1; border-width: 1px;"
           title="Sair do Sistema">
            <i class="fa-solid fa-right-from-bracket me-1"></i>Sair
        </a>
    </div>
</div>
