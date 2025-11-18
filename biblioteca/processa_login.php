<?php
/**
 * Processa o login do usuário
 * Compatível com o config.php + estaLogado() + definirMensagem()
 */

require_once 'config.php';          // carrega sessão e funções
require_once 'config/database.php'; // conexão PDO

$pdo = Database::getInstance()->getConnection();

// Apenas requisições POST podem acessar este script
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// ------------------------- VALIDAÇÃO -------------------------
if ($email === '' || $senha === '') {
    definirMensagem('erro', 'Preencha todos os campos.');
    header('Location: login.php');
    exit;
}

// ------------------------- CONSULTA NO BANCO -------------------------
// IMPORTANTE: seu erro dizia que a tabela é "usuario" (no singular)
// Logo, vou usar a tabela correta: usuario

try {
    $stmt = $pdo->prepare("
        SELECT id, nome, email, senha, perfil
        FROM usuario
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Usuário não encontrado ou senha inválida
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        definirMensagem('erro', 'E-mail ou senha inválidos.');
        header('Location: login.php');
        exit;
    }

    // ------------------------- LOGIN BEM-SUCESSIDO -------------------------
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['usuario_nome']   = $usuario['nome'];
    $_SESSION['usuario_perfil'] = $usuario['perfil']; // admin, bibliotecario, etc.

    definirMensagem('sucesso', 'Login realizado com sucesso!');
    header('Location: index.php');
    exit;

} catch (Exception $e) {

    if (DEBUG_MODE) {
        definirMensagem('erro', 'Erro no banco de dados: ' . $e->getMessage());
    } else {
        definirMensagem('erro', 'Erro interno do sistema.');
    }

    header('Location: login.php');
    exit;
}
