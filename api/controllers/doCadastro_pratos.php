<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

require_once __DIR__ . '/../middleware/verificador.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/upload_helper.php';

VerificarLogin();

$nome         = trim($_POST['nome'] ?? '');
$descricao    = trim($_POST['descricao'] ?? '');
$preco        = isset($_POST['preco']) ? (float) $_POST['preco'] : 0;
$id_categoria = isset($_POST['id_categoria']) ? (int) $_POST['id_categoria'] : 0;
$imagemUrl    = trim($_POST['url_imagem'] ?? '');

try
{
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

    $nomeImagem = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK)
    {
        $pastaDestino = __DIR__ . '/../../public/assets/img/pratos/';
        $nomeImagem   = salvarImagemLocal($_FILES['foto'], $pastaDestino);

        if ($nomeImagem === false)
        {
            throw new Exception('Erro ao salvar a imagem! Verifique o formato (jpg, png, webp) e tamanho (máx 5MB)');
        }
    }
    elseif (!empty($imagemUrl))
    {
        $pastaDestino = __DIR__ . '/../../public/assets/img/pratos/';
        $nomeImagem   = baixarImagemDaUrl($imagemUrl, $pastaDestino);

        if ($nomeImagem === false)
        {
            throw new Exception('Erro ao baixar a imagem da URL! (╥﹏╥)');
        }
    }

    $sql  = 'INSERT INTO comidinhas (nome, descricao, preco, id_categoria, imagem)
             VALUES (?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssdis', $nome, $descricao, $preco, $id_categoria, $nomeImagem);

    if (!mysqli_stmt_execute($stmt))
    {
        throw new Exception('Erro ao cadastrar prato! (╥﹏╥)');
    }

    mysqli_stmt_close($stmt);

    header('Location: ../../public/pages/lista.php?status=cadastrado');
    exit;
}
catch (Exception $e)
{
    header('Location: ../../public/pages/cadastro_pratos.php?erro=' . urlencode($e->getMessage()));
    exit;
}
