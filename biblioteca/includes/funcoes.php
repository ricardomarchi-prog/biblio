<?php
/**
 * funcoes.php
 * Funções utilitárias para autenticação, mensagens, navegação, segurança e formatação.
 */

// ========================================
// PROTEÇÃO CONTRA ACESSO DIRETO
// ========================================
// Usa a constante definida em config/config.php
if (!defined('DASHBOARD_INCLUIDO')) {
    header('HTTP/1.0 403 Forbidden');
    die("Acesso direto não permitido."); 
}

// ========================================
// FUNÇÕES DE SEGURANÇA E INPUT
// ========================================

/**
 * Limpa e sanitiza o input do usuário. (Compatível com a versão anterior)
 * @param mixed $dado O dado a ser limpo (string, array, etc.).
 * @return mixed O dado limpo.
 */
function limparInput($dado) {
    if (is_array($dado)) {
        return array_map('limparInput', $dado); 
    }
    // Remove tags HTML, espaços extras e barras invertidas
    $dado = trim((string)$dado);
    $dado = stripslashes($dado);
    // Usa ENT_QUOTES para codificar aspas simples e duplas
    return htmlspecialchars($dado, ENT_QUOTES, 'UTF-8');
}

/**
 * Cria o hash da senha usando bcrypt (Substitui 'gerarHashSenha').
 * @param string $senha Senha em texto puro.
 * @return string Senha hasheada.
 */
function criarHashSenha(string $senha): string {
    if (empty($senha)) {
        return '';
    }
    // Usa PASSWORD_DEFAULT que atualmente é bcrypt
    return password_hash($senha, PASSWORD_DEFAULT);
}

/**
 * Verifica se a senha fornecida corresponde ao hash armazenado (usado no login).
 * @param string $senha Senha em texto puro.
 * @param string $hash O hash armazenado no banco de dados.
 * @return bool
 */
function verificarSenha(string $senha, string $hash): bool {
    // Retorna true se a senha for válida
    return password_verify($senha, $hash);
}


// ========================================
// FUNÇÕES DE MENSAGENS FLASH
// ========================================

/**
 * Define uma mensagem para ser exibida na próxima página e redireciona.
 * (Unificado para usar as constantes do config.php: MSG_SUCESSO, MSG_ERRO, etc.)
 * @param string $pagina O caminho da página para redirecionar.
 * @param string $tipo O tipo da mensagem (MSG_SUCESSO, MSG_ERRO, etc).
 * @param string $mensagem O conteúdo da mensagem.
 */
function redirecionarComMensagem(string $pagina, string $tipo, string $mensagem): void {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Usa 'flash_message' para consistência com o login.php
    $_SESSION['flash_message'] = ['tipo' => $tipo, 'texto' => $mensagem];
    header("Location: " . $pagina);
    exit;
}

/**
 * Exibe a mensagem flash se houver e limpa a sessão. (Substitui 'verificarExibirMensagens')
 */
function exibirMensagemFlash(): void {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Mapeamento das constantes para classes CSS do Bootstrap
    $class_map = [
        MSG_SUCESSO => 'alert-success',
        MSG_ERRO    => 'alert-danger',
        MSG_AVISO   => 'alert-warning',
        MSG_INFO    => 'alert-info'
    ];

    // Usa 'flash_message' para consistência
    if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        $tipo = $msg['tipo'] ?? MSG_INFO;
        $texto = $msg['texto'] ?? '';
        
        $css_class = $class_map[$tipo] ?? 'alert-info';
        
        if (!empty($texto)) {
            // Usa 'alert-{$tipo}' no código HTML para compatibilidade com o login.php
            echo "<div class='alert {$css_class} alert-dismissible fade show' role='alert'>";
            // Ícone opcional, se quisermos mais detalhe, como na versão anterior
            // echo "<i class='bi bi-info-circle-fill me-2'></i> " . htmlspecialchars($texto); 
            echo htmlspecialchars($texto);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
        }
        
        unset($_SESSION['flash_message']);
    }
}

// ========================================
// FUNÇÕES DE AUTENTICAÇÃO E PERFIL
// ========================================

/**
 * Verifica se o usuário está logado. (Compatível com a versão anterior)
 * @return bool
 */
function estaLogado(): bool {
    // A chave da sessão foi corrigida para 'usuario_id' em login_salvar.php
    return isset($_SESSION['usuario_id']);
}

