-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 14/09/2026 às 00:53
-- Versão do servidor: 12.3.3-MariaDB
-- Versão do PHP: 8.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `comidinhas`
--
CREATE DATABASE IF NOT EXISTS `comidinhas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `comidinhas`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `icone` varchar(100) NOT NULL DEFAULT 'fa-utensils'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `icone`) VALUES
(1, 'Pizzas', 'fa-pizza-slice'),
(2, 'Hambúrgueres', 'fa-burger'),
(3, 'Massas', 'fa-bowl-food'),
(4, 'Salgados Fritos', 'fa-cookie-bite'),
(5, 'Bebidas', 'fa-glass-water'),
(6, 'Sobremesas', 'fa-ice-cream'),
(7, 'Lanches', 'fa-hotdog'),
(8, 'Açaí', 'fa-bowl-food'),
(9, 'Sucos', 'fa-blender'),
(10, 'Cafés', 'fa-mug-hot');

-- --------------------------------------------------------

--
-- Estrutura para tabela `comidinhas`
--

DROP TABLE IF EXISTS `comidinhas`;
CREATE TABLE `comidinhas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_categoria` int(11) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `comidinhas`
--

INSERT INTO `comidinhas` (`id`, `nome`, `descricao`, `preco`, `id_categoria`, `imagem`) VALUES
(1, 'Pizza de Mussarela', 'Pizza artesanal com mussarela derretida', 35.90, 1, 'pizza_mussarela.png'),
(2, 'Hambúrguer Artesanal', 'Hambúrguer com blend especial e molho secreto', 28.50, 2, 'hamburguer_artesanal.png'),
(3, 'Lasanha Bolonhesa', 'Lasanha caseira com molho bolonhesa e queijo gratinado', 32.00, 3, 'lasanha_bolonhesa.png'),
(4, 'Pastel de Frango', 'Pastel crocante recheado com frango e catupiry', 8.50, 4, 'pastel_frango.png'),
(5, 'Coxinha de Frango', 'Coxinha cremosa com catupiry derretido', 6.00, 4, 'coxinha_frango.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT 'avatar_padrao.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `login`, `senha`, `foto_perfil`) VALUES
(1, 'admin', '$2y$10$8K1p/a0dL1LXMc.0.6ZBju7B5KJr7tFzSqqL6z1w6vFv20G5i4b6m', 'avatar_padrao.png'),
(2, 'Lindverne', '$2y$12$V0kr11CJm5H3YKUfS1g.y.D0FSBUQXBmBnNBK7IqZE6rEx2KO3QuG', 'avatar_padrao.png');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `comidinhas`
--
ALTER TABLE `comidinhas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria` (`id_categoria`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_login` (`login`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `comidinhas`
--
ALTER TABLE `comidinhas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
