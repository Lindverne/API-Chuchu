<?php

require __DIR__ . '/../../api/middleware/verificador.php';
VerificarLogin();

include __DIR__ . '/../../api/config/conexao.php';
require_once __DIR__ . '/../../api/helpers/categorias.php';

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$erro = $_GET['erro'] ?? '';

if ($id <= 0)
{
    header('Location: lista.php');
    exit;
}

$sql  = "SELECT c.*, cat.nome as categoria
         FROM comidinhas c
         LEFT JOIN categorias cat ON c.id_categoria = cat.id
         WHERE c.id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$prato     = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$prato)
{
    header('Location: lista.php?erro=' . urlencode('Prato não encontrado! (╥﹏╥)'));
    exit;
}

$categorias = obterCategorias($con);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../assets/img/favicon/itsuki_icone.ico">
    <title>Editar Prato | API Chuchu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/pages/editar.css">
</head>
<body data-erro="<?= htmlspecialchars($erro) ?>"
      data-selected-categoria="<?= htmlspecialchars($prato['id_categoria']) ?>">

<div class="container mt-4">

    <div class="hero-card p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark m-0">Editar Prato</h1>
            <p class="text-muted small m-0">Atualize as informações do prato (ノ°∀°)ノ</p>
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
                <a class="nav-link" href="perfil.php">
                    <i class="fa-solid fa-user-gear me-2"></i>Meu Perfil
                </a>
            </li>
        </ul>
    </div>

    <div class="card form-card p-4">
        <form action="../../api/controllers/doEditar.php" method="POST" enctype="multipart/form-data" id="formEditar">
            <input type="hidden" name="id" value="<?= $prato['id'] ?>">

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="mb-3">
                        <label class="form-label">Nome do Prato</label>
                        <input type="text" class="form-control" name="nome" id="nome"
                               value="<?= htmlspecialchars($prato['nome']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao"
                                  rows="4" required><?= htmlspecialchars($prato['descricao']) ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number" class="form-control" name="preco" id="preco"
                                   step="0.01" min="0"
                                   value="<?= number_format($prato['preco'], 2, '.', '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoria</label>
                            <select class="form-select" name="id_categoria" id="id_categoria" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= (int) $prato['id_categoria'] === (int) $cat['id'] ? 'selected' : '' ?>>
                                        <i class="fa-solid <?= $cat['icone'] ?>"></i> <?= htmlspecialchars($cat['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Foto do Prato</label>

                    <?php if (!empty($prato['imagem'])): ?>
                        <div class="text-center mb-3" id="previewAtual">
                            <img src="../assets/img/pratos/<?= htmlspecialchars($prato['imagem']) ?>"
                                 class="preview-atual"
                                 alt="Imagem atual"
                                 onerror="this.onerror=null;this.src='../assets/img/pratos/prato_padrao.png';">
                            <p class="text-muted small mt-1">Imagem atual</p>
                        </div>
                    <?php endif; ?>

                    <input type="file" class="d-none" name="foto" id="fotoInput" accept="image/*">
                    <input type="hidden" name="url_imagem" id="urlImagem" value="">

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary rounded-3 px-4" data-bs-toggle="modal" data-bs-target="#imageModal">
                            <i class="fa-solid fa-image me-2"></i>Escolher Nova Imagem
                        </button>
                    </div>
                    <span id="file-status-text" class="text-muted small ms-2">Deixe em branco para manter a imagem atual</span>

                    <div class="text-center mt-3" id="previewNova" style="display: none;">
                        <img src="" class="preview-atual" id="previewImg" alt="Nova imagem">
                        <p class="text-success small mt-1"><i class="fa-solid fa-check"></i> Nova imagem selecionada</p>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="lista.php" class="btn btn-voltar">
                    <i class="fa-solid fa-arrow-left me-1"></i>Voltar
                </a>
                <button type="submit" class="btn btn-salvar">
                    <i class="fa-solid fa-save me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../components/modal_imagem.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/pages/editar.js"></script>
<script src="../assets/js/global.js"></script>

</body>
</html>
