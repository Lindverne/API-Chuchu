<?php

require __DIR__ . '/../../api/middleware/verificador.php';
VerificarLogin();

include __DIR__ . '/../../api/config/conexao.php';
require_once __DIR__ . '/../../api/helpers/categorias.php';

$erro       = $_GET['erro'] ?? '';
$categorias = obterCategorias($con);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../assets/img/favicon/itsuki_icone.ico">
    <title>Cadastrar Prato | API Chuchu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/pages/cadastro_pratos.css">
</head>
<body data-erro="<?= htmlspecialchars($erro) ?>">

<div class="container mt-4">

    <div class="hero-card p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark m-0">Cadastrar Prato</h1>
            <p class="text-muted small m-0">Adicione um novo prato ao cardápio! (>=<)</p>
        </div>

        <?php include __DIR__ . '/../components/header_usuario.php'; ?>
    </div>

    <div class="mb-4">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="#">
                    <i class="fa-solid fa-circle-plus text-success me-2"></i>Cadastrar Prato
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lista.php">
                    <i class="fa-solid fa-utensils me-2"></i>Lista de Pratos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="perfil.php">
                    <i class="fa-solid fa-user-gear me-2"></i>Meu Perfil
                </a>
            </li>
        </ul>
    </div>

    <div class="card form-card p-4">
        <form action="../../api/controllers/doCadastro_pratos.php" method="POST" enctype="multipart/form-data" id="formCadastroPrato">

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="mb-3">
                        <label class="form-label">Nome do Prato</label>
                        <input type="text" class="form-control" name="nome" id="nome"
                               placeholder="Ex: Pizza de 4 Queijos" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao" rows="4"
                                  placeholder="Descreva o prato..." required></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number" class="form-control" name="preco" id="preco"
                                   step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoria</label>
                            <select class="form-select" name="id_categoria" id="id_categoria" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <i class="fa-solid <?= $cat['icone'] ?>"></i> <?= htmlspecialchars($cat['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Foto do Prato</label>

                    <input type="file" class="d-none" name="foto" id="fotoInput" accept="image/*">
                    <input type="hidden" name="url_imagem" id="urlImagem" value="">

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary rounded-3 px-4" data-bs-toggle="modal" data-bs-target="#imageModal">
                            <i class="fa-solid fa-image me-2"></i>Escolher Imagem
                        </button>
                    </div>
                    <span id="file-status-text" class="text-muted small ms-2">Nenhuma imagem selecionada</span>

                    <div class="text-center mt-3" id="previewContainer" style="display: none;">
                        <img src="" class="preview-box" id="previewImg" alt="Preview">
                        <p class="text-success small mt-1"><i class="fa-solid fa-check"></i> Imagem selecionada</p>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="lista.php" class="btn btn-voltar">
                    <i class="fa-solid fa-arrow-left me-1"></i>Voltar
                </a>
                <button type="submit" class="btn btn-cadastrar">
                    <i class="fa-solid fa-save me-1"></i>Cadastrar Prato
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../components/modal_imagem.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/pages/cadastro_pratos.js"></script>
<script src="../assets/js/global.js"></script>

</body>
</html>
