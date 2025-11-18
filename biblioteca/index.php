<?php
/**
 * index.php
 * Página inicial (Dashboard) do sistema de Biblioteca.
 * Exibe estatísticas e listas de livros.
 */

// 1. Inclui o arquivo central de configuração.
// Ele inicia a sessão, conecta ao $pdo e carrega funcoes.php.
require_once 'config/config.php'; 

// 2. OBRIGA O LOGIN para acessar o Dashboard. ESSENCIAL PARA SEGURANÇA.
// A função estaLogado() verifica se a sessão do usuário está ativa.
if (!estaLogado()) { 
    // A função redirecionarComMensagem() deve estar definida em funcoes.php
    // e usa $_SESSION para armazenar a mensagem antes do redirecionamento.
    redirecionarComMensagem('login.php', MSG_AVISO, 'Você precisa fazer login para acessar o Dashboard.');
    // O código abaixo do redirecionamento não será executado.
    exit();
}

// 3. Obtém a conexão PDO e dados do usuário
// O $pdo é globalmente acessível após o require_once.
global $pdo; 
$usuario_nome = obterNomeUsuario(); 
$usuario_perfil = obterPerfilUsuario();

// Variável para armazenar erro SQL, se houver
$erro_sql = null; 
$stats = [];
$ultimos = [];
$top = [];

try {
    // ========================================
    // ESTATÍSTICAS GERAIS: Usando uma única query para eficiência
    // ========================================
    $sql_stats = "SELECT
        (SELECT COUNT(*) FROM livros) AS total_livros,
        (SELECT SUM(quantidade_total) FROM livros) AS total_exemplares,
        (SELECT SUM(quantidade_disponivel) FROM livros) AS exemplares_disponiveis,
        (SELECT COUNT(*) FROM usuarios WHERE ativo = 1) AS total_clientes, 
        (SELECT COUNT(*) FROM emprestimos WHERE status = 'Ativo') AS emprestimos_ativos,
        (SELECT COUNT(*) FROM emprestimos WHERE status = 'Ativo' AND data_devolucao_prevista < CURDATE()) AS emprestimos_atrasados";
    $stats = $pdo->query($sql_stats)->fetch(PDO::FETCH_ASSOC);

    // ========================================
    // Últimos 5 livros cadastrados
    // ========================================
    $sql_ultimos = "SELECT l.id, l.titulo, a.nome AS autor, l.ano_publicacao, l.quantidade_disponivel, l.quantidade_total
                      FROM livros l 
                      JOIN autores a ON l.autor_id = a.id 
                      ORDER BY l.id DESC LIMIT 5";
    $stmt_ultimos = $pdo->query($sql_ultimos);
    $ultimos = $stmt_ultimos->fetchAll(PDO::FETCH_ASSOC);

    // ========================================
    // Top 5 livros mais emprestados (Considera apenas empréstimos CONCLUÍDOS ou ATIVOS, 
    // dependendo da regra de negócio, mas COUNT(e.id) é um bom indicador)
    // ========================================
    $sql_top = "SELECT l.titulo, a.nome AS autor, COUNT(e.id) AS total, l.id 
                  FROM livros l
                  JOIN autores a ON l.autor_id = a.id
                  LEFT JOIN emprestimos e ON l.id = e.livro_id
                  GROUP BY l.id, l.titulo, a.nome
                  ORDER BY total DESC LIMIT 5";
    $stmt_top = $pdo->query($sql_top);
    $top = $stmt_top->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Captura erros específicos do PDO
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log("Erro PDO no Dashboard: " . $e->getMessage()); 
        $erro_sql = "Erro ao carregar dados do banco: " . $e->getMessage();
    } else {
        $erro_sql = "Erro de banco de dados. Tente novamente mais tarde.";
    }
    
    // Define valores padrão para evitar erros na exibição HTML
    $stats = array_fill_keys(['total_livros', 'total_exemplares', 'exemplares_disponiveis', 'total_clientes', 'emprestimos_ativos', 'emprestimos_atrasados'], 0);
    $ultimos = $top = [];
} catch (Exception $e) {
    // Captura outras exceções (como se require_once falhar)
    $erro_sql = "Ocorreu um erro inesperado: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Biblioteca - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Inclui o Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Inclui os Ícones do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-hover:hover { 
            transform: translateY(-5px); 
            transition: all 0.3s ease; 
            box-shadow: 0 15px 30px rgba(0,0,0,0.25); /* Sombra mais intensa no hover */
        }
        .bg-custom-info {
            /* Cor personalizada para um contraste agradável */
            background-color: #17a2b8 !important;
        }
        .shadow-sm-hover:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1) !important;
            background-color: #f8f9fa; /* Levemente cinza no hover da lista */
        }
        .list-group-item {
            border-left: 4px solid transparent; /* Espaço para borda de destaque */
        }
        .list-group-item:hover {
             border-left-color: #0d6efd; /* Borda de destaque azul no hover */
        }
    </style>
