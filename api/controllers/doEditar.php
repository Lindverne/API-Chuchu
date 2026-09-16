<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

require_once __DIR__ . '/../middleware/verificador.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/upload_helper.php';

VerificarLogin();

$id           = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$nome         = trim($_POST['nome'] ?? '');
$descricao    = trim($_POST['descricao'] ?? '');
$preco        = isset($_POST['preco']) ? (float) $_POST['preco'] : 0;
$id_categoria = isset($_POST['id_categoria']) ? (int) $_POST['id_categoria'] : 0;
$imagemUrl    = trim($_POST['url_imagem'] ?? '');

try
{
    if ($id <= 0)
    {
        throw new Exception('ID do prato inválido! (º _ º)');
    }

    if (empty($nome) || empty($descricao))
    {
        throw new Exception('Nome e descrição são obrigatórios! (>:-<)');
    }

    if ($preco < 0)
    {
        throw new Exception('O preço não pode ser negativo! (º _ º)');
    }

    if ($id_categoria <= 0)
    {
        throw new Exception('Selecione uma categoria! (・_・;)');
    }

    $sqlCheck  = 'SELECT imagem FROM comidinhas WHERE id = ?';
    $stmtCheck = mysqli_prepare($con, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, 'i', $id);
    mysqli_stmt_execute($stmtCheck);
    $resultado   = mysqli_stmt_get_result($stmtCheck);
    $pratoAtual  = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmtCheck);

    if (!$pratoAtual)
    {
        throw new Exception('Prato não encontrado! (╥﹏╥)');
    }

    $nomeImagem = $pratoAtual['imagem'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK)
    {
        $pastaDestino = __DIR__ . '/../../public/assets/img/pratos/';
        $novaImagem   = salvarImagemLocal($_FILES['foto'], $pastaDestino);

        if ($novaImagem === false)
        {
            throw new Exception('Erro ao salvar a nova imagem!');
        }

        if (!empty($pratoAtual['imagem']))
        {
            $caminhoAntigo = $pastaDestino . $pratoAtual['imagem'];
            if (file_exists($caminhoAntigo))
            {
                unlink($caminhoAntigo);
            }
        }

        $nomeImagem = $novaImagem;
    }
    elseif (!empty($imagemUrl))
    {
        $pastaDestino = __DIR__ . '/../../public/assets/img/pratos/';
        $novaImagem   = baixarImagemDaUrl($imagemUrl, $pastaDestino);

        if ($novaImagem === false)
        {
            throw new Exception('Erro ao baixar a imagem da URL!');
        }

        if (!empty($pratoAtual['imagem']))
        {
            $caminhoAntigo = $pastaDestino . $pratoAtual['imagem'];
            if (file_exists($caminhoAntigo))
            {
                unlink($caminhoAntigo);
            }
        }

        $nomeImagem = $novaImagem;
    }

    $sql  = 'UPDATE comidinhas SET nome = ?, descricao = ?, preco = ?, id_categoria = ?, imagem = ? WHERE id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssdisi', $nome, $descricao, $preco, $id_categoria, $nomeImagem, $id);

    if (!mysqli_stmt_execute($stmt))
    {
        throw new Exception('Erro ao atualizar prato! (╥﹏╥)');
    }

    mysqli_stmt_close($stmt);

    header('Location: ../../public/pages/lista.php?status=atualizado');
    exit;
}
catch (Exception $e)
{
    header("Location: ../../public/pages/editar.php?id={$id}&erro=" . urlencode($e->getMessage()));
    exit;
}
