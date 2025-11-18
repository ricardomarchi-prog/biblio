-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18/11/2025 às 02:32
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `biblioteca`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_calcular_multa` (IN `emprestimo_id` INT)   BEGIN
    DECLARE dias_atraso INT;
    DECLARE valor_multa DECIMAL(10,2);
    
    SELECT DATEDIFF(CURDATE(), data_devolucao_prevista)
    INTO dias_atraso
    FROM emprestimos
    WHERE id = emprestimo_id AND status = 'Ativo';
    
    IF dias_atraso > 0 THEN
        SET valor_multa = dias_atraso * 2.50;
        UPDATE emprestimos SET multa = valor_multa WHERE id = emprestimo_id;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `autores`
--

CREATE TABLE `autores` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `nacionalidade` varchar(50) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `biografia` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `autores`
--

INSERT INTO `autores` (`id`, `nome`, `nacionalidade`, `data_nascimento`, `biografia`, `created_at`, `updated_at`) VALUES
(1, 'Machado de Assis', 'Brasileira', '1839-06-21', 'Machado de Assis (1839-1908) é um dos maiores escritores da literatura brasileira, conhecido por sua obra inovadora e por ser o fundador do Realismo no Brasil.\r\nVida e Formação\r\nJoaquim Maria Machado de Assis nasceu no dia 21 de junho de 1839, no Morro do Livramento, no Rio de Janeiro. Filho de pais humildes, seu pai, Francisco José de Assis, era pintor, e sua mãe, Maria Leopoldina, era lavadeira. Perdeu a mãe muito cedo e foi criado por sua madrasta. Apesar das dificuldades financeiras, Machado se tornou autodidata e começou a publicar seus primeiros poemas aos 14 anos. \r\nCarreira Literária\r\nMachado de Assis começou sua carreira literária como poeta e, posteriormente, se destacou como romancista, contista, cronista e dramaturgo. Sua obra mais famosa, \"Memórias Póstumas de Brás Cubas\", publicada em 1881, é considerada um marco do Realismo e revolucionou a literatura brasileira. Ele escreveu nove romances, além de contos, crônicas e peças de teatro, abordando temas como a hipocrisia social, a condição humana e a crítica à elite burguesa. \r\nContribuições e Legado\r\nMachado de Assis foi um dos fundadores da Academia Brasileira de Letras, onde ocupou a cadeira n.º 23 e foi seu primeiro presidente. Ele é amplamente reconhecido como o maior expoente da literatura brasileira, e sua obra influenciou gerações de escritores. Sua escrita é marcada pela ironia, pela análise psicológica dos personagens e pela crítica social, refletindo as transformações políticas e sociais do Brasil no final do século XIX e início do século XX. \r\nMorte\r\nMachado de Assis faleceu em 29 de setembro de 1908, no Rio de Janeiro, deixando um legado literário que continua a ser estudado e admirado até hoje. Sua obra é fundamental para a compreensão da literatura brasileira e da formação da identidade cultural do país. \r\n\r\nMachado de Assis permanece como uma figura central na literatura mundial, sendo reconhecido por críticos e leitores como um dos maiores escritores de todos os tempos.', '2025-11-11 00:35:07', '2025-11-12 00:10:11'),
(2, 'Clarice Lispector', 'Brasileira', '1920-12-10', 'Clarice Lispector, nascida Chaya Pinkhasivna Lispector, foi uma escritora e jornalista de origem russo-judaica. Radicada no Brasil desde a primeira infância, naturalizou-se brasileira em 1943. Autora de romances, contos e ensaios, é considerada uma das escritoras brasileiras mais importantes do século XX.', '2025-11-11 00:35:07', '2025-11-13 00:04:17'),
(3, 'Jorge Amado', 'Brasileira', '1912-08-10', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(4, 'J.K. Rowling', 'Britânica', '1965-07-31', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(5, 'George Orwell', 'Britânica', '1903-06-25', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(6, 'Gabriel García Márquez', 'Colombiana', '1927-03-06', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(7, 'Agatha Christie', 'Britânica', '1890-09-15', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(8, 'Stephen King', 'Americana', '1947-09-21', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(9, 'Paulo Coelho', 'Brasileira', '1947-08-24', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(10, 'Monteiro Lobato', 'Brasileira', '1882-04-18', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `status` enum('Ativo','Inativo','Bloqueado') DEFAULT 'Ativo',
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `cpf`, `data_nascimento`, `endereco`, `cidade`, `estado`, `cep`, `status`, `observacoes`, `created_at`, `updated_at`) VALUES
(1, 'Ana Silva Santos', 'ana.silva@email.com', '(11) 98765-4321', '123.456.789-00', '1995-03-15', 'Rua das Flores, 123', 'São Paulo', 'SP', '01234-567', 'Ativo', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(2, 'Carlos Eduardo Souza', 'carlos.souza@email.com', '(11) 97654-3210', '234.567.890-11', '1988-07-22', 'Av. Paulista, 1000', 'São Paulo', 'SP', '01310-100', 'Ativo', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(3, 'Maria Oliveira Lima', 'maria.lima@email.com', '(21) 99876-5432', '345.678.901-22', '1992-11-08', 'Rua Copacabana, 456', 'Rio de Janeiro', 'RJ', '22070-000', 'Ativo', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(4, 'João Pedro Costa', 'joao.costa@email.com', '(41) 98765-1234', '456.789.012-33', '2000-05-30', 'Rua XV de Novembro, 789', 'Curitiba', 'PR', '80020-310', 'Ativo', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(5, 'Juliana Fernandes', 'juliana.fernandes@email.com', '(11) 96543-2109', '567.890.123-44', '1985-09-12', 'Rua Augusta, 2500', 'São Paulo', 'SP', '01412-100', 'Ativo', NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(6, 'Ricardo De marchi', 'ricardodemarchi3@gmail.com', '45999877851', NULL, NULL, NULL, NULL, NULL, NULL, 'Ativo', NULL, '2025-11-11 23:00:01', '2025-11-11 23:00:01'),
(8, 'joao', 'ricardodemarchi3@gmail2.com', '(45)999877851', NULL, NULL, NULL, NULL, NULL, NULL, 'Ativo', NULL, '2025-11-11 23:07:14', '2025-11-11 23:07:14'),
(9, 'luara rocha', 'luara123@gmail.com', '45908490583', NULL, NULL, NULL, NULL, NULL, NULL, 'Ativo', NULL, '2025-11-17 23:37:47', '2025-11-17 23:37:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `emprestimos`
--

CREATE TABLE `emprestimos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `data_emprestimo` date NOT NULL,
  `data_devolucao_prevista` date NOT NULL,
  `data_devolucao_real` date DEFAULT NULL,
  `status` enum('Ativo','Devolvido','Atrasado','Cancelado') DEFAULT 'Ativo',
  `multa` decimal(10,2) DEFAULT 0.00,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `emprestimos`
