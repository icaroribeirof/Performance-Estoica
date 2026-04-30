-- Esquema do Banco de Dados: Performance Estóica
-- Versão: 1.2 (Atualizado em 2026-04-30)

CREATE DATABASE IF NOT EXISTS performance_estoica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE performance_estoica;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
);

-- Tabela de Metas
CREATE TABLE IF NOT EXISTS metas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50),
    data_inicio DATE NOT NULL,
    data_termino DATE NOT NULL,
    progresso INT DEFAULT 0,
    status ENUM('em_progresso', 'concluida', 'cancelada') DEFAULT 'em_progresso',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Tarefas
CREATE TABLE IF NOT EXISTS tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    data_vencimento DATETIME,
    data_atual DATETIME,
    prioridade ENUM('baixa', 'media', 'alta') DEFAULT 'media',
    recorrencia ENUM('nenhuma', 'diaria', 'semanal', 'anual', 'dias_semana') DEFAULT 'nenhuma',
    concluida BOOLEAN DEFAULT FALSE,
    data_conclusao DATETIME,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Fichas de Treino
CREATE TABLE IF NOT EXISTS fichas_treino (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    dias_semana VARCHAR(50),
    ativa BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Exercícios
CREATE TABLE IF NOT EXISTS exercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ficha_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    series INT,
    repeticoes INT,
    peso VARCHAR(50),
    descanso INT,
    notas TEXT,
    FOREIGN KEY (ficha_id) REFERENCES fichas_treino(id) ON DELETE CASCADE
);

-- Tabela de Registros de Treino
CREATE TABLE IF NOT EXISTS registros_treino (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    ficha_id INT NOT NULL,
    data_treino DATE NOT NULL,
    duracao_minutos INT,
    intensidade ENUM('leve', 'moderada', 'intensa') DEFAULT 'moderada',
    notas TEXT,
    concluido BOOLEAN DEFAULT TRUE,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (ficha_id) REFERENCES fichas_treino(id) ON DELETE CASCADE
);

-- Tabela de Histórico de Progresso
CREATE TABLE IF NOT EXISTS progresso_treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mes INT,
    ano INT,
    total_treinos INT DEFAULT 0,
    calorias_queimadas INT DEFAULT 0,
    tempo_total_minutos INT DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Atualizações Individuais (Caso precise rodar apenas o que mudou)
-- ALTER TABLE tarefas MODIFY COLUMN recorrencia ENUM('nenhuma', 'diaria', 'semanal', 'anual', 'dias_semana') DEFAULT 'nenhuma';

-- Migration: Alteração para DATETIME para suporte a horários
-- Execute estes comandos se seu banco ainda estiver na versão 1.1
ALTER TABLE tarefas MODIFY COLUMN data_atual DATETIME NULL;
ALTER TABLE tarefas MODIFY COLUMN data_vencimento DATETIME NULL;