<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">📚 Minha Biblioteca</a>
        
        <div class="navbar-nav ms-auto align-items-center">
            <?php if (estaLogado()): ?>
                <!-- USUÁRIO LOGADO -->
                <span class="navbar-text me-3 text-white">
                    Olá, <strong><?= htmlspecialchars(nomeUsuario()) ?></strong>!
                </span>
                <a class="btn btn-outline-light" href="logout.php">Sair (Logout)</a>
            <?php else: ?>
                <!-- USUÁRIO NÃO LOGADO -->
                <a class="btn btn-outline-light" href="login.php">
                    Entrar (Login)
                </a>
                &nbsp;
                <a class="btn btn-light" href="login_cadastrar.php">
                    Cadastrar
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!--bibliteca -->
<?php
require_once 'config/database_mysqli.php';

// Consulta SQL
$sql = "SELECT l.id, l.titulo, a.nome as autor, l.ano_publicacao
        FROM livros l
        INNER JOIN autores a ON l.autor_id = a.id
        ORDER BY l.titulo";

// Executar consulta
$resultado = mysqli_query($conexao, $sql);

// Verificar se ha resultados
if (mysqli_num_rows($resultado) > 0) {
    echo "<h2>Lista de Livros</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Titulo</th><th>Autor</th><th>Ano</th></tr>";

    // Exibir cada linha
    while ($linha = mysqli_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . $linha['id'] . "</td>";
        echo "<td>" . $linha['titulo'] . "</td>";
        echo "<td>" . $linha['autor'] . "</td>";
        echo "<td>" . $linha['ano_publicacao'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Nenhum livro encontrado.";
}

// Liberar resultado e fechar conexao
mysqli_free_result($resultado);
mysqli_close($conexao);
?>