--

INSERT INTO `emprestimos` (`id`, `cliente_id`, `livro_id`, `data_emprestimo`, `data_devolucao_prevista`, `data_devolucao_real`, `status`, `multa`, `observacoes`, `created_at`, `updated_at`) VALUES
(1, 1, 7, '2025-11-01', '2025-11-08', NULL, 'Ativo', 0.00, NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(2, 2, 9, '2025-11-03', '2025-11-10', NULL, 'Ativo', 0.00, NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(3, 3, 3, '2025-10-28', '2025-11-04', NULL, 'Ativo', 0.00, NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(4, 4, 5, '2025-10-25', '2025-11-01', NULL, 'Devolvido', 0.00, NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(5, 5, 12, '2025-11-02', '2025-11-09', NULL, 'Ativo', 0.00, NULL, '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(6, 6, 12, '2025-11-12', '2025-11-19', NULL, 'Ativo', 0.00, NULL, '2025-11-11 23:40:26', '2025-11-11 23:40:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `exemplares`
--

CREATE TABLE `exemplares` (
  `id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL COMMENT 'Código único do exemplar (ex.: ISBN-001)',
  `status` enum('Disponivel','Emprestado','Manutencao','Danificado') DEFAULT 'Disponivel',
  `data_aquisicao` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `autor_id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `ano_publicacao` year(4) DEFAULT NULL,
  `editora` varchar(100) DEFAULT NULL,
  `numero_paginas` int(11) DEFAULT NULL,
  `quantidade_total` int(11) DEFAULT 1,
  `quantidade_disponivel` int(11) DEFAULT 1,
  `categoria` varchar(50) DEFAULT NULL,
  `localizacao` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `autor_id`, `isbn`, `ano_publicacao`, `editora`, `numero_paginas`, `quantidade_total`, `quantidade_disponivel`, `categoria`, `localizacao`, `created_at`, `updated_at`) VALUES
(1, 'Dom Casmurro', 1, '978-8535911664', '0000', 'Companhia das Letras', 256, 3, 3, 'Romance', 'Estante A1', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(2, 'Memórias Póstumas de Brás Cubas', 1, '978-8535911671', '0000', 'Companhia das Letras', 368, 2, 2, 'Romance', 'Estante A1', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(3, 'A Hora da Estrela', 2, '978-8520925683', '1977', 'Rocco', 88, 2, 0, 'Romance', 'Estante A2', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(4, 'A Paixão Segundo G.H.', 2, '978-8532511010', '1964', 'Rocco', 176, 1, 1, 'Romance', 'Estante A2', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(5, 'Capitães da Areia', 3, '978-8535914063', '1937', 'Companhia das Letras', 280, 4, 3, 'Romance', 'Estante A3', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(6, 'Gabriela, Cravo e Canela', 3, '978-8535911046', '1958', 'Companhia das Letras', 424, 2, 2, 'Romance', 'Estante A3', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(7, 'Harry Potter e a Pedra Filosofal', 4, '978-8532530787', '1997', 'Rocco', 264, 5, 3, 'Fantasia', 'Estante B1', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(8, 'Harry Potter e a Câmara Secreta', 4, '978-8532530794', '1998', 'Rocco', 288, 3, 3, 'Fantasia', 'Estante B1', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(9, '1984', 5, '978-8535914849', '1949', 'Companhia das Letras', 416, 4, 2, 'Ficção', 'Estante B2', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(10, 'A Revolução dos Bichos', 5, '978-8535909555', '1945', 'Companhia das Letras', 152, 3, 3, 'Ficção', 'Estante B2', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(11, 'Cem Anos de Solidão', 6, '978-8501061294', '1967', 'Record', 424, 2, 2, 'Romance', 'Estante C1', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(12, 'Assassinato no Expresso do Oriente', 7, '978-8595084841', '1934', 'HarperCollins', 256, 3, 0, 'Mistério', 'Estante C2', '2025-11-11 00:35:07', '2025-11-11 23:40:26'),
(13, 'O Iluminado', 8, '978-8581050584', '1977', 'Suma', 464, 2, 1, 'Terror', 'Estante C3', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(14, 'O Alquimista', 9, '978-8522008865', '1988', 'Rocco', 224, 5, 5, 'Ficção', 'Estante D1', '2025-11-11 00:35:07', '2025-11-11 00:35:07'),
(15, 'O Sítio do Picapau Amarelo', 10, '978-8525406293', '1920', 'Globo', 288, 4, 4, 'Infantil', 'Estante D2', '2025-11-11 00:35:07', '2025-11-11 00:35:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) DEFAULT NULL,
  `perfil` enum('admin','bibliotecario','membro') NOT NULL DEFAULT 'membro',
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `perfil`, `ativo`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@biblioteca.com', '$2y$10$JDJ5JDEwJEdVR2dDek9tZ2V1L3VQaXouTzR1d1V5bFF6b2ZxN2p5d2V1bA==', 'admin', 1, '2025-11-17 22:15:26');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_emprestimos_completos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_emprestimos_completos` (
`id` int(11)
,`data_emprestimo` date
,`data_devolucao_prevista` date
,`data_devolucao_real` date
,`status` enum('Ativo','Devolvido','Atrasado','Cancelado')
,`multa` decimal(10,2)
,`cliente_id` int(11)
,`cliente_nome` varchar(150)
,`cliente_email` varchar(150)
,`cliente_telefone` varchar(20)
,`livro_id` int(11)
,`livro_titulo` varchar(200)
,`livro_isbn` varchar(20)
,`autor_nome` varchar(150)
,`dias_atraso` int(7)
,`multa_calculada` decimal(9,2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_estatisticas_biblioteca`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_estatisticas_biblioteca` (
`total_livros` bigint(21)
,`total_exemplares` decimal(32,0)
,`exemplares_disponiveis` decimal(32,0)
,`clientes_ativos` bigint(21)
,`emprestimos_ativos` bigint(21)
,`emprestimos_atrasados` bigint(21)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_emprestimos_completos`
--
DROP TABLE IF EXISTS `vw_emprestimos_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_emprestimos_completos`  AS SELECT `e`.`id` AS `id`, `e`.`data_emprestimo` AS `data_emprestimo`, `e`.`data_devolucao_prevista` AS `data_devolucao_prevista`, `e`.`data_devolucao_real` AS `data_devolucao_real`, `e`.`status` AS `status`, `e`.`multa` AS `multa`, `c`.`id` AS `cliente_id`, `c`.`nome` AS `cliente_nome`, `c`.`email` AS `cliente_email`, `c`.`telefone` AS `cliente_telefone`, `l`.`id` AS `livro_id`, `l`.`titulo` AS `livro_titulo`, `l`.`isbn` AS `livro_isbn`, `a`.`nome` AS `autor_nome`, to_days(curdate()) - to_days(`e`.`data_devolucao_prevista`) AS `dias_atraso`, CASE WHEN `e`.`status` = 'Ativo' AND curdate() > `e`.`data_devolucao_prevista` THEN (to_days(curdate()) - to_days(`e`.`data_devolucao_prevista`)) * 2.50 ELSE 0 END AS `multa_calculada` FROM (((`emprestimos` `e` join `clientes` `c` on(`e`.`cliente_id` = `c`.`id`)) join `livros` `l` on(`e`.`livro_id` = `l`.`id`)) join `autores` `a` on(`l`.`autor_id` = `a`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_estatisticas_biblioteca`
--
DROP TABLE IF EXISTS `vw_estatisticas_biblioteca`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_estatisticas_biblioteca`  AS SELECT (select count(0) from `livros`) AS `total_livros`, (select sum(`livros`.`quantidade_total`) from `livros`) AS `total_exemplares`, (select sum(`livros`.`quantidade_disponivel`) from `livros`) AS `exemplares_disponiveis`, (select count(0) from `clientes` where `clientes`.`status` = 'Ativo') AS `clientes_ativos`, (select count(0) from `emprestimos` where `emprestimos`.`status` = 'Ativo') AS `emprestimos_ativos`, (select count(0) from `emprestimos` where `emprestimos`.`status` = 'Ativo' and `emprestimos`.`data_devolucao_prevista` < curdate()) AS `emprestimos_atrasados` ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `autores`
--
ALTER TABLE `autores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_nome` (`nome`),
  ADD KEY `idx_status` (`status`);

--
-- Índices de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_livro` (`livro_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_data_emprestimo` (`data_emprestimo`),
  ADD KEY `idx_data_devolucao` (`data_devolucao_prevista`);

--
-- Índices de tabela `exemplares`
--
ALTER TABLE `exemplares`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `livro_id` (`livro_id`);

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `idx_titulo` (`titulo`),
  ADD KEY `idx_autor` (`autor_id`),
  ADD KEY `idx_isbn` (`isbn`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `autores`
--
ALTER TABLE `autores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `exemplares`
--
ALTER TABLE `exemplares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD CONSTRAINT `emprestimos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `emprestimos_ibfk_2` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`);

--
-- Restrições para tabelas `exemplares`
--
ALTER TABLE `exemplares`
  ADD CONSTRAINT `exemplares_ibfk_1` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `livros`
--
ALTER TABLE `livros`
  ADD CONSTRAINT `livros_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `autores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
