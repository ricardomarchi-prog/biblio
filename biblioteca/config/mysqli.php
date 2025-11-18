<?php
// Parametros de conexao
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'biblioteca';

// Criar conexao
$conexao = mysqli_connect($host, $usuario, $senha, $banco);

// Verificar conexao
if (!$conexao) {
    die("Erro na conexao: " . mysqli_connect_error());
}

// Definir charset
mysqli_set_charset($conexao, "utf8mb4");

echo "Conexao estabelecida com sucesso!";
?>