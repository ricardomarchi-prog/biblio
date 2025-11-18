<?php
/**
 * Página de Relatórios
 * 
 * Exibe diversos relatórios gerenciais do sistema:
 * - Estatísticas gerais
 * - Empréstimos por período
 * - Livros mais emprestados
 * - Clientes mais ativos
 * - Situação financeira (multas)
 * - Livros disponíveis/indisponíveis
 * 
 * @author Módulo 5 - Banco de Dados II
 * @version 1.0
 */

require_once 'config/database.php';
require_once 'config/config.php';
require_once 'includes/funcoes.php';
require_once 'includes/header.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

// ========================================
// FILTROS DE PERÍODO
// ========================================
$periodo = isset($_GET['periodo']) ? limparInput($_GET['periodo']) : 'mes_atual';
$data_inicio = '';
$data_fim = '';

// Define as datas baseado no período selecionado
switch ($periodo) {
    case 'hoje':
        $data_inicio = date('Y-m-d');
        $data_fim = date('Y-m-d');
        break;
    case 'semana':
        $data_inicio = date('Y-m-d', strtotime('-7 days'));
        $data_fim = date('Y-m-d');
        break;
    case 'mes_atual':
        $data_inicio = date('Y-m-01');
        $data_fim = date('Y-m-t');
        break;
    case 'mes_passado':
        $data_inicio = date('Y-m-01', strtotime('first day of last month'));
        $data_fim = date('Y-m-t', strtotime('last day of last month'));
        break;
    case 'ano_atual':
        $data_inicio = date('Y-01-01');
        $data_fim = date('Y-12-31');
        break;
    case 'tudo':
        $data_inicio = '2000-01-01';
        $data_fim = date('Y-m-d');
        break;
    case 'personalizado':
        $data_inicio = isset($_GET['data_inicio']) ? limparInput($_GET['data_inicio']) : date('Y-m-01');
        $data_fim = isset($_GET['data_fim']) ? limparInput($_GET['data_fim']) : date('Y-m-d');
        break;
}
?>

    <!-- Título da Página -->
    <h1>📊 Relatórios Gerenciais</h1>

    <!-- ========================================
         SELETOR DE PERÍODO
         ======================================== -->
    <div class="card">
        <h3>📅 Selecione o Período</h3>
        <form method="GET" action="relatorios.php" style="background: transparent; padding: 0;">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="periodo">Período:</label>
                        <select id="periodo" name="periodo" onchange="toggleCustomDates()">
                            <option value="hoje" <?= $periodo == 'hoje' ? 'selected' : '' ?>>Hoje</option>
                            <option value="semana" <?= $periodo == 'semana' ? 'selected' : '' ?>>Última Semana</option>
                            <option value="mes_atual" <?= $periodo == 'mes_atual' ? 'selected' : '' ?>>Mês Atual</option>
                            <option value="mes_passado" <?= $periodo == 'mes_passado' ? 'selected' : '' ?>>Mês Passado</option>
                            <option value="ano_atual" <?= $periodo == 'ano_atual' ? 'selected' : '' ?>>Ano Atual</option>
                            <option value="tudo" <?= $periodo == 'tudo' ? 'selected' : '' ?>>Todo o Período</option>
                            <option value="personalizado" <?= $periodo == 'personalizado' ? 'selected' : '' ?>>Personalizado</option>
                        </select>
                    </div>
                </div>
                
                <div class="col" id="customDates" style="display: <?= $periodo == 'personalizado' ? 'block' : 'none' ?>;">
                    <div class="form-group">
                        <label for="data_inicio">De:</label>
                        <input type="date" id="data_inicio" name="data_inicio" value="<?= $data_inicio ?>">
                    </div>
                </div>
                
                <div class="col" id="customDates2" style="display: <?= $periodo == 'personalizado' ? 'block' : 'none' ?>;">
                    <div class="form-group">
                        <label for="data_fim">Até:</label>
                        <input type="date" id="data_fim" name="data_fim" value="<?= $data_fim ?>">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-info">🔍 Gerar Relatórios</button>
            <button type="button" onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
        </form>
        
        <p style="margin-top: 15px; color: #666;">
            <strong>Período selecionado:</strong> 
            <?= formatarData($data_inicio) ?> até <?= formatarData($data_fim) ?>
        </p>
    </div>

    <script>
        function toggleCustomDates() {
            var select = document.getElementById('periodo');
            var custom = document.getElementById('customDates');
            var custom2 = document.getElementById('customDates2');
            if (select.value === 'personalizado') {
                custom.style.display = 'block';
                custom2.style.display = 'block';
            } else {
                custom.style.display = 'none';
                custom2.style.display = 'none';
            }
        }
    </script>

