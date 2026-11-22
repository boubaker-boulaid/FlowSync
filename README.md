# FlowSync
FlowSync is a multi-tenant SaaS platform that lets businesses connect their applications and automate repeatable processes using a visual workflow builder. 

## Run locally

Everything runs in Docker. You only need Docker Engine with Compose v2 and Git.

### First time

```bash
cp .env.example .env                 # set DB_PASSWORD and DB_ROOT_PASSWORD
cp backend/.env.example backend/.env # set DB_DATABASE=flowsync, DB_USERNAME=flowsync, DB_PASSWORD (same as above)
docker compose up -d --build
bin/composer install
bin/art key:generate
bin/art migrate
```

### Every day

```bash
docker compose up -d     # start the stack
docker compose ps        # mysql and redis should show "healthy"
docker compose down      # stop it (add -v to also delete the database)
```

### Services

| Service | What it is | Reach it at |
|---|---|---|
| `app` | PHP-FPM 8.5 running Laravel (`backend/`) | `bin/art`, `bin/composer` |
| `mysql` | MySQL 8.0, databases `flowsync` and `flowsync_test` | `mysql:3306` inside the network only |
| `redis` | Redis (cache) | `redis:6379` inside the network only |
| `mailpit` | Catches outgoing email | http://localhost:8025 |

### Command wrappers

| You type | It runs |
|---|---|
| `bin/art <args>` | `docker compose exec app php artisan <args>` |
| `bin/composer <args>` | `docker compose exec app composer <args>` |
| `bin/pest <args>` | `docker compose exec app ./vendor/bin/pest <args>` |
| `bin/npm <args>` | `docker compose exec frontend npm <args>` |
| `bin/sandbox <args>` | `docker compose exec sandbox php artisan <args>` |

### Troubleshooting

- **MySQL init scripts didn't run:** they only run on an empty volume. Run `docker compose down -v`, then `up -d` again.
- **`Connection refused` to MySQL:** use `DB_HOST=mysql`, not `127.0.0.1`.
