<?php
/**
 * config.php
 * Configurações globais, constantes, inicialização da sessão e conexão com o BD.
 */

// ========================================
// 0. CONSTANTE DE SEGURANÇA
// ========================================
// Esta constante será usada pelos arquivos incluídos para garantir que não sejam acessados diretamente.
define('DASHBOARD_INCLUIDO', true); 

// TEMPORARY DEBUG: Esta mensagem DEVE aparecer se config.php for carregado.
// Se você NUNCA vir esta mensagem, o erro está na linha "require_once 'config/config.php';"
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    echo "<p style='color:green; background-color:#e6ffe6; padding: 5px; border: 1px solid green;'>[DEBUG: config.php EXECUTADO com sucesso.]</p>";
}

// ========================================
// 1. CONSTANTES GERAIS
// ========================================
define('DEBUG_MODE', true); // Mudar para false em ambiente de produção

// Constantes para mensagens flash
define('MSG_SUCESSO', 'success');
define('MSG_ERRO', 'danger');
define('MSG_AVISO', 'warning');
define('MSG_INFO', 'info');

// NOVAS CONSTANTES PARA O SISTEMA DA BIBLIOTECA
define('PRAZO_EMPRESTIMO_DIAS', 7); // Prazo padrão de empréstimo em dias
define('VALOR_MULTA_DIA', 0.50);    // Valor da multa por dia de atraso (R$)

// ========================================
// 2. CONFIGURAÇÃO DO BANCO DE DADOS (PDO)
// ========================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'biblioteca_db'); // Mude para o nome do seu banco de dados
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ========================================
// 3. INICIALIZAÇÃO
// ========================================

// 3.1. Iniciar a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 3.2. Carregar Funções (Carregamento Absoluto CORRIGIDO)
// Usa __DIR__ para obter o caminho do diretório atual (config) e sobe um nível
$funcoes_path = __DIR__ . '/../includes/funcoes.php';

// Verifica se o arquivo existe (para ajudar no diagnóstico)
if (!file_exists($funcoes_path)) {
    die("ERRO FATAL: O arquivo de funções essenciais não foi encontrado no caminho: <strong>" . htmlspecialchars($funcoes_path) . "</strong>. Verifique se a estrutura de pastas '/includes/funcoes.php' está correta em relação ao 'config.php'.");
}

// Carrega o arquivo de funções
require_once $funcoes_path;

// 3.3. Conexão PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    // Conexão é estabelecida e o objeto $pdo fica disponível globalmente
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options); 
} catch (\PDOException $e) {
    // Se a conexão falhar, tenta usar a função de redirecionamento (se já estiver carregada)
    if (function_exists('redirecionarComMensagem')) {
        $erro_bd = defined('DEBUG_MODE') && DEBUG_MODE ? "Erro de Conexão com o BD: " . $e->getMessage() : "Erro ao conectar ao banco de dados.";
        // O redirecionamento aqui não deve ser para index.php, mas para login.php
        redirecionarComMensagem('login.php', MSG_ERRO, $erro_bd);
    } else {
         // Fallback se a função redirecionarComMensagem ainda não estiver disponível
        die("Erro de Conexão com o BD. Verifique as credenciais em config.php.");
    }
    exit;
}