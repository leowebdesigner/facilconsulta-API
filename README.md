# FacilConsulta API

API profissional para agendamento médico construída em Laravel 11+, seguindo Clean Architecture, Sanctum (SPA mode) e containers Docker.

## Pré-requisitos

- Docker e Docker Compose
- Make (GNU Make 4.x)
- Acesso ao daemon Docker (usuário precisa estar no grupo `docker` ou usar `sudo`)

## Primeiros passos

```bash
git clone git@github.com:seu-usuario/facilconsulta.git
cd facilconsulta
cp .env.example .env          # ajuste variáveis conforme necessidade
```

### Ajuste de ambiente

* `APP_PORT=8080` controla a porta exposta pelo Nginx.
* `DB_*` já apontam para o serviço interno `mysql`; altere senha/empresa se necessário.
* Se optar por SQLite para testes locais sem Docker, atualize apenas o `.env` (não versionado).

### Build das imagens

```bash
make build                    # primeiro build (ou sempre que alterar Dockerfile)
```

## Comandos Make disponíveis

| Comando        | Descrição                                                         |
|----------------|-------------------------------------------------------------------|
| `make build`   | Executa `docker compose build`.                                    |
| `make up`      | Sobe PHP-FPM, Nginx e MySQL em background.                        |
| `make down`    | Derruba os containers (mantém volumes).                           |
| `make restart` | Reinicia o stack.                                                 |
| `make logs`    | Acompanha logs combinados (`docker compose logs -f`).             |
| `make bash`    | Abre bash no container PHP para rodar artesãos/pacotes manualmente. |
| `make migrate` | Executa `php artisan migrate --force` dentro do container PHP.    |
| `make seed`    | Executa `php artisan db:seed --force`.                            |
| `make test`    | Roda `php artisan test`.                                          |
| `make fix`     | Executa o Pint (PSR-12) para padronizar o código.                 |
| `make optimize`| Roda `php artisan optimize`.                                      |
| `make swagger` | Gera documentação Swagger (L5 Swagger).                           |

## Fluxo completo para subir localmente

```bash
cp .env.example .env        # ajuste APP_KEY com `php artisan key:generate` se quiser offline
make build                  # primeira vez ou após alterações em Dockerfile
make up                     # sobe php/nginx/mysql
make migrate                # cria estrutura de banco
make seed                   # popular dados fake (opcional)
make key                    # gerar a chave do laravel APP KEY
```

Depois disso, acesse [`http://localhost:8080`](http://localhost:8080) para validar que o Laravel está respondendo.

## Sanctum + CORS (SPA Mode)

- `FRONTEND_URL` define a origem SPA liberada via CORS (default `http://localhost:5173`).
- `CORS_ALLOWED_ORIGINS` e `SANCTUM_STATEFUL_DOMAINS` aceitam lista separada por vírgulas; ajuste se publicar em outro domínio (inclua porta do SPA).
- Middleware API (`statefulApi()`) já injeta `EnsureFrontendRequestsAreStateful` e `HandleCors`.
- Endpoints úteis:
  - `GET /api/v1/health` – público.
  - `POST /api/v1/auth/register` / `POST /api/v1/auth/login` – público.
  - `POST /api/v1/auth/logout` / `GET /api/v1/auth/me` – requer token Sanctum.
  - `POST /api/v1/appointments` / `PATCH /api/v1/appointments/{id}/status` – protegidos.

## Troubleshooting

- **Permissão negada no docker socket**: confirme que seu usuário está no grupo `docker` (veja bloco “Build das imagens”).
- **Missing APP_KEY**: execute `docker compose exec php php artisan key:generate` para definir a chave e evitar `MissingAppKeyException`.

## Rotas principais (V1)

| Rota | Método | Descrição |
|------|--------|-----------|
| `/api/v1/health` | GET | Health check público |
| `/api/v1/auth/register` | POST | Cadastro de paciente |
| `/api/v1/auth/login` | POST | Login e geração de token |
| `/api/v1/auth/logout` | POST | Logout (precisa Sanctum) |
| `/api/v1/patient/profile` | GET | Perfil do paciente autenticado |
| `/api/v1/patient/appointments/upcoming` | GET | Próximos agendamentos do paciente |
| `/api/v1/doctors` | GET | Lista de médicos (filtros `specialty`, `active`) |
| `/api/v1/doctors/available` | GET | Médicos disponíveis por data/especialidade |
| `/api/v1/appointments` | POST | Cria um agendamento |
| `/api/v1/appointments/doctor/{doctorId}` | GET | Lista agendamentos por médico |
| `/api/v1/appointments/patient/{patientId}` | GET | Lista agendamentos por paciente |
| `/api/v1/appointments/{appointment}/status` | PATCH | Atualiza status (confirmar/cancelar/completar) |

## Dados fake

Execute `make seed` para popular:
- 10 pacientes ativos.
- 5 médicos com 3 horários cada.
- Agendamentos de exemplo (futuros) conectando pacientes/médicos/horários.

# Swagger / API Docs

- Gere a documentação após alterações: `make swagger`.
- Acesse `http://localhost:8080/api/documentation` para visualizar a UI.
- As anotações ficam nos controllers/resources e em `SwaggerController`.

## Coleção Postman

- Arquivo `postman/facilconsulta.postman_collection.json`.
- Importar no Postman, ajustar variáveis `base_url`, `token` e `today`.
- Requests prontos para Auth, Patient, Doctors e Appointments.

## phpMyAdmin

- Incluí um container phpMyAdmin para inspecionar o MySQL.
- Acesse `http://localhost:${PHPMYADMIN_PORT:-8081}` (default `http://localhost:8081`).
- Host: `mysql`
- Usuário/senha: use as mesmas variáveis `DB_USERNAME` / `DB_PASSWORD` (padrão `facilconsulta` / `secret`).

## Suite de testes

```
make test
```

Rodará FormRequests (unit) e fluxos principais (feature) usando SQLite em memória. Certifique-se de que o container PHP esteja ativo (`make up`).***
