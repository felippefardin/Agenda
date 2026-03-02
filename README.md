# Bug Criativo - Agenda Inteligente

Uma aplicação de agenda intuitiva e moderna desenvolvida com **Laravel 11**, focada no controle de compromissos com níveis de prioridade e status de execução. O projeto utiliza uma interface dinâmica baseada em calendários para facilitar a organização diária.

## Funcionalidades

- **Calendário Interativo**: Visualização mensal completa utilizando a biblioteca FullCalendar.
- **Gestão de Tarefas**: Criação, edição e exclusão de compromissos diretamente através de cliques no calendário.
- **Interface em Português**: Botões de navegação traduzidos (Ex: "Hoje" e "Mês") para melhor usabilidade.
- **Comportamento Inteligente**: Ao clicar no botão "Mês", o calendário retorna automaticamente para o mês e dia atual.
- **Sistema de Prioridades**: Classificação visual de tarefas:
    - 🔴 **Urgente**: Destaque vermelho.
    - 🟡 **Importante**: Destaque amarelo.
    - 🟢 **Opcional**: Destaque verde.
- **Status de Progresso**: Controle do ciclo de vida da tarefa (🔒 Fechado, ⏳ Em andamento, ✅ Finalizado).
- **Notificações em Tempo Real**: Alertas visuais na tela listando os compromissos agendados para o dia atual.

## Tecnologias Utilizadas

- **Backend**: PHP 8.2+ e Framework Laravel 11.
- **Frontend**: Blade Templates, Tailwind CSS (estilização) e JavaScript puro.
- **APIs**: FullCalendar 6 para a renderização do cronograma.
- **Banco de Dados**: MySQL (configuração padrão via migrations).

## Como Instalar e Rodar o Projeto

Se você deseja clonar este projeto em outro computador, siga os passos abaixo:

### 1. Requisitos Prévios
Certifique-se de ter instalado:
- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- Um servidor de banco de dados (MySQL/MariaDB)

### 2. Instalação das Dependências
Abra o terminal na pasta do projeto e execute:
```bash
composer install
npm install
3. Configuração de Ambiente
Crie o arquivo de configuração local:

Bash
cp .env.example .env
Edite o arquivo .env com as suas credenciais de banco de dados:

Snippet de código
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
4. Inicialização do Banco e Chave
Bash
php artisan key:generate
php artisan migrate
5. Execução
Inicie o compilador de ativos e o servidor local:

Bash
# Em um terminal
npm run dev

# Em outro terminal
php artisan serve
Acesse: http://localhost:8000
