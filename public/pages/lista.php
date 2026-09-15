<?php

require __DIR__ . '/../../api/middleware/verificador.php';
VerificarLogin();

include __DIR__ . '/../../api/config/conexao.php';
require_once __DIR__ . '/../../api/helpers/categorias.php';

$busca      = $_GET['busca'] ?? '';
$filtro_cat = $_GET['filtro_cat'] ?? '';
$filtro_ord = $_GET['filtro_ord'] ?? '';
$status     = $_GET['status'] ?? '';
$erro       = $_GET['erro'] ?? '';

$itensPorPagina = 8;
$paginaAtual    = max(1, (int)($_GET['pagina'] ?? 1));

$sqlTotal     = "SELECT COUNT(*) as total FROM comidinhas WHERE 1=1";
$paramsTotal  = [];
$typesTotal   = '';

if (!empty($busca))
{
    $sqlTotal     .= " AND nome LIKE ?";
    $paramsTotal[] = "%{$busca}%";
    $typesTotal   .= 's';
}

if (!empty($filtro_cat))
{
    $sqlTotal     .= " AND id_categoria = ?";
    $paramsTotal[] = (int) $filtro_cat;
    $typesTotal   .= 'i';
}

$stmtTotal = mysqli_prepare($con, $sqlTotal);
if (!empty($typesTotal))
{
    mysqli_stmt_bind_param($stmtTotal, $typesTotal, ...$paramsTotal);
}
mysqli_stmt_execute($stmtTotal);
$totalRegistros = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTotal))['total'];
$totalPaginas   = max(1, (int) ceil($totalRegistros / $itensPorPagina));
$paginaAtual    = min($paginaAtual, $totalPaginas);
$offset         = ($paginaAtual - 1) * $itensPorPagina;

$sqlPratos = "SELECT c.*, cat.nome as categoria, cat.icone as categoria_icone
              FROM comidinhas c
              LEFT JOIN categorias cat ON c.id_categoria = cat.id
              WHERE 1=1";

$params = [];
$types  = '';

if (!empty($busca))
{
    $sqlPratos .= " AND c.nome LIKE ?";
    $params[]   = "%{$busca}%";
    $types     .= 's';
}

if (!empty($filtro_cat))
{
    $sqlPratos .= " AND c.id_categoria = ?";
    $params[]   = (int) $filtro_cat;
    $types     .= 'i';
}

if ($filtro_ord === 'preco_asc')
{
    $sqlPratos .= " ORDER BY c.preco ASC";
}
elseif ($filtro_ord === 'preco_desc')
{
    $sqlPratos .= " ORDER BY c.preco DESC";
}
else
{
    $sqlPratos .= " ORDER BY c.id DESC";
}

$sqlPratos .= " LIMIT ? OFFSET ?";
$params[]   = $itensPorPagina;
$types     .= 'i';
$params[]   = $offset;
$types     .= 'i';

$stmtPratos = mysqli_prepare($con, $sqlPratos);
mysqli_stmt_bind_param($stmtPratos, $types, ...$params);
mysqli_stmt_execute($stmtPratos);
$pratos = mysqli_stmt_get_result($stmtPratos);

$categorias = obterCategorias($con);

