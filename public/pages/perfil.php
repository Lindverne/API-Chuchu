<?php

require __DIR__ . '/../../api/middleware/verificador.php';
VerificarLogin();

include __DIR__ . '/../../api/config/conexao.php';

$id_usuario = $_SESSION['id_usuario'];
$status     = $_GET['status'] ?? '';
$erro       = $_GET['erro'] ?? '';

$sql  = "SELECT id, login, foto_perfil FROM usuarios WHERE id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario   = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$usuario)
{
    header('Location: login.php');
    exit;
}

$_SESSION['foto_perfil'] = $usuario['foto_perfil'] ?? '';

$fotoAtual = !empty($usuario['foto_perfil']) && $usuario['foto_perfil'] !== 'avatar_padrao.png'
    ? '../assets/img/usuarios/' . $usuario['foto_perfil']
    : '../assets/img/usuarios/avatar_padrao.png';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../assets/img/favicon/itsuki_icone.ico">
    <title>Meu Perfil | API Chuchu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/pages/perfil.css">
</head>
<body data-status="<?= htmlspecialchars($status) ?>" data-erro="<?= htmlspecialchars($erro) ?>">

<div class="container mt-4">

    <div class="hero-card p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark m-0">Meu Perfil</h1>
            <p class="text-muted small m-0">Atualize sua foto e dados pessoais</p>
        </div>

        <?php include __DIR__ . '/../components/header_usuario.php'; ?>
    </div>

    <div class="mb-4">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link" href="cadastro_pratos.php">
                    <i class="fa-solid fa-circle-plus text-success me-2"></i>Cadastrar Prato
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lista.php">
                    <i class="fa-solid fa-utensils me-2"></i>Lista de Pratos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">
                    <i class="fa-solid fa-user-gear me-2"></i>Meu Perfil
                </a>
            </li>
        </ul>
    </div>

    <div class="card form-card p-4">

        <form action="../../api/controllers/atualizarPerfil.php" method="POST"
              enctype="multipart/form-data" id="formPerfil">

            <input type="file" name="foto" id="fotoPerfilReal" class="d-none" accept="image/*">
            <input type="hidden" name="url_imagem" id="hiddenFotoUrl">

            <div class="row g-4">

                <div class="col-md-4 d-flex flex-column align-items-center justify-content-start gap-3">
                    <label class="form-label fw-semibold w-100 text-center">Foto Atual</label>

                    <div class="pfp-hero-wrapper">
                        <img src="<?= htmlspecialchars($fotoAtual) ?>"
                             class="pfp-atual"
                             id="pfpAtualImg"
                             alt="Foto atual"
                             onerror="this.onerror=null;this.src='../assets/img/usuarios/avatar_padrao.png';">
                        <small>Clique para ampliar</small>
                    </div>

                    <button type="button" class="btn btn-outline-primary rounded-3 px-4 w-100"
                            data-bs-toggle="modal" data-bs-target="#perfilImageModal">
                        <i class="fa-solid fa-camera me-2"></i>Trocar Foto
                    </button>
                    <span id="pfp-status-text" class="text-muted small text-center">Nenhuma imagem selecionada</span>
                </div>

                <div class="col-md-8 d-flex flex-column justify-content-start">
                    <label class="form-label fw-semibold">Pré-visualização da Nova Foto</label>
                    <div id="pfpPreviewContainer" style="display:none; text-align:center; margin-top:8px;">
                        <img src="" id="pfpPreviewImg"
                             class="pfp-preview"
                             alt="Preview">
                        <p class="text-muted small mt-2">Esta será a sua nova foto de perfil</p>
                    </div>
                    <div id="pfpPreviewEmpty" class="text-muted small mt-3">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Clique em "Trocar Foto" para escolher uma nova imagem.
                        Você poderá recortá-la em formato circular antes de salvar.
                    </div>
                </div>

                <div class="col-12 d-flex gap-3 justify-content-end pt-4 mt-2">
                    <a href="lista.php" class="btn btn-light btn-action text-muted">
                        Voltar para Lista
                    </a>
                    <button type="submit" class="btn btn-success btn-action text-white">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Salvar Foto
                    </button>
                </div>


            </div>
        </form>

        <hr class="my-4">

        <form action="../../api/controllers/atualizarPerfil.php" method="POST" id="formDadosUsuario">

            <input type="hidden" name="acao" value="dados">

            <div class="row g-4">

                <div class="col-12">
                    <h5 class="fw-bold text-dark mb-1">Alterar Dados da Conta</h5>
                    <p class="text-muted small mb-0">Deixe em branco os campos que não deseja alterar.</p>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Novo Nome de Usuário</label>
                    <input type="text"
                           name="login"
                           class="form-control"
                           value="<?= htmlspecialchars($usuario['login']) ?>"
                           placeholder="<?= htmlspecialchars($usuario['login']) ?>"
                           autocomplete="off">
                </div>

                <div class="col-12 d-flex gap-3 justify-content-end pt-4 mt-2">
                    <button type="submit" class="btn btn-action text-white"
                            style="background: var(--itsuki-primary); border-color: var(--itsuki-primary);">
                        <i class="fa-solid fa-user-pen me-2"></i>Salvar Dados
                    </button>
                </div>

            </div>
        </form>

        <hr class="my-4">

        <form action="../../api/controllers/atualizarPerfil.php" method="POST" id="formSenha">

            <input type="hidden" name="acao" value="senha">

            <div class="row g-4">

                <div class="col-12">
                    <h5 class="fw-bold text-dark mb-1">Alterar Senha</h5>
                    <p class="text-muted small mb-0">Mantenha sua conta segura. (・_・;)</p>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Senha Atual</label>
                    <input type="password"
                           name="senha_atual"
                           class="form-control"
                           placeholder="Digite sua senha atual"
                           autocomplete="current-password"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nova Senha</label>
                    <input type="password"
                           name="nova_senha"
                           class="form-control"
                           placeholder="Mínimo 4 caracteres"
                           minlength="4"
                           autocomplete="new-password"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirmar Nova Senha</label>
                    <input type="password"
                           name="confirmar_nova_senha"
                           class="form-control"
                           placeholder="Repita a nova senha"
                           minlength="4"
                           autocomplete="new-password"
                           required>
                </div>

                <div class="col-12 d-flex gap-3 justify-content-end pt-4 mt-2">
                    <button type="submit" class="btn btn-action text-white"
                            style="background: var(--color-danger); border-color: var(--color-danger);">
                        <i class="fa-solid fa-lock me-2"></i>Alterar Senha
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

