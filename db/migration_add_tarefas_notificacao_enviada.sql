-- Adiciona controle de envio de notificações na tabela de tarefas
ALTER TABLE tarefas ADD COLUMN notificacao_enviada BOOLEAN DEFAULT FALSE AFTER tempo_notificacao;
