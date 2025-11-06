# Documentação do Banco de Dados

## Visão Geral

Este documento descreve a estrutura completa do banco de dados da aplicação. O banco utiliza PostgreSQL como Sistema Gerenciador de Banco de Dados (SGBD).

**SGBD:** PostgreSQL (pgsql)  
**Versão do Laravel:** 12.36.1  
**Versão do PHP:** 8.3.27

---

## Tabelas

### 1. activity_log

Tabela responsável por registrar atividades e ações realizadas no sistema, permitindo auditoria e rastreamento de eventos.

| Coluna         | Tipo          | Descrição                                                                  |
| -------------- | ------------- | -------------------------------------------------------------------------- |
| `id`           | int8 (BIGINT) | Chave primária, identificador único do registro de atividade               |
| `log_name`     | varchar       | Nome do log/categoria da atividade (ex: "default", "auth", "system")       |
| `description`  | text          | Descrição detalhada da atividade realizada                                 |
| `subject_type` | varchar       | Tipo do modelo/subject relacionado à atividade (polimórfico)               |
| `subject_id`   | int8 (BIGINT) | ID do modelo/subject relacionado à atividade (polimórfico)                 |
| `causer_type`  | varchar       | Tipo do modelo que causou a atividade (polimórfico, ex: "App\Models\User") |
| `causer_id`    | int8 (BIGINT) | ID do modelo que causou a atividade (polimórfico)                          |
| `properties`   | json          | Propriedades adicionais da atividade em formato JSON                       |
| `created_at`   | timestamp     | Data e hora de criação do registro                                         |
| `updated_at`   | timestamp     | Data e hora da última atualização do registro                              |
| `event`        | varchar       | Nome do evento que gerou o log (ex: "created", "updated", "deleted")       |
| `batch_uuid`   | uuid          | UUID único do lote/batch de atividades relacionadas                        |

**Índices:**

-   `activity_log_pkey` (PRIMARY KEY): `id`
-   `activity_log_log_name_index`: `log_name`
-   `subject`: `subject_type`, `subject_id`
-   `causer`: `causer_type`, `causer_id`

---

### 2. cache

Tabela utilizada para armazenar dados em cache no banco de dados.

| Coluna       | Tipo           | Descrição                                      |
| ------------ | -------------- | ---------------------------------------------- |
| `key`        | varchar        | Chave única do item em cache (chave primária)  |
| `value`      | text           | Valor serializado do item em cache             |
| `expiration` | int4 (INTEGER) | Timestamp Unix indicando quando o cache expira |

**Índices:**

-   `cache_pkey` (PRIMARY KEY): `key`

---

### 3. cache_locks

Tabela para gerenciamento de locks de cache, permitindo controle de concorrência.

| Coluna       | Tipo           | Descrição                                                |
| ------------ | -------------- | -------------------------------------------------------- |
| `key`        | varchar        | Chave única do lock (chave primária)                     |
| `owner`      | varchar        | Identificador do processo/proprietário que possui o lock |
| `expiration` | int4 (INTEGER) | Timestamp Unix indicando quando o lock expira            |

**Índices:**

-   `cache_locks_pkey` (PRIMARY KEY): `key`

---

### 4. failed_jobs

Tabela que armazena informações sobre jobs que falharam durante a execução.

| Coluna       | Tipo          | Descrição                                          |
| ------------ | ------------- | -------------------------------------------------- |
| `id`         | int8 (BIGINT) | Chave primária, identificador único do job falhado |
| `uuid`       | varchar       | UUID único do job falhado                          |
| `connection` | text          | Nome da conexão de fila utilizada                  |
| `queue`      | text          | Nome da fila onde o job estava sendo processado    |
| `payload`    | text          | Dados serializados do job que falhou               |
| `exception`  | text          | Mensagem de exceção/erro gerada pelo job           |
| `failed_at`  | timestamp     | Data e hora em que o job falhou                    |

**Índices:**

-   `failed_jobs_pkey` (PRIMARY KEY): `id`
-   `failed_jobs_uuid_unique` (UNIQUE): `uuid`

---

### 5. job_batches

Tabela que gerencia lotes de jobs em processamento.

