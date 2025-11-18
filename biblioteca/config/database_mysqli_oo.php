<?php
class Database {
    private $host = 'localhost';
    private $usuario = 'root';
    private $senha = '';
    private $banco = 'biblioteca';
    private $conexao;

    public function __construct() {
        $this->conexao = new mysqli(
            $this->host,
            $this->usuario,
            $this->senha,
            $this->banco
        );

        if ($this->conexao->connect_error) {
            die("Erro na conexao: " . $this->conexao->connect_error);
        }

        $this->conexao->set_charset("utf8mb4");
    }

    public function getConexao() {
        return $this->conexao;
    }

    public function fecharConexao() {
        if ($this->conexao) {
            $this->conexao->close();
        }
    }
}

// Usar a classe
$db = new Database();
$conn = $db->getConexao();
echo "Conexao estabelecida com sucesso!";
?>