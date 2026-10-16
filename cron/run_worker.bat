@echo off
title Worker Notificacoes WhatsApp - Performance Estoica
echo ========================================================
echo  Worker de Notificacoes WhatsApp - Performance Estoica
echo  Verificando tarefas a cada 60 segundos...
echo  (Mantenha esta janela aberta para receber lembretes)
echo ========================================================

:loop
"D:\wamp64\bin\php\php8.2.29\php.exe" "%~dp0notify_whatsapp.php"
timeout /t 60 /nobreak >nul
goto loop
