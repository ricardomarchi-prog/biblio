<?php
// Parâmetros de conexão
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'biblioteca';

// Criar conexão
$conexao = mysqli_connect($host, $usuario, $senha, $banco);

// Verificar conexão
if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Definir charset para suportar acentos e caracteres especiais
mysqli_set_charset($conexao, "utf8mb4");
echo "Conexão estabelecida com sucesso!";

// Exemplo: Fechar conexão (opcional, PHP fecha automaticamente no fim do script)
// mysqli_close($conexao);
?>