function gerarUrlPagina(int $pagina): string
{
    $p           = $_GET;
    $p['pagina'] = $pagina;
    return '?' . http_build_query($p);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../assets/img/favicon/itsuki_icone.ico">
    <title>Listagem | API Chuchu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/pages/lista.css">
</head>
<body data-status="<?= htmlspecialchars($status) ?>"
      data-erro="<?= htmlspecialchars($erro) ?>">

<div class="container mt-4">

    <div class="hero-card p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark m-0">Comidinhas</h1>
            <p class="text-muted small m-0">Gerencie seu cardápio de pratos! (⌒‿⌒)</p>
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
                <a class="nav-link active" href="#">
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

    <div class="row g-2 align-items-center mb-4">
        <div class="col-md-7 col-12">
            <form method="GET" action="lista.php" class="row g-2 justify-content-md-end align-items-center search-form">
                <div class="col-sm-4 col-6">
                    <input type="text" name="busca"
                           class="form-control form-control-sm border rounded-3"
                           placeholder="🔍 Buscar por nome..."
                           value="<?= htmlspecialchars($busca) ?>">
                </div>
                <div class="col-sm-4 col-6">
                    <select name="filtro_cat" class="form-select form-select-sm border rounded-3">
                        <option value="">Todas as categorias...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (int) $filtro_cat === (int) $cat['id'] ? 'selected' : '' ?>>
                                <i class="fa-solid <?= $cat['icone'] ?>"></i> <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="filtro-preco-wrapper">
                        <select name="filtro_ord" class="form-select form-select-sm border rounded-3">
                            <option value="">Preço...</option>
                            <option value="preco_asc" <?= $filtro_ord === 'preco_asc' ? 'selected' : '' ?>>Menor preço</option>
                            <option value="preco_desc" <?= $filtro_ord === 'preco_desc' ? 'selected' : '' ?>>Maior preço</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary text-white rounded-3 btn-filtro" title="Filtrar">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card main-card bg-white">
        <div class="table-responsive">
            <table class="table table-itsuki align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th class="text-center pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($linha = mysqli_fetch_array($pratos)): ?>
                    <tr>
                        <td class="ps-4 text-muted fw-medium">#<?= $linha['id'] ?></td>
                        <td>
                            <?php
                                $caminhoImagem = !empty($linha['imagem'])
                                    ? '../assets/img/pratos/' . htmlspecialchars($linha['imagem'])
                                    : '../assets/img/pratos/prato_padrao.png';
                            ?>
                            <img src="<?= $caminhoImagem ?>"
                                 class="produto-img-table"
                                 alt="Foto do prato"
                                 data-product-name="<?= htmlspecialchars($linha['nome']) ?>"
                                 data-product-src="<?= $caminhoImagem ?>"
                                 onerror="this.onerror=null;this.src='../assets/img/pratos/prato_padrao.png';">
                        </td>
                        <td><span class="fw-bold text-dark"><?= htmlspecialchars($linha['nome']) ?></span></td>
                        <td>
                            <span class="text-muted" style="max-width: 200px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($linha['descricao']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-categoria">
                                <?php if (!empty($linha['categoria'])): ?>
                                    <i class="fa-solid <?= htmlspecialchars($linha['categoria_icone'] ?? 'fa-utensils') ?> me-1"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($linha['categoria'] ?? 'Sem categoria') ?>
                            </span>
                        </td>
                        <td class="preco-col">
                            R$ <?= number_format((float) $linha['preco'], 2, ',', '.') ?>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group btn-group-sm rounded-3 overflow-hidden shadow-sm">
                                <a href="editar.php?id=<?= $linha['id'] ?>" class="btn btn-editar" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button type="button" class="btn btn-excluir" title="Excluir"
                                        onclick="confirmarExclusao(<?= $linha['id'] ?>, '<?= htmlspecialchars(addslashes($linha['nome'])) ?>')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPaginas > 1): ?>
    <nav aria-label="Navegação de páginas" class="mt-4 mb-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $paginaAtual <= 1 ? 'disabled' : '' ?>">
                <a class="page-link rounded-start-3" href="<?= gerarUrlPagina($paginaAtual - 1) ?>">
                    <i class="fa-solid fa-chevron-left fa-xs"></i>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= $paginaAtual === $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= gerarUrlPagina($i) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>">
                <a class="page-link rounded-end-3" href="<?= gerarUrlPagina($paginaAtual + 1) ?>">
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</div>

<div class="modal fade" id="previewFotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4 pb-2 position-relative">
                <h5 class="modal-title fw-bold text-dark w-100 text-center" id="modalNomeProduto">
                    Nome do Prato
                </h5>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center"
                 style="background: #f8f9fb; min-height: 300px;">
                <img src="" id="modalImgPrato"
                     class="img-fluid"
                     style="max-height: 460px; object-fit: contain; width: 100%;"
                     alt="Foto do Prato">
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center pb-4 pt-3"
                 style="background: #f8f9fb;">
                <button type="button" class="btn btn-primary px-5 fw-semibold rounded-3 text-white"
                        data-bs-dismiss="modal">Sair</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/pages/lista.js"></script>
<script src="../assets/js/global.js"></script>

</body>
</html>
