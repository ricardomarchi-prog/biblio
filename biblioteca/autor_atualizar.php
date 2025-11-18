<?php
/**
 * Página de Atualização de Autor
 * 
 * Permite atualizar os dados de um autor existente no sistema.
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

$mensagem = '';

// Verifica se o ID do autor foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID do autor inválido.</div>";
    require_once 'includes/footer.php';
    exit;
}

$autor_id = intval($_GET['id']);

// Busca os dados do autor
try {
    $sql = "SELECT * FROM autores WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $autor_id, PDO::PARAM_INT);
    $stmt->execute();
    $autor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$autor) {
        echo "<div class='alert alert-danger'>Autor não encontrado.</div>";
        require_once 'includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro ao buscar autor: " . $e->getMessage() . "</div>";
    require_once 'includes/footer.php';
    exit;
}

// Processa o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparInput($_POST['nome']);
    $nacionalidade = limparInput($_POST['nacionalidade']);
    $data_nascimento = limparInput($_POST['data_nascimento']);
    $biografia = limparInput($_POST['biografia']);

    if (empty($nome)) {
        $mensagem = "<div class='alert alert-danger'>O nome do autor é obrigatório.</div>";
    } else {
        try {
            $sql_update = "UPDATE autores SET 
                nome = :nome, 
                nacionalidade = :nacionalidade, 
                data_nascimento = :data_nascimento, 
                biografia = :biografia 
                WHERE id = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(':nome', $nome);
            $stmt_update->bindParam(':nacionalidade', $nacionalidade);
            $stmt_update->bindParam(':data_nascimento', $data_nascimento);
            $stmt_update->bindParam(':biografia', $biografia);
            $stmt_update->bindParam(':id', $autor_id, PDO::PARAM_INT);
            $stmt_update->execute();

            $mensagem = "<div class='alert alert-success'>Autor atualizado com sucesso!</div>";
            // Atualiza os dados para exibir no form
            $autor = [
                'nome' => $nome,
                'nacionalidade' => $nacionalidade,
                'data_nascimento' => $data_nascimento,
                'biografia' => $biografia
            ];
        } catch (PDOException $e) {
            $mensagem = "<div class='alert alert-danger'>Erro ao atualizar autor: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<h1>📝 Atualizar Autor</h1>

<?= $mensagem ?>

<div class="card">
    <form method="POST" action="autor_atualizar.php?id=<?= $autor_id ?>">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($autor['nome']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nacionalidade">Nacionalidade:</label>
            <input type="text" id="nacionalidade" name="nacionalidade" value="<?= htmlspecialchars($autor['nacionalidade'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?= $autor['data_nascimento'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label for="biografia">Biografia:</label>
            <textarea id="biografia" name="biografia" rows="5"><?= htmlspecialchars($autor['biografia'] ?? '') ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">💾 Atualizar</button>
        <a href="autores.php" class="btn btn-secondary">← Voltar</a>
    </form>
</div>

<?php
require_once 'includes/footer.php';