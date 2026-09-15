<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

@ini_set('upload_max_filesize', '5M');
@ini_set('post_max_size', '10M');

require_once __DIR__ . '/../middleware/verificador.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/upload_helper.php';

VerificarLogin();

$id_usuario = $_SESSION['id_usuario'];
$acao       = $_POST['acao'] ?? 'foto';

try
{
    if ($acao === 'senha')
    {
        $senhaAtual     = $_POST['senha_atual'] ?? '';
        $novaSenha      = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_nova_senha'] ?? '';

        if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarSenha))
        {
            throw new Exception('Preencha todos os campos de senha! (>:-<)');
        }

        if (strlen($novaSenha) < 4)
        {
            throw new Exception('A nova senha deve ter pelo menos 4 caracteres! (º _ º)');
        }

        if ($novaSenha !== $confirmarSenha)
        {
            throw new Exception('As senhas não coincidem! (・_・;)');
        }

        $sqlSenha  = 'SELECT senha FROM usuarios WHERE id = ? LIMIT 1';
        $stmtSenha = mysqli_prepare($con, $sqlSenha);
        mysqli_stmt_bind_param($stmtSenha, 'i', $id_usuario);
        mysqli_stmt_execute($stmtSenha);
        $resultadoSenha = mysqli_stmt_get_result($stmtSenha);
        $linhaSenha     = mysqli_fetch_assoc($resultadoSenha);
        mysqli_stmt_close($stmtSenha);

        if (!$linhaSenha || !password_verify($senhaAtual, $linhaSenha['senha']))
        {
            throw new Exception('A senha atual está incorreta! (╥﹏╥)');
        }

        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $sqlUpdate     = 'UPDATE usuarios SET senha = ? WHERE id = ?';
        $stmtUpdate    = mysqli_prepare($con, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, 'si', $novaSenhaHash, $id_usuario);

        if (!mysqli_stmt_execute($stmtUpdate))
        {
            throw new Exception('Erro ao alterar senha! (╥﹏╥)');
        }

        mysqli_stmt_close($stmtUpdate);

        header('Location: ../../public/pages/perfil.php?senha_status=sucesso');
        exit;
    }
    elseif ($acao === 'dados')
    {
        $login = trim($_POST['login'] ?? '');

        if (empty($login))
        {
            throw new Exception('O nome de usuário não pode ficar vazio! (>:-<)');
        }

        $sqlCheck  = 'SELECT id FROM usuarios WHERE login = ? AND id != ? LIMIT 1';
        $stmtCheck = mysqli_prepare($con, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, 'si', $login, $id_usuario);
        mysqli_stmt_execute($stmtCheck);
        $resultado = mysqli_stmt_get_result($stmtCheck);

        if (mysqli_num_rows($resultado) > 0)
        {
            mysqli_stmt_close($stmtCheck);
            throw new Exception('Esse nome de usuário já está em uso! (・_・;)');
        }
        mysqli_stmt_close($stmtCheck);

        $sql  = 'UPDATE usuarios SET login = ? WHERE id = ?';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $login, $id_usuario);

        if (!mysqli_stmt_execute($stmt))
        {
            throw new Exception('Erro ao atualizar dados! (╥﹏╥)');
        }

        mysqli_stmt_close($stmt);

        $_SESSION['login'] = $login;

        header('Location: ../../public/pages/perfil.php?dados_status=sucesso');
        exit;
    }
    else
    {
        $nomeImagem = $_SESSION['foto_perfil'] ?? '';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK)
        {
            $pastaDestino = __DIR__ . '/../../public/assets/img/usuarios/';
            $novaImagem   = salvarImagemLocal($_FILES['foto'], $pastaDestino);

            if ($novaImagem === false)
            {
                throw new Exception('Erro ao salvar a foto! Verifique formato e tamanho.');
            }

            if (!empty($_SESSION['foto_perfil']) && $_SESSION['foto_perfil'] !== 'avatar_padrao.png')
            {
                $caminhoAntigo = $pastaDestino . $_SESSION['foto_perfil'];
                if (file_exists($caminhoAntigo))
                {
                    unlink($caminhoAntigo);
                }
            }

            $nomeImagem = $novaImagem;
        }
        elseif (!empty($_POST['url_imagem']))
        {
            $pastaDestino = __DIR__ . '/../../public/assets/img/usuarios/';
            $urlOuArquivo = $_POST['url_imagem'];

            $caminhoExistente = $pastaDestino . $urlOuArquivo;
            if (file_exists($caminhoExistente))
            {
                $novaImagem = $urlOuArquivo;
            }
            else
            {
                $novaImagem = baixarImagemDaUrl($urlOuArquivo, $pastaDestino);
            }

            if ($novaImagem === false)
            {
                throw new Exception('Não foi possível obter a imagem. Verifique o link. (・_・;)');
            }

            if (!empty($_SESSION['foto_perfil']) && $_SESSION['foto_perfil'] !== 'avatar_padrao.png')
            {
                $caminhoAntigo = $pastaDestino . $_SESSION['foto_perfil'];
                if (file_exists($caminhoAntigo))
                {
                    unlink($caminhoAntigo);
                }
            }
            $nomeImagem = $novaImagem;
        }

        $sql  = 'UPDATE usuarios SET foto_perfil = ? WHERE id = ?';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $nomeImagem, $id_usuario);

        if (!mysqli_stmt_execute($stmt))
        {
            throw new Exception('Erro ao atualizar foto! (╥﹏╥)');
        }

        mysqli_stmt_close($stmt);

        $_SESSION['foto_perfil'] = $nomeImagem;

        header('Location: ../../public/pages/perfil.php?status=sucesso');
        exit;
    }
}
catch (Exception $e)
{
    if ($acao === 'senha')
    {
        $paramErro = 'senha_erro';
    }
    elseif ($acao === 'dados')
    {
        $paramErro = 'dados_erro';
    }
    else
    {
        $paramErro = 'erro';
    }
    header("Location: ../../public/pages/perfil.php?{$paramErro}=" . urlencode($e->getMessage()));
    exit;
}
