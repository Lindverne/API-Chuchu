<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
{
    exit(0);
}

require_once __DIR__ . '/config/conexao.php';

function verificarAutenticacaoApi(): void
{
    if (!isset($_SESSION))
    {
        session_start();
    }

    if (!isset($_SESSION['login']))
    {
        http_response_code(401);
        echo json_encode(['erro' => 'Acesso não autorizado! Faça login primeiro. (º _ º)']);
        exit;
    }
}

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET')
{

    $id        = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $busca     = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    $categoria = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;
    $preco     = isset($_GET['preco']) ? $_GET['preco'] : '';

    if ($id > 0)
    {
        $sql = 'SELECT c.*, cat.nome as categoria
                FROM comidinhas c
                LEFT JOIN categorias cat ON c.id_categoria = cat.id
                WHERE c.id = ?';

        $stmt      = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $prato     = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);

        if ($prato)
        {
            echo json_encode($prato);
        }
        else
        {
            http_response_code(404);
            echo json_encode(['erro' => 'Prato não encontrado (・_・;)']);
        }
        exit();
    }

    $sql = 'SELECT c.*, cat.nome as categoria
            FROM comidinhas c
            LEFT JOIN categorias cat ON c.id_categoria = cat.id
            WHERE 1=1';

    $params = [];
    $types  = '';

    if (!empty($busca))
    {
        $sql     .= ' AND c.nome LIKE ?';
        $params[] = "%{$busca}%";
        $types   .= 's';
    }

    if ($categoria > 0)
    {
        $sql     .= ' AND c.id_categoria = ?';
        $params[] = $categoria;
        $types   .= 'i';
    }

    if ($preco === 'asc')
    {
        $sql .= ' ORDER BY c.preco ASC';
    }
    elseif ($preco === 'desc')
    {
        $sql .= ' ORDER BY c.preco DESC';
    }
    else
    {
        $sql .= ' ORDER BY c.id DESC';
    }

    $stmt = mysqli_prepare($con, $sql);

    if (!empty($types))
    {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $comidinhas = [];
    while ($linha = mysqli_fetch_assoc($resultado))
    {
        $comidinhas[] = $linha;
    }

    mysqli_stmt_close($stmt);

    echo json_encode($comidinhas);
    exit();
}

if ($metodo === 'POST')
{

    $dadosRequest = file_get_contents('php://input');
    $dados        = json_decode($dadosRequest, true);

    $nome         = trim($dados['nome'] ?? '');
    $descricao    = trim($dados['descricao'] ?? '');
    $preco        = isset($dados['preco']) ? (float) $dados['preco'] : 0;
    $id_categoria = isset($dados['id_categoria']) ? (int) $dados['id_categoria'] : 0;
    $imagem       = trim($dados['imagem'] ?? '');

    if (empty($nome) || empty($descricao))
    {
        http_response_code(400);
        echo json_encode(['erro' => 'Nome e descrição são obrigatórios! (>:-<)']);
        exit();
    }

    $sql  = 'INSERT INTO comidinhas (nome, descricao, preco, id_categoria, imagem)
             VALUES (?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssdis', $nome, $descricao, $preco, $id_categoria, $imagem);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode([
        'mensagem' => 'Prato cadastrado com sucesso! (⌒‿⌒)',
        'id'       => mysqli_insert_id($con),
    ]);
    exit();
}

if ($metodo === 'PUT')
{

    verificarAutenticacaoApi();

    $dadosRequest = file_get_contents('php://input');
    $dados        = json_decode($dadosRequest, true);

    $id           = isset($dados['id']) ? (int) $dados['id'] : 0;
    $nome         = trim($dados['nome'] ?? '');
    $descricao    = trim($dados['descricao'] ?? '');
    $preco        = isset($dados['preco']) ? (float) $dados['preco'] : 0;
    $id_categoria = isset($dados['id_categoria']) ? (int) $dados['id_categoria'] : 0;
    $imagem       = trim($dados['imagem'] ?? '');

    if ($id <= 0)
    {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do prato é obrigatório! (・_・;)']);
        exit();
    }

    if (empty($nome) || empty($descricao))
    {
        http_response_code(400);
        echo json_encode(['erro' => 'Nome e descrição são obrigatórios! (>:-<)']);
        exit();
    }

    $sqlCheck  = 'SELECT id FROM comidinhas WHERE id = ?';
    $stmtCheck = mysqli_prepare($con, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, 'i', $id);
    mysqli_stmt_execute($stmtCheck);
    $resultado = mysqli_stmt_get_result($stmtCheck);

    if (mysqli_num_rows($resultado) === 0)
    {
        mysqli_stmt_close($stmtCheck);
        http_response_code(404);
        echo json_encode(['erro' => 'Prato não encontrado! (╥﹏╥)']);
        exit();
    }

    mysqli_stmt_close($stmtCheck);

    if (!empty($imagem))
    {
        $sql  = 'UPDATE comidinhas SET nome = ?, descricao = ?, preco = ?, id_categoria = ?, imagem = ? WHERE id = ?';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ssdisi', $nome, $descricao, $preco, $id_categoria, $imagem, $id);
    }
    else
    {
        $sql  = 'UPDATE comidinhas SET nome = ?, descricao = ?, preco = ?, id_categoria = ? WHERE id = ?';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ssdii', $nome, $descricao, $preco, $id_categoria, $id);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(['mensagem' => 'Prato atualizado com sucesso! (ノ°∀°)ノ']);
    exit();
}

if ($metodo === 'DELETE')
{

    verificarAutenticacaoApi();

    if (!isset($_GET['id']))
    {
        http_response_code(400);
        echo json_encode(['erro' => 'Informe o ID do prato para excluir! (º _ º)']);
        exit();
    }

    $id = (int) $_GET['id'];

    if ($id <= 0)
    {
        http_response_code(400);
        echo json_encode(['erro' => 'ID inválido! (・_・;)']);
        exit();
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
        http_response_code(404);
        echo json_encode(['erro' => 'Prato não encontrado! (╥﹏╥)']);
        exit();
    }

    if (!empty($prato['imagem']))
    {
        $caminhoImagem = __DIR__ . '/../public/assets/img/pratos/' . $prato['imagem'];
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

    echo json_encode(['mensagem' => 'Prato excluído com sucesso! (>=<)']);
    exit();
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido!']);

mysqli_close($con);