| Coluna           | Tipo           | Descrição                                                             |
| ---------------- | -------------- | --------------------------------------------------------------------- |
| `id`             | varchar        | Identificador único do lote (chave primária)                          |
| `name`           | varchar        | Nome do lote de jobs                                                  |
| `total_jobs`     | int4 (INTEGER) | Número total de jobs no lote                                          |
| `pending_jobs`   | int4 (INTEGER) | Número de jobs pendentes no lote                                      |
| `failed_jobs`    | int4 (INTEGER) | Número de jobs que falharam no lote                                   |
| `failed_job_ids` | text           | Lista de IDs dos jobs que falharam (serializado)                      |
| `options`        | text           | Opções adicionais do lote (serializado)                               |
| `cancelled_at`   | int4 (INTEGER) | Timestamp Unix de quando o lote foi cancelado (null se não cancelado) |
| `created_at`     | int4 (INTEGER) | Timestamp Unix de criação do lote                                     |
| `finished_at`    | int4 (INTEGER) | Timestamp Unix de conclusão do lote (null se ainda em processamento)  |

**Índices:**

-   `job_batches_pkey` (PRIMARY KEY): `id`

---

### 6. jobs

Tabela que armazena jobs pendentes na fila de processamento.

| Coluna         | Tipo            | Descrição                                                                               |
| -------------- | --------------- | --------------------------------------------------------------------------------------- |
| `id`           | int8 (BIGINT)   | Chave primária, identificador único do job                                              |
| `queue`        | varchar         | Nome da fila onde o job será processado                                                 |
| `payload`      | text            | Dados serializados do job                                                               |
| `attempts`     | int2 (SMALLINT) | Número de tentativas de execução já realizadas                                          |
| `reserved_at`  | int4 (INTEGER)  | Timestamp Unix de quando o job foi reservado para processamento (null se não reservado) |
| `available_at` | int4 (INTEGER)  | Timestamp Unix de quando o job ficará disponível para processamento                     |
| `created_at`   | int4 (INTEGER)  | Timestamp Unix de criação do job                                                        |

**Índices:**

-   `jobs_pkey` (PRIMARY KEY): `id`
-   `jobs_queue_index`: `queue`

---

### 7. migrations

Tabela que controla quais migrations já foram executadas no banco de dados.

| Coluna      | Tipo           | Descrição                                                                 |
| ----------- | -------------- | ------------------------------------------------------------------------- |
| `id`        | int4 (INTEGER) | Chave primária, identificador único da migration                          |
| `migration` | varchar        | Nome do arquivo de migration (ex: "2024_01_01_000001_create_users_table") |
| `batch`     | int4 (INTEGER) | Número do lote em que a migration foi executada                           |

**Índices:**

-   `migrations_pkey` (PRIMARY KEY): `id`

---

### 8. model_has_permissions

Tabela pivot que relaciona modelos (polimórfico) com permissões (Sistema de permissões Spatie).

| Coluna          | Tipo          | Descrição                                                                  |
| --------------- | ------------- | -------------------------------------------------------------------------- |
| `permission_id` | int8 (BIGINT) | ID da permissão (chave estrangeira para `permissions.id`)                  |
| `model_type`    | varchar       | Tipo do modelo que possui a permissão (polimórfico, ex: "App\Models\User") |
| `model_id`      | int8 (BIGINT) | ID do modelo que possui a permissão (polimórfico)                          |

**Índices:**

-   `model_has_permissions_pkey` (PRIMARY KEY): `permission_id`, `model_id`, `model_type`
-   `model_has_permissions_model_id_model_type_index`: `model_id`, `model_type`

**Chaves Estrangeiras:**

-   `model_has_permissions_permission_id_foreign`: `permission_id` → `permissions.id` (ON DELETE CASCADE)

---

### 9. model_has_roles

Tabela pivot que relaciona modelos (polimórfico) com roles/papéis (Sistema de permissões Spatie).

| Coluna       | Tipo          | Descrição                                                             |
| ------------ | ------------- | --------------------------------------------------------------------- |
| `role_id`    | int8 (BIGINT) | ID do role/papel (chave estrangeira para `roles.id`)                  |
| `model_type` | varchar       | Tipo do modelo que possui o role (polimórfico, ex: "App\Models\User") |
| `model_id`   | int8 (BIGINT) | ID do modelo que possui o role (polimórfico)                          |

**Índices:**

-   `model_has_roles_pkey` (PRIMARY KEY): `role_id`, `model_id`, `model_type`
-   `model_has_roles_model_id_model_type_index`: `model_id`, `model_type`

**Chaves Estrangeiras:**

-   `model_has_roles_role_id_foreign`: `role_id` → `roles.id` (ON DELETE CASCADE)

---

### 10. password_reset_tokens

Tabela para armazenar tokens de redefinição de senha.

| Coluna       | Tipo      | Descrição                                                     |
| ------------ | --------- | ------------------------------------------------------------- |
| `email`      | varchar   | Email do usuário que solicitou a redefinição (chave primária) |
| `token`      | varchar   | Token hash usado para redefinir a senha                       |
| `created_at` | timestamp | Data e hora de criação do token                               |

