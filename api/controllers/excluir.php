<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

require_once __DIR__ . '/../middleware/verificador.php';
require_once __DIR__ . '/../config/conexao.php';

VerificarLogin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0)
{
    header('Location: ../../public/pages/lista.php?erro=' . urlencode('ID inválido! (º _ º)'));
    exit;
}

$sqlCheck  = 'SELECT id, imagem FROM comidinhas WHERE id = ?';
$stmtCheck = mysqli_prepare($con, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, 'i', $id);
mysqli_stmt_execute($stmtCheck);
$resultado = mysqli_stmt_get_result($stmtCheck);
$prato     = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmtCheck);

if (!$prato)
{
    header('Location: ../../public/pages/lista.php?erro=' . urlencode('Prato não encontrado! (╥﹏╥)'));
    exit;
}

if (!empty($prato['imagem']))
{
    $caminhoImagem = __DIR__ . '/../../public/assets/img/pratos/' . $prato['imagem'];
    if (file_exists($caminhoImagem))
    {
        unlink($caminhoImagem);
    }
}

$sql  = 'DELETE FROM comidinhas WHERE id = ?';
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: ../../public/pages/lista.php?status=deletado');
exit;