<?php
$error = false;
try {
    // ========================================
    // RELATÓRIO 1: ESTATÍSTICAS GERAIS
    // ========================================
    $sql_geral = "
        SELECT
            (SELECT COUNT(*) FROM livros) AS total_livros,
            (SELECT SUM(quantidade_total) FROM livros) AS total_exemplares,
            (SELECT SUM(quantidade_disponivel) FROM livros) AS exemplares_disponiveis,
            (SELECT COUNT(*) FROM clientes WHERE status = 'Ativo') AS clientes_ativos,
            (SELECT COUNT(*) FROM autores) AS total_autores,
            (SELECT COUNT(*) FROM emprestimos WHERE status = 'Ativo') AS emprestimos_ativos,
            (SELECT COUNT(*) FROM emprestimos WHERE status = 'Ativo' AND data_devolucao_prevista < CURDATE()) AS emprestimos_atrasados
    ";
    $stmt_geral = $pdo->prepare($sql_geral);
    $stmt_geral->execute();
    $stats = $stmt_geral->fetch(PDO::FETCH_ASSOC);

    // ========================================
    // RELATÓRIO 2: EMPRÉSTIMOS POR PERÍODO
    // ========================================
    $sql_emprestimos = "
        SELECT COUNT(*) AS total_emprestimos
        FROM emprestimos
        WHERE data_emprestimo BETWEEN :data_inicio AND :data_fim
    ";
    $stmt_emprestimos = $pdo->prepare($sql_emprestimos);
    $stmt_emprestimos->bindParam(':data_inicio', $data_inicio);
    $stmt_emprestimos->bindParam(':data_fim', $data_fim);
    $stmt_emprestimos->execute();
    $emprestimos_periodo = $stmt_emprestimos->fetch(PDO::FETCH_ASSOC);

    // ========================================
    // RELATÓRIO 3: LIVROS MAIS EMPRESTADOS
    // ========================================
    $sql_livros_mais = "
        SELECT l.titulo, COUNT(e.id) AS total_emprestimos
        FROM emprestimos e
        JOIN livros l ON e.livro_id = l.id
        WHERE e.data_emprestimo BETWEEN :data_inicio AND :data_fim
        GROUP BY l.id
        ORDER BY total_emprestimos DESC
        LIMIT 10
    ";
    $stmt_livros_mais = $pdo->prepare($sql_livros_mais);
    $stmt_livros_mais->bindParam(':data_inicio', $data_inicio);
    $stmt_livros_mais->bindParam(':data_fim', $data_fim);
    $stmt_livros_mais->execute();
    $livros_mais = $stmt_livros_mais->fetchAll(PDO::FETCH_ASSOC);

    // ========================================
    // RELATÓRIO 4: CLIENTES MAIS ATIVOS
    // ========================================
    $sql_clientes_mais = "
        SELECT c.nome, COUNT(e.id) AS total_emprestimos
        FROM emprestimos e
        JOIN clientes c ON e.cliente_id = c.id
        WHERE e.data_emprestimo BETWEEN :data_inicio AND :data_fim
        GROUP BY c.id
        ORDER BY total_emprestimos DESC
        LIMIT 10
    ";
    $stmt_clientes_mais = $pdo->prepare($sql_clientes_mais);
    $stmt_clientes_mais->bindParam(':data_inicio', $data_inicio);
    $stmt_clientes_mais->bindParam(':data_fim', $data_fim);
    $stmt_clientes_mais->execute();
    $clientes_mais = $stmt_clientes_mais->fetchAll(PDO::FETCH_ASSOC);

    // ========================================
    // RELATÓRIO 5: SITUAÇÃO FINANCEIRA (MULTAS)
    // ========================================
    $sql_multas = "
        SELECT SUM(multa) AS total_multas
        FROM emprestimos
        WHERE data_devolucao_prevista BETWEEN :data_inicio AND :data_fim
        AND multa > 0
    ";
    $stmt_multas = $pdo->prepare($sql_multas);
    $stmt_multas->bindParam(':data_inicio', $data_inicio);
    $stmt_multas->bindParam(':data_fim', $data_fim);
    $stmt_multas->execute();
    $multas = $stmt_multas->fetch(PDO::FETCH_ASSOC);

    // ========================================
    // RELATÓRIO 6: LIVROS DISPONÍVEIS/INDISPONÍVEIS
    // ========================================
    $sql_disponiveis = "
        SELECT titulo, quantidade_disponivel, quantidade_total - quantidade_disponivel AS indisponiveis
        FROM livros
        ORDER BY titulo
    ";
    $stmt_disponiveis = $pdo->prepare($sql_disponiveis);
    $stmt_disponiveis->execute();
    $livros_disp = $stmt_disponiveis->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro ao gerar relatórios: " . $e->getMessage() . "</div>";
    $error = true;
}

