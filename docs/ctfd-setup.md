# CTFd Setup, Configuration, Backup, and Restore

## 1. Service overview

The current `docker-compose.yml` defines these services:

- `gateway` — Nginx entry point published on host port `8000`.
- `ctfd` — CTFd 3.7.0 application; reachable through the gateway.
- `db` — MariaDB 10.11, with data persisted in the named volume `db_data`.
- `cache` — Redis 7.
- `stage1` through `stage6` — individual challenge containers.

CTFd connects to MariaDB using the Compose service name `db` and to Redis using `cache`. These names resolve inside the Docker networks; do not replace them with `localhost` in the CTFd container's connection URL.

## 2. Start the services

Run commands from the folder containing `docker-compose.yml`:

```bash
docker compose up -d --build
docker compose ps
docker compose logs --tail=100 ctfd
```

The gateway should be reachable at:

```text
http://<UBUNTU-VM-IP>:8000
```

Use `http://`, not `https://`, unless TLS has been deliberately configured. From another machine, use the Ubuntu VM IP that is reachable on the selected lab network, not `localhost` and not an unreachable VirtualBox NAT-only address.

## 3. Configure the challenges

Sign in to CTFd with an administrator account. In the admin interface, create or verify the six challenge records using the challenge specifications agreed by the team. For each challenge, verify:

- Name, category, description, and point value.
- Correct flag and case-sensitive validation.
- Hints and any intended point penalties.
- Connection URL or downloadable evidence location.
- Sequential challenge requirements, if the team is using stage locking.

Do not assume that starting the Docker containers automatically creates CTFd challenge records. Confirm each record in the admin interface and test a correct and incorrect submission.

## 4. Create a fresh database backup

Run this on the source machine, from the Compose directory:

```bash
docker compose exec -T db mariadb-dump \
  -uctfd -pctfd --single-transaction ctfd > ctfd-backup.sql
```

Check that the file exists and is non-empty:

```bash
ls -lh ctfd-backup.sql
head -n 5 ctfd-backup.sql
```

The command assumes the database user and password match the current Compose configuration. Avoid placing real secrets in shared shell history or public logs. Protect the backup because it can contain account data, password hashes, challenge records, flags, and submissions. Do not commit it to a public repository.

## 5. Restore onto another machine

1. Copy the project files to the target machine.
2. Place the SQL dump somewhere you can reference from the terminal. It does not have to be beside `docker-compose.yml`.
3. Start the database and cache services:

   ```bash
   docker compose up -d db cache
   docker compose ps
   docker compose logs --tail=80 db
   ```

4. Wait for MariaDB to report that it is ready.
5. **Back up any target database data first.** Importing a dump may drop/recreate tables and overwrite target records. Use a fresh target database for a clean migration.
6. If the backup is one directory above the Compose folder, import it like this:

   ```bash
   docker compose exec -T db mariadb -uctfd -pctfd ctfd < ../ctfd-backup.sql
   ```

   If the file is in the current directory, use `< ctfd-backup.sql` instead.

7. Restart CTFd and start the rest of the project:

   ```bash
   docker compose restart ctfd
   docker compose up -d --build
   docker compose ps
   ```

8. Open the target machine's CTFd URL and verify the original users, challenges, scores, and submissions.

The SQL backup restores database state only. It does not restore challenge source files, images, PCAPs, Dockerfiles, Compose settings, or custom theme files; transfer the project files as well.

## 6. Troubleshooting

### CTFd shows a fresh/empty instance after import

- Confirm the import command completed without errors.
- Check the CTFd `DATABASE_URL` in `docker compose config`.
- Confirm CTFd and the MariaDB service are from the same Compose project.
- Check database contents:

  ```bash
  docker compose exec db mariadb -uctfd -pctfd ctfd -e "SHOW TABLES;"
  docker compose exec db mariadb -uctfd -pctfd ctfd -e "SELECT id,name FROM users;"
  docker compose exec db mariadb -uctfd -pctfd ctfd -e "SELECT id,name FROM challenges;"
  ```

- Restart CTFd after verifying the data is in the database it actually uses.

### Docker permission denied

Try the command with `sudo`, for example:

```bash
sudo docker compose ps
```

For persistent non-sudo access, a trusted administrator may add the account to the `docker` group. Membership in that group grants root-equivalent control of the host.

### Browser reports an SSL error

The current gateway is plain HTTP. Visit `http://<UBUNTU-VM-IP>:8000`, not `https://...:8000`.

### Do not delete the database volume casually

`docker compose down -v` deletes named volumes declared by the project, including `db_data`, and can erase CTFd data. Use it only when intentionally discarding the database.
