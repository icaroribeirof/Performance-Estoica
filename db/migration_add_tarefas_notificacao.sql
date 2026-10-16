-- =====================================================================
-- Migração: Adicionar local e notificações na tabela tarefas
-- Banco de Dados: performance_estoica
-- =====================================================================
-- Instruções:
-- Copie e execute este comando no phpMyAdmin (aba SQL) dentro da base 'performance_estoica'.

ALTER TABLE `tarefas`
ADD COLUMN `local` VARCHAR(255) NULL AFTER `descricao`,
ADD COLUMN `notificacao_ativa` TINYINT(1) DEFAULT 0 AFTER `recorrencia`,
ADD COLUMN `tempo_notificacao` VARCHAR(50) NULL AFTER `notificacao_ativa`;