<div class="modal fade" id="perfilImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Selecione a Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs border-bottom mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active small fw-bold" data-bs-toggle="tab"
                                data-bs-target="#pfpUploadPanel" type="button">Arquivo Local</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small fw-bold" data-bs-toggle="tab"
                                data-bs-target="#pfpUrlPanel" type="button">Endereço URL</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pfpUploadPanel">
                        <div class="drop-zone" id="pfpDropZone">
                            <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2" style="color: var(--itsuki-primary);"></i>
                            <p class="mb-1 fw-medium text-dark">Arraste a imagem aqui</p>
                            <span class="text-muted small">ou clique para explorar o computador</span>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pfpUrlPanel">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Cole o link completo da imagem:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="fa-solid fa-link"></i>
                                </span>
                                <input type="url" id="pfpUrlInput" class="form-control"
                                    placeholder="https://exemplo.com/foto.jpg">
                            </div>
                        </div>
                        <button type="button" id="btnAplicarUrlPerfil"
                                class="btn w-100 rounded-3 small fw-bold"
                                style="background: var(--itsuki-primary); border-color: var(--itsuki-primary); color: #fff;">
                            Confirmar Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg cropper-modal-content">

            <div class="modal-header cropper-modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-crop-simple me-2" style="color: var(--itsuki-primary);"></i>Recortar Foto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 cropper-modal-body">
                <img id="cropperImagePerfil" src="" alt="Recorte" style="max-width:100%; display:block;">
            </div>

            <div class="modal-footer border-0 justify-content-end gap-2 cropper-modal-footer">
                <button type="button" class="btn btn-light rounded-3 px-4 fw-semibold"
                        data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnSalvarCropPerfil"
                        class="btn rounded-3 px-4 fw-semibold"
                        style="background: var(--itsuki-primary); border-color: var(--itsuki-primary); color: #fff;">
                    <i class="fa-solid fa-check me-1"></i>Usar Esta Foto
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPfpZoom" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-0 text-center" style="background:#0f172a;">
                <img id="modalPfpImg" src="" alt="Foto ampliada"
                     style="max-width:100%; max-height:80vh; object-fit:contain;">
            </div>
            <div class="modal-footer border-0 justify-content-center" style="background:#0f172a;">
                <button type="button" class="btn btn-light rounded-3 px-4"
                        data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script src="../assets/js/pages/perfil.js"></script>
<script src="../assets/js/global.js"></script>

</body>
</html>