**Índices:**

-   `password_reset_tokens_pkey` (PRIMARY KEY): `email`

---

### 11. permissions

Tabela que armazena as permissões disponíveis no sistema (Sistema de permissões Spatie).

| Coluna       | Tipo          | Descrição                                                  |
| ------------ | ------------- | ---------------------------------------------------------- |
| `id`         | int8 (BIGINT) | Chave primária, identificador único da permissão           |
| `name`       | varchar       | Nome único da permissão (ex: "create users", "edit posts") |
| `guard_name` | varchar       | Nome do guard utilizado (geralmente "web")                 |
| `created_at` | timestamp     | Data e hora de criação do registro                         |
| `updated_at` | timestamp     | Data e hora da última atualização do registro              |

**Índices:**

-   `permissions_pkey` (PRIMARY KEY): `id`
-   `permissions_name_guard_name_unique` (UNIQUE): `name`, `guard_name`

---

### 12. role_has_permissions

Tabela pivot que relaciona roles/papéis com permissões (Sistema de permissões Spatie).

| Coluna          | Tipo          | Descrição                                                 |
| --------------- | ------------- | --------------------------------------------------------- |
| `permission_id` | int8 (BIGINT) | ID da permissão (chave estrangeira para `permissions.id`) |
| `role_id`       | int8 (BIGINT) | ID do role/papel (chave estrangeira para `roles.id`)      |

**Índices:**

-   `role_has_permissions_pkey` (PRIMARY KEY): `permission_id`, `role_id`

**Chaves Estrangeiras:**

-   `role_has_permissions_permission_id_foreign`: `permission_id` → `permissions.id` (ON DELETE CASCADE)
-   `role_has_permissions_role_id_foreign`: `role_id` → `roles.id` (ON DELETE CASCADE)

---

### 13. roles

Tabela que armazena os roles/papéis disponíveis no sistema (Sistema de permissões Spatie).

| Coluna       | Tipo          | Descrição                                          |
| ------------ | ------------- | -------------------------------------------------- |
| `id`         | int8 (BIGINT) | Chave primária, identificador único do role        |
| `name`       | varchar       | Nome único do role (ex: "admin", "editor", "user") |
| `guard_name` | varchar       | Nome do guard utilizado (geralmente "web")         |
| `created_at` | timestamp     | Data e hora de criação do registro                 |
| `updated_at` | timestamp     | Data e hora da última atualização do registro      |

**Índices:**

-   `roles_pkey` (PRIMARY KEY): `id`
-   `roles_name_guard_name_unique` (UNIQUE): `name`, `guard_name`

---

### 14. sessions

Tabela que armazena sessões de usuários (armazenamento de sessão no banco de dados).

| Coluna          | Tipo           | Descrição                                                                      |
| --------------- | -------------- | ------------------------------------------------------------------------------ |
| `id`            | varchar        | Identificador único da sessão (chave primária)                                 |
| `user_id`       | int8 (BIGINT)  | ID do usuário associado à sessão (pode ser null para sessões não autenticadas) |
| `ip_address`    | varchar        | Endereço IP de onde a sessão foi criada                                        |
| `user_agent`    | text           | User agent do navegador/cliente                                                |
| `payload`       | text           | Dados serializados da sessão                                                   |
| `last_activity` | int4 (INTEGER) | Timestamp Unix da última atividade na sessão                                   |

**Índices:**

-   `sessions_pkey` (PRIMARY KEY): `id`
-   `sessions_user_id_index`: `user_id`
-   `sessions_last_activity_index`: `last_activity`

---

### 15. user_sessions_history

Tabela que armazena histórico detalhado de sessões de usuários, incluindo informações sobre dispositivos e localização.

| Coluna       | Tipo           | Descrição                                                       |
| ------------ | -------------- | --------------------------------------------------------------- |
| `id`         | int8 (BIGINT)  | Chave primária, identificador único do registro de histórico    |
| `session_id` | varchar        | ID da sessão relacionada                                        |
| `user_id`    | int8 (BIGINT)  | ID do usuário (chave estrangeira para `users.id`)               |
| `ip_address` | varchar        | Endereço IP de onde a sessão foi iniciada                       |
| `user_agent` | text           | User agent completo do navegador/cliente                        |
| `device`     | varchar        | Tipo de dispositivo (ex: "Desktop", "Mobile", "Tablet")         |
| `browser`    | varchar        | Nome do navegador (ex: "Chrome", "Firefox", "Safari")           |
| `location`   | varchar        | Localização geográfica estimada (ex: "São Paulo, Brasil")       |
| `started_at` | timestamp      | Data e hora de início da sessão                                 |
| `ended_at`   | timestamp      | Data e hora de término da sessão (null se ainda ativa)          |
| `is_active`  | bool (BOOLEAN) | Indica se a sessão está ativa no momento                        |
| `end_reason` | varchar        | Motivo do término da sessão (ex: "logout", "timeout", "forced") |
| `created_at` | timestamp      | Data e hora de criação do registro                              |
| `updated_at` | timestamp      | Data e hora da última atualização do registro                   |

