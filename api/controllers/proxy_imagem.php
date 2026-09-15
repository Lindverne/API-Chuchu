<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

require_once __DIR__ . '/../middleware/verificador.php';
VerificarLogin();

header('Content-Type: application/json; charset=UTF-8');

$url = $_GET['url'] ?? '';

if (empty($url))
{
    http_response_code(400);
    echo json_encode(['erro' => 'URL não fornecida']);
    exit;
}

$parsedUrl = parse_url($url);
if (!$parsedUrl || !in_array($parsedUrl['scheme'] ?? '', ['http', 'https']))
{
    http_response_code(400);
    echo json_encode(['erro' => 'URL inválida']);
    exit;
}

$pastaDestino = __DIR__ . '/../../public/assets/img/usuarios/';

require_once __DIR__ . '/upload_helper.php';

$nomeImagem = baixarImagemDaUrl($url, $pastaDestino);

if ($nomeImagem === false)
{
    http_response_code(422);
    echo json_encode(['erro' => 'Não foi possível baixar a imagem da URL fornecida']);
    exit;
}

$caminhoRelativo = '../assets/img/usuarios/' . $nomeImagem;

echo json_encode([
    'sucesso' => true,
    'caminho' => $caminhoRelativo,
    'arquivo' => $nomeImagem,
]);
exit;