if (!$error) {
?>

    <div class="card">
        <h3>📈 Estatísticas Gerais</h3>
        <div class="row">
            <div class="col"><strong>Total de Livros:</strong> <?= $stats['total_livros'] ?></div>
            <div class="col"><strong>Total de Exemplares:</strong> <?= $stats['total_exemplares'] ?></div>
            <div class="col"><strong>Exemplares Disponíveis:</strong> <?= $stats['exemplares_disponiveis'] ?></div>
        </div>
        <div class="row">
            <div class="col"><strong>Clientes Ativos:</strong> <?= $stats['clientes_ativos'] ?></div>
            <div class="col"><strong>Total de Autores:</strong> <?= $stats['total_autores'] ?></div>
            <div class="col"><strong>Empréstimos Ativos:</strong> <?= $stats['emprestimos_ativos'] ?></div>
            <div class="col"><strong>Empréstimos Atrasados:</strong> <?= $stats['emprestimos_atrasados'] ?></div>
        </div>
    </div>

    <div class="card">
        <h3>📅 Empréstimos no Período</h3>
        <p><strong>Total de Empréstimos:</strong> <?= $emprestimos_periodo['total_emprestimos'] ?></p>
    </div>

    <div class="card">
        <h3>📚 Livros Mais Emprestados</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Total de Empréstimos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livros_mais as $livro): ?>
                    <tr>
                        <td><?= $livro['titulo'] ?></td>
                        <td><?= $livro['total_emprestimos'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>👥 Clientes Mais Ativos</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Total de Empréstimos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes_mais as $cliente): ?>
                    <tr>
                        <td><?= $cliente['nome'] ?></td>
                        <td><?= $cliente['total_emprestimos'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>💰 Situação Financeira (Multas)</h3>
        <p><strong>Total de Multas Recebidas:</strong> R$ <?= number_format($multas['total_multas'] ?? 0, 2, ',', '.') ?></p>
    </div>

    <div class="card">
        <h3>📖 Livros Disponíveis/Indisponíveis</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Disponíveis</th>
                    <th>Indisponíveis</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livros_disp as $livro): ?>
                    <tr>
                        <td><?= $livro['titulo'] ?></td>
                        <td><?= $livro['quantidade_disponivel'] ?></td>
                        <td><?= $livro['indisponiveis'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php
}
require_once 'includes/footer.php';