/**
 * Obtém o nome do usuário logado. (Compatível com a versão anterior)
 * @return string
 */
function obterNomeUsuario(): string {
    return $_SESSION['usuario_nome'] ?? 'Visitante';
}

/**
 * Obtém o perfil (role) do usuário logado. (Compatível com a versão anterior)
 * @return string
 */
function obterPerfilUsuario(): string {
    return $_SESSION['usuario_perfil'] ?? 'publico'; 
}

/**
 * Verifica se o usuário é Administrador. (Compatível com a versão anterior)
 * @return bool
 */
function ehAdmin(): bool {
    // Normaliza para 'admin' que é usado no SQL e login_salvar.php
    return obterPerfilUsuario() === 'administrador' || obterPerfilUsuario() === 'admin';
}

/**
 * Verifica se o usuário é Bibliotecário ou Administrador. (Compatível com a versão anterior)
 * @return bool
 */
function ehBibliotecarioOuAdmin(): bool {
    $perfil = obterPerfilUsuario();
    return $perfil === 'bibliotecario' || $perfil === 'administrador' || $perfil === 'admin';
}

// ========================================
// FUNÇÕES DE FORMATAÇÃO (NOVAS)
// ========================================

/**
 * Formata datas do tipo Y-m-d para d/m/Y.
 * @param string|null $data Data no formato SQL.
 * @return string Data formatada ou '-'.
 */
function formatarData(?string $data): string
{
    if (empty($data) || in_array($data, ['0000-00-00', 'NULL'], true)) {
        return '-';
    }
    
    try {
        $dateObj = new DateTime($data);
        return $dateObj->format('d/m/Y');
    } catch (Exception $e) {
        return '-';
    }
}

/**
 * Formata data e hora do tipo Y-m-d H:i:s para d/m/Y às H:i.
 * @param string|null $dataHora Data e hora no formato SQL.
 * @return string Data e hora formatada ou '-'.
 */
function formatarDataHora(?string $dataHora): string
{
    if (empty($dataHora) || in_array($dataHora, ['0000-00-00 00:00:00', 'NULL'], true)) {
        return '-';
    }
    
    try {
        $dateObj = new DateTime($dataHora);
        return $dateObj->format('d/m/Y \à\s H:i'); // \à\s garante que 'às' é literal
    } catch (Exception $e) {
        return '-';
    }
}

/**
 * Formata um valor numérico como moeda Real (R$).
 * @param mixed $valor Valor numérico.
 * @return string Valor formatado.
 */
function formatarMoeda($valor): string
{
    $valor_float = (float)$valor;
    return 'R$ ' . number_format($valor_float, 2, ',', '.');
}

/**
 * Formata números de telefone (10 ou 11 dígitos) no padrão (XX) XXXX-XXXX ou (XX) XXXXX-XXXX.
 * @param mixed $telefone Telefone em qualquer formato.
 * @return string Telefone formatado ou o valor original.
 */
function formatarTelefone($telefone): string
{
    $telefone_limpo = preg_replace('/\D/', '', (string)$telefone);
    $len = strlen($telefone_limpo);

    if ($len === 11) {
        // Ex: (88) 99999-8888
        return '(' . substr($telefone_limpo, 0, 2) . ') ' . substr($telefone_limpo, 2, 5) . '-' . substr($telefone_limpo, 7);
    }
    if ($len === 10) {
        // Ex: (88) 9999-8888
        return '(' . substr($telefone_limpo, 0, 2) . ') ' . substr($telefone_limpo, 2, 4) . '-' . substr($telefone_limpo, 6);
    }
    return (string)$telefone;
}

/**
 * Formata CPF no padrão XXX.XXX.XXX-XX.
 * @param mixed $cpf CPF em qualquer formato.
 * @return string CPF formatado ou o valor original.
 */
function formatarCPF($cpf): string
{
    $cpf_limpo = preg_replace('/\D/', '', (string)$cpf);
    
    if (strlen($cpf_limpo) === 11) {
        return substr($cpf_limpo, 0, 3) . '.' . substr($cpf_limpo, 3, 3) . '.' . substr($cpf_limpo, 6, 3) . '-' . substr($cpf_limpo, 9);
    }
    return (string)$cpf;
}

// ========================================
// FUNÇÕES DE CÁLCULO (NOVAS)
// ========================================