**Índices:**

-   `user_sessions_history_pkey` (PRIMARY KEY): `id`
-   `user_sessions_history_session_id_index`: `session_id`
-   `user_sessions_history_session_id_user_id_index`: `session_id`, `user_id`
-   `user_sessions_history_user_id_started_at_index`: `user_id`, `started_at`

**Chaves Estrangeiras:**

-   `user_sessions_history_user_id_foreign`: `user_id` → `users.id` (ON DELETE CASCADE)

---

### 16. users

Tabela principal que armazena os usuários do sistema.

| Coluna              | Tipo          | Descrição                                                          |
| ------------------- | ------------- | ------------------------------------------------------------------ |
| `id`                | int8 (BIGINT) | Chave primária, identificador único do usuário                     |
| `name`              | varchar       | Nome completo do usuário                                           |
| `username`          | varchar       | Nome de usuário único para login                                   |
| `email`             | varchar       | Email único do usuário                                             |
| `email_verified_at` | timestamp     | Data e hora em que o email foi verificado (null se não verificado) |
| `password`          | varchar       | Hash da senha do usuário (criptografado)                           |
| `remember_token`    | varchar       | Token usado para manter a sessão do usuário ativa ("Lembrar-me")   |
| `created_at`        | timestamp     | Data e hora de criação do registro                                 |
| `updated_at`        | timestamp     | Data e hora da última atualização do registro                      |

**Índices:**

-   `users_pkey` (PRIMARY KEY): `id`
-   `users_email_unique` (UNIQUE): `email`
-   `users_username_unique` (UNIQUE): `username`

---

## Relacionamentos entre Tabelas

### Sistema de Permissões (Spatie)

-   **users** ↔ **model_has_roles** ↔ **roles**: Um usuário pode ter múltiplos roles
-   **roles** ↔ **role_has_permissions** ↔ **permissions**: Um role pode ter múltiplas permissões
-   **users** ↔ **model_has_permissions** ↔ **permissions**: Um usuário pode ter permissões diretas

### Sessões

-   **users** → **user_sessions_history**: Um usuário pode ter múltiplos registros de histórico de sessões (1:N)
-   **sessions**: Armazena sessões ativas (pode estar relacionada a users via `user_id`)

### Logs e Auditoria

-   **activity_log**: Registra atividades relacionadas a qualquer modelo (polimórfico)

---

## Observações Importantes

1. **Sistema de Permissões**: O sistema utiliza o pacote Spatie Laravel Permission, que permite gerenciamento flexível de roles e permissões.

2. **Polimorfismo**: As tabelas `activity_log`, `model_has_permissions` e `model_has_roles` utilizam relacionamentos polimórficos através dos campos `*_type` e `*_id`.

3. **Cascata**: As chaves estrangeiras nas tabelas de relacionamento (`model_has_permissions`, `model_has_roles`, `role_has_permissions`, `user_sessions_history`) estão configuradas com `ON DELETE CASCADE`, garantindo que registros relacionados sejam removidos automaticamente.

4. **Timestamps**: A maioria das tabelas possui campos `created_at` e `updated_at` para controle de criação e atualização.

5. **Armazenamento de Sessão**: O sistema utiliza armazenamento de sessão no banco de dados (`sessions`), além de manter um histórico detalhado em `user_sessions_history`.

---

## Glossário de Tipos de Dados PostgreSQL

-   **int8 (BIGINT)**: Número inteiro de 8 bytes (-9.223.372.036.854.775.808 a 9.223.372.036.854.775.807)
-   **int4 (INTEGER)**: Número inteiro de 4 bytes (-2.147.483.648 a 2.147.483.647)
-   **int2 (SMALLINT)**: Número inteiro de 2 bytes (-32.768 a 32.767)
-   **varchar**: String de comprimento variável (com limite máximo)
-   **text**: String de comprimento variável sem limite de tamanho
-   **timestamp**: Data e hora (sem timezone)
-   **bool (BOOLEAN)**: Valor booleano (true/false)
-   **json**: Dados em formato JSON
-   **uuid**: Identificador único universal (UUID)

---

_Versão do Banco de Dados: PostgreSQL_
