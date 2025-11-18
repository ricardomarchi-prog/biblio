<?php
/**
 * Formulário de Login - Sistema de Biblioteca
 * (Apresentação do formulário, estilizado com Bootstrap)
 */

// 1. O arquivo config.php deve ser incluído para iniciar a sessão, conexão e carregar funcoes.php
// ATENÇÃO: Se este arquivo estiver na raiz e 'config.php' estiver em um subdiretório, o caminho DEVE ser ajustado:
require_once 'config/config.php'; // Alterei para o caminho presumido: 'config/config.php'

// 2. Lógica de redirecionamento: Se já estiver logado, redireciona.
if (estaLogado()) {
    // Redireciona para o dashboard ou página principal
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Minha Biblioteca</title>
    <!-- Inclui o Bootstrap CSS para estilização -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Fundo gradiente para um visual moderno */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .card-header {
            /* Cor primária para o cabeçalho */
            background: #007bff;
            color: white;
            text-align: center;
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .btn-primary {
            background: #007bff;
            border: none;
            padding: 0.75rem;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="login-card card">
    <div class="card-header">
        <h3>📚 Minha Biblioteca</h3>
        <p class="mb-0">Acesso ao sistema</p>
    </div>
    <div class="card-body p-4">

        <!-- Lógica de Mensagens Flash: Exibe mensagens (sucesso/erro) da sessão -->
        <?php verificarExibirMensagens(); ?>

        <!-- O formulário aponta para login_salvar.php (o processador) -->
        <form action="login_salvar.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">E-mail</label>
                <input type="email" class="form-control form-control-lg rounded-3" id="email" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label fw-bold">Senha</label>
                <input type="password" class="form-control form-control-lg rounded-3" id="senha" name="senha" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">
                Entrar no Sistema
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                Ainda não tem conta? 
                <a href="login_cadastrar.php" class="text-decoration-none text-primary fw-bold">Cadastre-se aqui</a>
            </small>
        </div>

    </div>
</div>

<!-- Inclui o Bootstrap JS para que os alertas 'dismissible' funcionem -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>