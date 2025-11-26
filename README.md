# FacilConsulta API
Api para teste da fácil consulta

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
docker compose build          # primeiro build (ou sempre que alterar Dockerfile)
```

Se receber erro de permissão ao acessar `/var/run/docker.sock`, adicione seu usuário ao grupo `docker`:

```bash
sudo usermod -aG docker $USER
newgrp docker
```

## Comandos Make disponíveis

| Comando        | Descrição                                                         |
|----------------|-------------------------------------------------------------------|
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

## Fluxo completo para subir localmente

```bash
cp .env.example .env        # ajuste APP_KEY com `php artisan key:generate` se quiser offline
docker compose build        # primeira vez ou após alterações em Dockerfile
make up                     # sobe php/nginx/mysql
make migrate                # cria estrutura de banco
make seed                   # (opcional) popular dados quando seeds forem criadas
```

Depois disso, acesse [`http://localhost:8080`](http://localhost:8080) para validar que o Laravel está respondendo.

## Troubleshooting

- **Permissão negada no docker socket**: confirme que seu usuário está no grupo `docker` (veja bloco “Build das imagens”).
- **MySQL reiniciando em loop**: o stack usa volume nomeado `mysql_data`. Caso o container fique preso iniciando (por exemplo, após alterar parâmetros incompatíveis), execute `docker compose down -v` para remover o volume e subir novamente. Em seguida rode `make migrate`.
- **Logs e debugging**: use `make logs` para acompanhar todos os containers ou `docker compose logs <serviço>` para filtrar (ex.: `mysql`, `php`, `nginx`).