/**
 * Calcula o número de dias em atraso de uma data prevista de devolução.
 * @param string $data_prevista Data prevista de devolução (Y-m-d).
 * @return int Número de dias em atraso (0 se não estiver atrasado).
 */
function calcularDiasAtraso(string $data_prevista): int
{
    try {
        $hoje = new DateTime('today');
        $prevista = new DateTime($data_prevista);
        
        if ($hoje > $prevista) {
            $intervalo = $hoje->diff($prevista);
            return (int)$intervalo->days;
        }
        return 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Calcula o valor total da multa com base nos dias de atraso.
 * Requer que a constante VALOR_MULTA_DIA seja definida em config.php
 * @param int $dias_atraso Número de dias em atraso.
 * @return float Valor da multa (R$).
 */
function calcularMulta(int $dias_atraso): float
{
    // Verifica se a constante VALOR_MULTA_DIA está definida no config.php
    if (!defined('VALOR_MULTA_DIA') || $dias_atraso <= 0) {
        return 0.00;
    }
    
    return $dias_atraso * (float)VALOR_MULTA_DIA;
}

/**
 * Calcula a data de devolução prevista (data de empréstimo + prazo).
 * Requer que a constante PRAZO_EMPRESTIMO_DIAS seja definida em config.php
 * @param string|null $data_emprestimo Data inicial (padrão: hoje).
 * @param int|null $dias_prazo Prazo em dias (padrão: 7 dias ou PRAZO_EMPRESTIMO_DIAS).
 * @return string Data de devolução (Y-m-d).
 */
function calcularDataDevolucao(?string $data_emprestimo = null, ?int $dias_prazo = null): string
{
    try {
        $data_base = $data_emprestimo ?: date('Y-m-d');
        
        // Prioriza a constante global, senão usa o parâmetro, senão usa 7
        $prazo = $dias_prazo ?? (defined('PRAZO_EMPRESTIMO_DIAS') ? PRAZO_EMPRESTIMO_DIAS : 7);

        $dateObj = new DateTime($data_base);
        $dateObj->modify("+{$prazo} days");

        return $dateObj->format('Y-m-d');
    } catch (Exception $e) {
        return date('Y-m-d');
    }
}

// ========================================
// FUNÇÕES DE VALIDAÇÃO (NOVAS)
// ========================================

/**
 * Valida o formato básico de um e-mail.
 * @param string $email
 * @return bool
 */
function validarEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida a estrutura de um CPF brasileiro (incluindo dígitos verificadores).
 * @param string $cpf
 * @return bool
 */
function validarCPF(string $cpf): bool
{
    $cpf = preg_replace('/\D/', '', $cpf);
    
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Valida o primeiro e segundo dígito verificador
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += (int)$cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$t] != $d) {
            return false;
        }
    }
    return true;
}

// ========================================
// FUNÇÕES DE UTILIDADE (NOVAS)
// ========================================

/**
 * Resume um texto longo para um limite de caracteres.
 * @param string $texto Texto original.
 * @param int $limite Limite de caracteres.
 * @param string $complemento Complemento (e.g., '...').
 * @return string Texto resumido.
 */
function resumirTexto(string $texto, int $limite = 100, string $complemento = '...'): string
{
    if (mb_strlen($texto, 'UTF-8') <= $limite) {
        return $texto;
    }
    return mb_substr($texto, 0, $limite, 'UTF-8') . $complemento;
}

/**
 * Retorna uma classe CSS de status (usada para emprestimos).
 * @param string $status
 * @return string Classe CSS.
 */
function obterClasseStatus(string $status): string
{
    // Mapeamento para classes Bootstrap (ajustadas para cores)
    $map = [
        'Ativo'      => 'text-primary',
        'Devolvido'  => 'text-success',
        'Atrasado'   => 'text-danger',
        'Cancelado'  => 'text-muted'
    ];
    return $map[$status] ?? 'text-info';
}

/**
 * Função de debug para exibir variáveis formatadas.
 * @param mixed $variavel Variável a ser inspecionada.
 * @param bool $die Se deve interromper a execução.
 * @return void
 */
function debug($variavel, bool $die = false): void
{
    echo '<pre style="background:#fff3cd;padding:15px;border:1px solid #ffeeba;margin:15px 0;border-radius:4px;color:#856404;overflow-x:auto;">';
    var_export($variavel); 
    echo '</pre>';
    if ($die) die();
}
