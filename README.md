<div align="center">

# **INFOLAB**

_Gestão Inteligente, Transformando a UFES Digitalmente_

[![Last Commit](https://img.shields.io/github/last-commit/mauriciosilvadev/infolab?color=0ea5e9&label=last%20commit&logo=git&logoColor=white&style=flat-square)](https://github.com/mauriciosilvadev/infolab)
![PHP](https://img.shields.io/badge/php-57.7%25-777BB4?logo=php&logoColor=white&style=flat-square)
![Languages](https://img.shields.io/badge/languages-5-0ea5e9?style=flat-square)

_Desenvolvido com as ferramentas e tecnologias:_

![JSON](https://img.shields.io/badge/JSON-000000?style=flat-square&logo=json&logoColor=white)
![Markdown](https://img.shields.io/badge/Markdown-000000?style=flat-square&logo=markdown&logoColor=white)
![npm](https://img.shields.io/badge/npm-CB3837?style=flat-square&logo=npm&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-DC382D?style=flat-square&logo=redis&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630?style=flat-square&logo=composer&logoColor=white)

![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square&logo=docker&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-4169E1?style=flat-square&logo=postgresql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat-square&logo=vite&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)
![Axios](https://img.shields.io/badge/Axios-5A29E4?style=flat-square&logo=axios&logoColor=white)

</div>

---

## 📋 Sobre o Projeto

Sistema de gestão desenvolvido com Laravel 12, Filament Admin Panel, PostgreSQL e Redis, totalmente containerizado com Laravel Sail.

## 🔧 Pré-requisitos

-   **Docker** (versão 20.10 ou superior)
-   **Docker Compose** (versão 2.0 ou superior)
-   **Git**

### Instalação do Docker (Ubuntu/Debian)

```bash
# Atualizar pacotes
sudo apt update

# Instalar Docker
sudo apt install -y docker.io docker-compose

# Adicionar usuário ao grupo docker (opcional - evita usar sudo)
sudo usermod -aG docker $USER

# Verificar instalação
docker --version
docker-compose --version
```

## ⚡ Instalação Rápida

Execute estes comandos em sequência para ter o projeto rodando:

```bash
# 1. Clone o repositório
git clone <url-do-repositorio>
cd infolab

# 2. Instale as dependências PHP (se ainda não tiver feito)
composer install

# 3. Inicie os containers
./vendor/bin/sail up -d

# 4. Execute as migrações
./vendor/bin/sail artisan migrate

# 5. Crie um usuário administrador
./vendor/bin/sail artisan make:filament-user
```

**Pronto!** O projeto estará rodando em:

-   **Aplicação**: http://localhost
-   **Admin Panel**: http://localhost/admin
-   **Adminer**: http://localhost:8080

## 🌐 Acessos

| Serviço                   | URL                    | Descrição                   |
| ------------------------- | ---------------------- | --------------------------- |
| **Aplicação Principal**   | http://localhost       | Site principal              |
| **Painel Administrativo** | http://localhost/admin | Filament Admin Panel        |
| **Adminer**               | http://localhost:8080  | Interface do banco de dados |

### Credenciais do Banco (Adminer)

-   **Sistema**: PostgreSQL
-   **Servidor**: pgsql
-   **Usuário**: sail
-   **Senha**: password
-   **Base de dados**: infolab

## 🔧 Comandos Úteis

### Gerenciamento dos Containers

```bash
# Iniciar containers
./vendor/bin/sail up -d

# Parar containers
./vendor/bin/sail down

# Ver status dos containers
./vendor/bin/sail ps

# Ver logs
./vendor/bin/sail logs -f

# Reconstruir containers
./vendor/bin/sail build --no-cache
```

### Laravel

```bash
# Executar comandos Artisan
./vendor/bin/sail artisan <comando>

# Limpar cache
./vendor/bin/sail artisan cache:clear

# Executar migrações
./vendor/bin/sail artisan migrate

# Executar seeders
./vendor/bin/sail artisan db:seed

# Executar testes
./vendor/bin/sail test
```

### Filament

```bash
# Criar usuário admin
./vendor/bin/sail artisan make:filament-user

# Criar resource (CRUD)
./vendor/bin/sail artisan make:filament-resource <NomeResource>
```

### Desenvolvimento

```bash
# Acessar container da aplicação
./vendor/bin/sail shell

# Instalar dependências PHP
./vendor/bin/sail composer install

# Instalar dependências Node.js
./vendor/bin/sail npm install

# Compilar assets para desenvolvimento
./vendor/bin/sail npm run dev

# Watch para desenvolvimento
./vendor/bin/sail npm run watch

# Build para produção
./vendor/bin/sail npm run build
```

### Alias para Facilitar (Opcional)

```bash
# Adicionar ao ~/.bashrc ou ~/.zshrc
alias sail='./vendor/bin/sail'

# Depois de adicionar, execute:
source ~/.bashrc  # ou source ~/.zshrc

# Agora você pode usar apenas:
sail up -d
sail artisan migrate
sail composer install
```

## 📁 Estrutura do Projeto

```
infolab/
├── app/
│   ├── Filament/          # Recursos do Filament Admin Panel
│   │   ├── Resources/     # Resources (CRUDs)
│   │   └── ...
│   ├── Models/            # Modelos Eloquent
│   ├── Listeners/         # Event Listeners
│   └── ...                # Código da aplicação Laravel
├── config/                # Arquivos de configuração
├── database/
│   ├── migrations/        # Migrações do banco de dados
│   └── seeders/           # Seeders
├── public/                # Arquivos públicos (ponto de entrada)
├── resources/
│   ├── views/             # Blade templates
│   ├── css/               # Arquivos CSS
│   └── js/                # Arquivos JavaScript
├── routes/                # Rotas da aplicação
├── storage/               # Arquivos de storage e cache
├── docker-compose.yml     # Configuração Docker Compose (Sail)
├── Dockerfile             # Dockerfile customizado
└── .env                   # Variáveis de ambiente
```

## 🐛 Solução de Problemas

### Container não inicia

```bash
# Verificar logs
./vendor/bin/sail logs

# Reconstruir containers
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

### Erro de permissão

```bash
# Corrigir permissões do Laravel
./vendor/bin/sail shell
chown -R sail:sail /var/www/html/storage
chown -R sail:sail /var/www/html/bootstrap/cache
```

### Banco de dados não conecta

```bash
# Verificar se o container do banco está rodando
./vendor/bin/sail ps

# Verificar logs do banco
./vendor/bin/sail logs pgsql

# Recriar volume do banco (ATENÇÃO: apaga dados)
./vendor/bin/sail down -v
./vendor/bin/sail up -d
```

### Limpar tudo e recomeçar

```bash
# Parar e remover containers, redes e volumes
./vendor/bin/sail down -v

# Reconstruir tudo
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

### Porta já em uso

```bash
# Verificar o que está usando a porta
sudo lsof -i :80
sudo lsof -i :5432

# Parar outros containers Docker
sudo docker stop $(sudo docker ps -aq)
sudo docker rm $(sudo docker ps -aq)
```

## 📝 Tecnologias Utilizadas

### Backend

-   **Laravel 12** - Framework PHP moderno e robusto
-   **PHP 8.2** - Linguagem de programação
-   **Filament 4** - Admin Panel completo e moderno
-   **PostgreSQL 15** - Banco de dados relacional
-   **Redis 7** - Cache e gerenciamento de sessões
-   **Spatie Laravel Activity Log** - Log de atividades do sistema
-   **Spatie Laravel Permission** - Gerenciamento de permissões e roles

### Frontend

-   **Vite 7** - Build tool e bundler moderno
-   **TailwindCSS 4** - Framework CSS utilitário
-   **Axios** - Cliente HTTP para requisições AJAX
-   **JavaScript (ES Modules)** - Linguagem frontend

### DevOps & Ferramentas

-   **Docker** - Containerização
-   **Laravel Sail** - Ambiente de desenvolvimento Docker integrado
-   **Composer** - Gerenciador de dependências PHP
-   **npm** - Gerenciador de dependências JavaScript
-   **Laravel Pint** - Code style fixer

## 📞 Suporte

Se encontrar problemas:

1. Verifique os logs: `./vendor/bin/sail logs -f`
2. Consulte a documentação do Laravel: https://laravel.com/docs
3. Consulte a documentação do Filament: https://filamentphp.com/docs
4. Consulte a documentação do Laravel Sail: https://laravel.com/docs/sail
5. Entre em contato: `mauricio.s.dev@gmail.com`

---

**Até a próxima!**
