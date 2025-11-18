<?php
/**
 * Arquivo de processamento do formulário de login.
 * Garante que a coluna 'perfil' seja lida corretamente.
 */

// 1. Inclui o arquivo de configuração, que deve iniciar a sessão e a conexão com o banco ($pdo)
require_once 'config/config.php'; // Certifique-se de que este caminho está correto
require_once 'includes/funcoes.php'; // Inclui as funções auxiliares

// 2. Garante que os dados foram enviados por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirecionarComMensagem('login.php', 'erro', 'Método de requisição inválido.');
}

// 3. Sanitiza e limpa os dados
$email = limparInput($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? ''; // Não limpa a senha, apenas a busca por segurança

if (empty($email) || empty($senha)) {
    redirecionarComMensagem('login.php', 'aviso', 'Por favor, preencha E-mail e Senha.');
}

// 4. Conecta e busca o usuário
try {
    // ------------------------------------------------------------------------------------------------
    // VERIFIQUE ESTA LINHA: Garante que 'perfil' está sendo selecionada.
    $sql = "SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = :email AND ativo = 1";
    // ------------------------------------------------------------------------------------------------
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Verifica se o usuário existe e se a senha está correta
    if (!$usuario || !verificarSenha($senha, $usuario['senha'])) {
        redirecionarComMensagem('login.php', 'erro', 'E-mail ou Senha incorretos.');
    }

    // 6. Login bem-sucedido: Armazena dados na sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_perfil'] = $usuario['perfil']; // Armazena o perfil na sessão

    // 7. Redireciona para a área restrita
    if ($usuario['perfil'] === 'admin') {
        redirecionarComMensagem('painel_admin.php', 'sucesso', 'Bem-vindo, Administrador!');
    } elseif ($usuario['perfil'] === 'bibliotecario') {
        redirecionarComMensagem('painel_bibliotecario.php', 'sucesso', 'Bem-vindo, Bibliotecário!');
    } else {
        redirecionarComMensagem('index.php', 'sucesso', 'Login realizado com sucesso!');
    }

} catch (PDOException $e) {
    // Se ainda falhar com erro de banco, mostre a mensagem de erro no console
    // e redirecione com uma mensagem genérica para o usuário.
    error_log("Erro no banco de dados: " . $e->getMessage());
    redirecionarComMensagem('login.php', 'erro', 'Erro no banco de dados. Tente novamente mais tarde.');
}

?>