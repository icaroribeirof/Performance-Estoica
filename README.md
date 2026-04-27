# 💪 Performance Estóica

Um sistema completo de rastreamento de metas, tarefas e treinos de academia com visual profissional e intuitivo.

## 🚀 Características

- ✅ **Autenticação Segura**: Sistema de login e cadastro com senhas criptografadas
- 🎯 **Gerenciamento de Metas**: Crie e acompanhe suas metas com prazos e progresso
- ✓ **Tarefas do Dia a Dia**: Organize suas tarefas diárias com prioridades
- 🏋️ **Treinos na Academia**: Registre fichas de treino, exercícios e histórico de treinos
- 📊 **Dashboard Intuitivo**: Visualize suas estatísticas e progresso em tempo real
- 🎨 **Design Responsivo**: Interface bonita e profissional que funciona em todos os dispositivos

## 📋 Requisitos

- **PHP** 7.4 ou superior
- **MySQL** 5.7 ou superior
- **Servidor Web** (Apache, Nginx, etc.)
- **Navegador Moderno** (Chrome, Firefox, Safari, Edge)

## 🔧 Instalação

### 1. Copiar Projeto

Copie a pasta `PERFORMANCE ESTÓICA` para a raiz do seu servidor web (htdocs, www, etc).

### 2. Configurar Banco de Dados

Edite o arquivo `config.php` com suas credenciais MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'performance_estoica');
```

### 3. Criar Banco de Dados

Abra seu navegador e acesse:
```
http://localhost/PERFORMANCE%20EST%C3%93ICA/criar_banco_dados.php
```

Você verá a mensagem de sucesso após a criação.

### 4. Acessar o Sistema

Acesse o sistema em:
```
http://localhost/PERFORMANCE%20EST%C3%93ICA/login.php
```

## 📁 Estrutura do Projeto

```
PERFORMANCE ESTÓICA/
├── index.php                    # Página inicial (redirecionamento)
├── login.php                    # Página de login
├── cadastro.php                 # Página de cadastro
├── dashboard.php                # Dashboard principal
├── metas.php                    # Página de metas
├── tarefas.php                  # Página de tarefas
├── treinos.php                  # Página de treinos
├── config.php                   # Configuração do banco de dados
├── criar_banco_dados.php        # Script de criação do banco
│
├── api/
│   ├── auth.php                 # API de autenticação
│   ├── metas.php                # API de metas
│   ├── tarefas.php              # API de tarefas
│   └── treinos.php              # API de treinos
│
├── css/
│   ├── geral.css                # Estilos gerais
│   ├── login.css                # Estilos de login/cadastro
│   ├── cadastro.css             # Estilos específicos de cadastro
│   ├── dashboard.css            # Estilos do dashboard
│   ├── metas.css                # Estilos de metas
│   ├── tarefas.css              # Estilos de tarefas
│   └── treinos.css              # Estilos de treinos
│
└── js/
    ├── login.js                 # Script de login
    ├── cadastro.js              # Script de cadastro
    ├── dashboard.js             # Script do dashboard
    ├── metas.js                 # Script de metas
    ├── tarefas.js               # Script de tarefas
    └── treinos.js               # Script de treinos
```

## 🎯 Como Usar

### 1. Criar Conta

- Acesse a página de cadastro
- Preencha seus dados
- Clique em "Cadastrar"
- Você será redirecionado para o login

### 2. Fazer Login

- Insira seu email e senha
- Clique em "Entrar"
- Você será direcionado ao dashboard

### 3. Gerenciar Metas

- Clique em "Metas" no menu
- Clique em "+ Nova Meta"
- Preencha os dados e crie sua meta
- Acompanhe o progresso em tempo real

### 4. Organizar Tarefas

- Clique em "Tarefas" no menu
- Clique em "+ Nova Tarefa"
- Defina a prioridade e data de vencimento
- Marque como concluída quando terminar

### 5. Registrar Treinos

- Clique em "Treinos" no menu
- Crie sua ficha de treino com exercícios
- Registre seus treinos diários
- Acompanhe estatísticas de progresso

## 🛠️ APIs Disponíveis

### Autenticação (`api/auth.php`)

- `?acao=cadastro` - Registrar novo usuário
- `?acao=login` - Fazer login
- `?acao=logout` - Fazer logout

### Metas (`api/metas.php`)

- `?acao=listar` - Listar todas as metas
- `?acao=adicionar` - Criar nova meta
- `?acao=atualizar` - Atualizar progresso
- `?acao=deletar` - Deletar meta

### Tarefas (`api/tarefas.php`)

- `?acao=listar&filtro=todas|hoje|alta` - Listar tarefas
- `?acao=adicionar` - Criar nova tarefa
- `?acao=concluir` - Marcar como concluída
- `?acao=deletar` - Deletar tarefa

### Treinos (`api/treinos.php`)

- `?acao=listar_fichas` - Listar fichas de treino
- `?acao=criar_ficha` - Criar nova ficha
- `?acao=listar_exercicios&ficha_id=X` - Listar exercícios
- `?acao=adicionar_exercicio` - Adicionar exercício
- `?acao=registrar_treino` - Registrar treino
- `?acao=listar_registros&mes=X&ano=X` - Listar registros
- `?acao=estatisticas&mes=X&ano=X` - Obter estatísticas

## 🔐 Segurança

- Senhas são armazenadas com hash bcrypt
- Sessões PHP para autenticação
- Sanitização de inputs
- Prepared statements para prevenir SQL injection

## 🎨 Personalização

Você pode personalizar as cores editando as variáveis CSS no arquivo `css/geral.css`:

```css
:root {
    --primary-color: #6366f1;
    --secondary-color: #ec4899;
    --success-color: #10b981;
    --danger-color: #ef4444;
    /* ... etc */
}
```

## 🤝 Contribuindo

Este é um projeto de uso pessoal. Sinta-se livre para modificar e melhorar!

## 📝 Licença

Livre para uso pessoal e comercial.

## 📞 Suporte

Se encontrar algum problema, verifique:

1. Se o banco de dados foi criado corretamente
2. As credenciais MySQL estão corretas em `config.php`
3. O servidor PHP está rodando
4. A pasta tem permissões de leitura/escrita

---

**Made with 💜 for Excellence**

Transforme sua vida com Performance Estóica! 🚀
