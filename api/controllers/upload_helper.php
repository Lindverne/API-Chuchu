<?php

function salvarImagemLocal(array $arquivo, string $pastaDestino): string|false
{
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $tamanhoMaximo       = 5 * 1024 * 1024;

    if ($arquivo['error'] !== UPLOAD_ERR_OK)
    {
        return false;
    }

    if ($arquivo['size'] > $tamanhoMaximo)
    {
        return false;
    }

    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    $mimePermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    $mime           = mime_content_type($arquivo['tmp_name']);

    if (!in_array($extensao, $extensoesPermitidas) || !in_array($mime, $mimePermitidos))
    {
        return false;
    }

    $novoNome        = uniqid('img_', true) . '.' . $extensao;
    $caminhoCompleto = $pastaDestino . $novoNome;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto))
    {
        return $novoNome;
    }

    return false;
}

function baixarImagemDaUrl(string $url, string $pastaDestino): string|false
{
    $context = stream_context_create([
        'http' => [
            'timeout'          => 10,
            'follow_location'  => true,
            'ignore_errors'    => true,
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ],
    ]);

    $conteudo = @file_get_contents($url, false, $context);

    if ($conteudo === false || strlen($conteudo) === 0)
    {
        return false;
    }

    if (!is_dir($pastaDestino) || !is_writable($pastaDestino))
    {
        return false;
    }

    $tmpFile = tempnam(sys_get_temp_dir(), 'img_');
    if ($tmpFile === false)
    {
        return false;
    }

    if (file_put_contents($tmpFile, $conteudo) === false)
    {
        @unlink($tmpFile);
        return false;
    }

    $mime = mime_content_type($tmpFile) ?: 'image/jpeg';
    @unlink($tmpFile);

    $extensoesMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    $extensao        = $extensoesMap[$mime] ?? 'jpg';
    $novoNome        = uniqid('img_', true) . '.' . $extensao;
    $caminhoCompleto = $pastaDestino . $novoNome;

    if (file_put_contents($caminhoCompleto, $conteudo))
    {
        return $novoNome;
    }

    return false;
}