</head>
<body class="bg-light">

<!-- NAVBAR COM LOGIN/LOGOUT -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            📚 Minha Biblioteca
        </a>
        
        <!-- Navbar Toggler para mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <!-- Links de Navegação (Verifica o perfil) -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white active" aria-current="page" href="index.php">
                        <i class="bi bi-house-door-fill me-1"></i> Início
                    </a>
                </li>
                <?php if (ehBibliotecarioOuAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="livros_listar.php">
                            <i class="bi bi-book me-1"></i> Livros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="emprestimos_listar.php">
                            <i class="bi bi-arrow-left-right me-1"></i> Empréstimos
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (ehAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="usuarios_listar.php">
                            <i class="bi bi-person-gear me-1"></i> Usuários
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center">
                <?php if (estaLogado()): ?>
                    <span class="text-white me-4 d-none d-lg-inline">
                        Olá, <strong><?= htmlspecialchars($usuario_nome) ?></strong>! (<?= ucfirst(htmlspecialchars($usuario_perfil)) ?>)
                    </span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                <?php else: ?>
                    <!-- Links só aparecem se o usuário NÃO estiver logado -->
                    <a href="login.php" class="btn btn-outline-light me-2">Entrar</a>
                    <a href="login_cadastrar.php" class="btn btn-light">Cadastrar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <!-- Exibe a mensagem flash (Se o user veio de login.php ou outra página) -->
    <?php exibirMensagemFlash(); ?> 

    <h1 class="mb-4 text-primary fw-bolder"><i class="bi bi-speedometer2"></i> Dashboard Administrativo</h1>

    <?php if ($erro_sql): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-x-octagon-fill me-2"></i> <?= htmlspecialchars($erro_sql) ?>
        </div>
    <?php endif; ?>

    <!-- ALERTA DE ATRASADOS (Apenas se o user tiver permissão para ver empréstimos) -->
    <?php if (ehBibliotecarioOuAdmin() && !empty($stats['emprestimos_atrasados']) && $stats['emprestimos_atrasados'] > 0): ?>
        <div class="alert alert-danger d-flex align-items-center rounded-3 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
            <div>
                <strong>Atenção!</strong> Existem **<?= $stats['emprestimos_atrasados'] ?>** empréstimo(s) em atraso.
                <a href="emprestimos_listar.php?filtro=atrasados" class="btn btn-sm btn-outline-danger ms-3">Ver detalhes →</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- CARDS DE ESTATÍSTICAS -->
    <div class="row g-4 mb-5">
        
        <!-- CARD 1: Títulos de Livros -->
        <div class="col-md-6 col-lg-3">
            <a href="livros_listar.php" class="text-decoration-none">
                <div class="card bg-primary text-white card-hover shadow-lg">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="display-4 fw-bold mb-0"><?= number_format($stats['total_livros'] ?? 0) ?></h2>
                                <p class="fs-5 mb-0">Títulos Cadastrados</p>
                            </div>
                            <i class="bi bi-bookshelf display-4 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- CARD 2: Exemplares Disponíveis -->
        <div class="col-md-6 col-lg-3">
            <a href="livros_listar.php" class="text-decoration-none">
                <div class="card bg-custom-info text-white card-hover shadow-lg">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="display-4 fw-bold mb-0"><?= number_format($stats['exemplares_disponiveis'] ?? 0) ?></h2>
                                <p class="fs-5 mb-0">Exemplares Disponíveis</p>
                            </div>
                            <i class="bi bi-journal-check display-4 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- CARD 3: Usuários Ativos -->
        <div class="col-md-6 col-lg-3">
            <a href="usuarios_listar.php" class="text-decoration-none">
                <div class="card bg-success text-white card-hover shadow-lg">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="display-4 fw-bold mb-0"><?= number_format($stats['total_clientes'] ?? 0) ?></h2>
                                <p class="fs-5 mb-0">Usuários Ativos</p>
                            </div>
                            <i class="bi bi-people-fill display-4 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- CARD 4: Empréstimos Ativos -->
        <div class="col-md-6 col-lg-3">
            <a href="emprestimos_listar.php" class="text-decoration-none">
                <div class="card bg-warning text-dark card-hover shadow-lg">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="display-4 fw-bold mb-0"><?= number_format($stats['emprestimos_ativos'] ?? 0) ?></h2>
                                <p class="fs-5 mb-0">Empréstimos Ativos</p>
                            </div>
                            <i class="bi bi-arrow-left-right display-4 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- TABELAS DE DETALHES -->
    <div class="row">
        <!-- ÚLTIMOS LIVROS -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold fs-5 border-bottom-0">
                    <i class="bi bi-book-half me-2"></i> Últimos 5 Livros Cadastrados
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($ultimos)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($ultimos as $l): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <!-- Link de detalhes assumido para livro_detalhes.php -->
                                        <a href="livro_detalhes.php?id=<?= $l['id'] ?>" class="fw-bold text-decoration-none text-primary"><?= htmlspecialchars($l['titulo']) ?></a><br>
                                        <small class="text-muted"><?= htmlspecialchars($l['autor']) ?> • <?= $l['ano_publicacao'] ?></small>
                                    </div>
                                    <span class="badge bg-<?= $l['quantidade_disponivel'] > 0 ? 'success' : 'danger' ?> fs-6 py-2 px-3 rounded-pill" title="Disponível / Total">
                                        <?= $l['quantidade_disponivel'] ?> / <?= $l['quantidade_total'] ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="p-4 text-center">
                            <i class="bi bi-info-circle text-muted fs-4"></i>
                            <p class="text-muted mt-2">Nenhum livro cadastrado ainda.</p>
                            <?php if (ehBibliotecarioOuAdmin()): ?>
                                <a href="livro_cadastrar.php" class="btn btn-sm btn-outline-primary mt-2">Cadastrar Primeiro Livro</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- TOP 5 MAIS EMPRESTADOS -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold fs-5 border-bottom-0">
                    <i class="bi bi-trophy me-2"></i> Top 5 Livros Mais Emprestados
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($top)): ?>
                        <ol class="list-group list-group-numbered">
                            <?php foreach ($top as $l): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center rounded-3 mb-2 shadow-sm-hover">
                                    <div>
                                        <strong class="text-dark"><?= htmlspecialchars($l['titulo']) ?></strong><br>
                                        <small class="text-muted">Autor: <?= htmlspecialchars($l['autor']) ?></small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill fs-6 py-2 px-3">
                                        <?= $l['total'] ?> empréstimos
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <div class="p-4 text-center">
                            <i class="bi bi-graph-up-arrow text-muted fs-4"></i>
                            <p class="text-muted mt-2">Nenhum empréstimo registrado ainda.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Rodapé Simples (Adicionado para dar acabamento) -->
<footer class="bg-white border-top mt-5 p-3 text-center text-muted">
    &copy; <?= date('Y') ?> Minha Biblioteca. Todos os direitos reservados.
</footer>

<!-- Inclui o Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>