-- =====================================================================
-- Migração: Adicionar coluna de telefone na tabela usuarios
-- Banco de Dados: performance_estoica
-- =====================================================================
-- Instruções:
-- Copie e execute este comando no phpMyAdmin (aba SQL) dentro da base 'performance_estoica'.

ALTER TABLE `usuarios`
ADD COLUMN `telefone` VARCHAR(20) NULL AFTER `email`;