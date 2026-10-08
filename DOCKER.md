# SmilePro in Docker (CasaOS / Portainer)

The stack has two containers:

- **smilepro-app**: PHP 8.3 + Apache with the Laravel app. The Vite assets are built inside the image.
- **smilepro-db**: MariaDB 11.

On the first start the app runs the migrations and seeds the demo data. On later starts it only runs new migrations, so the data is kept. Data lives in the `smilepro_db` and `smilepro_storage` volumes.

## Option A: Portainer, from the Git repository (recommended)

1. Portainer → **Stacks** → **Add stack** → **Repository**.
2. Repository URL: `https://github.com/NimrodLoozar/SmilePro`, reference: `refs/heads/<branch>` (the branch with these Docker files), compose path: `docker-compose.yml`.
3. Under **Environment variables** add:

   | Name | Example |
   |---|---|
   | `APP_PORT` | `8080` (CasaOS already uses port 80) |
   | `APP_URL` | `http://<server-ip>:8080` |
   | `DB_PASSWORD` | a strong password |
   | `DB_ROOT_PASSWORD` | another strong password |

4. **Deploy the stack**. The first build takes a few minutes. Then open `http://<server-ip>:8080`.

## Option B: on the server via SSH

```bash
git clone https://github.com/NimrodLoozar/SmilePro.git && cd SmilePro
git checkout <branch>
DB_PASSWORD=change-me DB_ROOT_PASSWORD=change-me-too docker compose up -d --build
```

The stack then also shows up in Portainer and in CasaOS (as a "legacy" app).

## Demo accounts (from the seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@gmail.com | Admin1234 |
| Dentist | dentist@gmail.com | Dentist1234 |
| Patient | patient@gmail.com | Patient1234 |
| Test | test@gmail.com | Test1234 |

## Good to know

- `APP_KEY` is generated automatically and stored in the storage volume. You can also set it yourself.
- Set `SEED_ON_FIRST_RUN` to `"false"` if you don't want demo data.
- Update: in Portainer **Pull and redeploy** (enable "Re-pull image and redeploy"), or `git pull && docker compose up -d --build`.
- Start over with an empty database: `docker compose down -v` (**deletes all data**).
- Logs: `docker logs smilepro-app`.
