# 🚀 InfoLab - Sistema de Gestão UFES

Sistema de gestão desenvolvido com Laravel 11, Filament Admin Panel, PostgreSQL e Redis, totalmente containerizado com Laravel Sail.

## 📋 Pré-requisitos

- **Docker** (versão 20.10 ou superior)
- **Docker Compose** (versão 2.0 ou superior)
- **Git**

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

# 2. Inicie os containers
./vendor/bin/sail up -d

# 3. Execute as migrações
./vendor/bin/sail artisan migrate

# 4. Crie um usuário administrador
./vendor/bin/sail artisan make:filament-user
```

**Pronto!** O projeto estará rodando em:
- **Aplicação**: http://localhost
- **Admin Panel**: http://localhost/admin
- **Adminer**: http://localhost:8080

## 🌐 Acessos

| Serviço | URL | Descrição |
|---------|-----|-----------|
| **Aplicação Principal** | http://localhost | Site principal |
| **Painel Administrativo** | http://localhost/admin | Filament Admin Panel |
| **Adminer** | http://localhost:8080 | Interface do banco de dados |

### Credenciais do Banco (Adminer)

- **Sistema**: PostgreSQL
- **Servidor**: pgsql
- **Usuário**: sail
- **Senha**: password
- **Base de dados**: infolab

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
├── app/                    # Código da aplicação Laravel
├── config/                 # Arquivos de configuração
├── database/              # Migrações e seeders
├── public/                # Arquivos públicos
├── resources/             # Views, CSS, JS
├── routes/                # Rotas da aplicação
├── storage/               # Arquivos de storage
├── vendor/laravel/sail/   # Laravel Sail
├── docker-compose.yml     # Configuração Docker Compose (Sail)
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

- **Laravel 11** - Framework PHP
- **Filament 4** - Admin Panel
- **PostgreSQL 15** - Banco de dados
- **Redis 7** - Cache e sessões
- **Laravel Sail** - Ambiente de desenvolvimento Docker

## 📞 Suporte

Se encontrar problemas:

1. Verifique os logs: `./vendor/bin/sail logs -f`
2. Consulte a documentação do Laravel: https://laravel.com/docs
3. Consulte a documentação do Filament: https://filamentphp.com/docs
4. Consulte a documentação do Laravel Sail: https://laravel.com/docs/sail
5. Entre em contato: `mauricio.s.dev@gmail.com`

---

**Até a próxima!**
