# Paper Zero CTF Box — Deployment Guide

This guide describes the deployment defined by the current `docker-compose.yml` and `gateway/nginx.conf` in this repository. It uses the host-port mappings that are actually configured in the project.

## 1. Architecture and port mappings

The Docker Compose project defines these services:

| Service | Purpose | Host access | Container port |
|---|---|---|---:|
| `gateway` | Nginx entry point for CTFd | `http://<UBUNTU-VM-IP>:8000/` | 80 |
| `ctfd` | CTF platform | Through the gateway on host port 8000 | 8000 |
| `db` | MariaDB database for CTFd | Not published to the host | 3306 |
| `cache` | Redis cache for CTFd | Not published to the host | 6379 |
| `stage1` | Steganography challenge | `http://<UBUNTU-VM-IP>:8001/` | 80 |
| `stage2` | Web security challenge | `http://<UBUNTU-VM-IP>:8002/` | 80 |
| `stage3` | Network forensics challenge | `http://<UBUNTU-VM-IP>:8003/` | 80 |
| `stage4` | Linux/SSH challenge | `ssh lecturer@<UBUNTU-VM-IP> -p 8004` | 22 |
| `stage5` | Python command-injection challenge | `http://<UBUNTU-VM-IP>:8005/` | 5000 |
| `stage6` | Reverse-engineering challenge | `http://<UBUNTU-VM-IP>:8006/` | 80 |

In `docker-compose.yml`, the gateway publishes `8000:80`; the challenge containers publish host ports `8001` through `8006`. In particular, Stage 4 uses **host port 8004**, not 2222.

The `ctf_public` network includes the gateway and challenge services. CTFd is connected to both `ctf_public` and `ctf_internal`; MariaDB and Redis are connected to `ctf_internal` and do not publish host ports. These Docker networks provide service connectivity boundaries, but the challenge host ports are published and can be reachable through the Ubuntu VM's network interfaces.

## 2. Prerequisites and network setup

1. Use an Ubuntu VM with Docker Engine and the Docker Compose plugin installed.
2. Use a Kali VM or another permitted player machine on the same isolated lab network as the Ubuntu VM.
3. For a VMware lab, configure both VMs on the same suitable **host-only or private lab network** and verify that the player machine can reach the Ubuntu VM. Do not expose this intentionally vulnerable CTF environment to the public Internet or an institutional production network.
4. Ensure host ports `8000`–`8006` are available. Allow access only from the intended lab network.
5. Extract the project directory so that `docker-compose.yml`, `ctfd-backup.sql`, `gateway/`, and `challenges/` are all in the same `paper-zero/` directory.

Find the IPv4 address of the Ubuntu VM's lab-network interface with:

```sh
ip -br -4 addr
```

Use the address assigned to the Ubuntu VM on the lab network. Do not use a Docker bridge address such as one shown for `docker0` or a Compose network.

## 3. Start or update the services

Run the following commands from the directory containing `docker-compose.yml`:

```sh
docker compose config
docker compose up -d --build
docker compose ps
```

`docker compose config` checks that the Compose configuration can be parsed. `docker compose up -d --build` builds the challenge images and starts the services. The named `db_data` volume stores MariaDB data between normal restarts and updates.

Check logs if a service does not start correctly:

```sh
docker compose logs --tail=50
docker compose logs --tail=50 gateway ctfd db cache
docker compose logs --tail=50 stage1 stage2 stage3 stage4 stage5 stage6
```

The Compose file does not define health checks, so `docker compose ps` and the logs should be used to confirm that services have started successfully.

## 4. Set up CTFd data

There are two options for CTFd data.

### Option A — Start with a new CTFd database

On a new deployment, open the CTFd page and complete its initial setup:

```text
http://<UBUNTU-VM-IP>:8000/
```

Create the CTFd administrator and event configuration, then create the six challenges and their prerequisites. Use the host-port URLs in the port-mapping table above in the challenge descriptions. Stage 4's connection instructions should use port `8004`.

### Option B — Restore the included database backup

The repository contains `ctfd-backup.sql` in the project root. Importing it replaces database tables and data with the contents of that backup, so back up any current CTFd data first.

To save the current database to a file on the Ubuntu host:

```sh
docker compose exec -T db mariadb-dump --user=ctfd --password=ctfd --single-transaction ctfd > ctfd-backup-before-restore.sql
```

Then stop CTFd, import the included backup, and start CTFd again:

```sh
docker compose stop ctfd
docker compose exec -T db mariadb --user=ctfd --password=ctfd ctfd < ctfd-backup.sql
docker compose start ctfd
docker compose logs --tail=50 ctfd
```

After restoring, open CTFd and review each challenge description. The included backup contains the old VM address `192.168.198.130` in its challenge URLs. Replace that address with the current Ubuntu VM IP if it has changed. Use ports `8001`, `8002`, `8003`, `8004` for Stage 4 SSH, `8005`, and `8006` as appropriate.

The current repository does **not** contain `database/portable-links.sql`; no migration command referencing that path is part of this deployment process. The current gateway configuration also does not make the `/stageN/` paths work on port 8000.

## 5. Player access and validation

From Kali or another allowed machine on the lab network, open:

```text
http://<UBUNTU-VM-IP>:8000/
```

Use the following direct challenge links while testing:

```text
Stage 1: http://<UBUNTU-VM-IP>:8001/
Stage 2: http://<UBUNTU-VM-IP>:8002/
Stage 3: http://<UBUNTU-VM-IP>:8003/
Stage 4: ssh lecturer@<UBUNTU-VM-IP> -p 8004
Stage 5: http://<UBUNTU-VM-IP>:8005/
Stage 6: http://<UBUNTU-VM-IP>:8006/
```

Replace `<UBUNTU-VM-IP>` with the actual IP address reachable from the player machine. The Stage 4 credentials should be obtained through the intended challenge path and its evidence.

Useful connectivity checks from the Ubuntu VM or player machine include:

```sh
curl -I http://<UBUNTU-VM-IP>:8000/
curl -I http://<UBUNTU-VM-IP>:8001/
curl -I http://<UBUNTU-VM-IP>:8002/
curl -I http://<UBUNTU-VM-IP>:8003/
curl -I http://<UBUNTU-VM-IP>:8005/
curl -I http://<UBUNTU-VM-IP>:8006/
```

Test Stage 4 separately with SSH on port `8004`. A response from these checks only confirms web-service reachability; each challenge must also be solved and its flag submitted through CTFd to verify the intended path and flag validation.

## 6. Reset and recovery

### Recreate challenge containers

The challenge services do not have named data volumes in the current Compose file. To rebuild and recreate all six challenge containers from the repository files:

```sh
docker compose up -d --build --force-recreate stage1 stage2 stage3 stage4 stage5 stage6
```

To recreate just one stage, specify only that service, for example:

```sh
docker compose up -d --build --force-recreate stage4
```

A container recreation resets that container to its image contents; it does not reset CTFd users, challenge definitions, submissions, or other database records.

### Stop and start the project without deleting CTFd data

```sh
docker compose down
docker compose up -d --build
```

Do **not** use `docker compose down -v` for a normal restart or update. The `-v` option removes the named `db_data` volume and deletes persisted CTFd database data.

If CTFd itself needs to be reset to the supplied backup, follow Option B in Section 4. That import replaces existing database data and should only be performed after making a backup.

## 7. Troubleshooting

- **A port is already in use:** Check whether ports `8000`–`8006` are occupied, stop the conflicting service, or deliberately update the corresponding Compose port mapping and this guide.
- **A challenge URL opens the wrong page or fails:** Confirm the Ubuntu VM IP and port. The current Nginx gateway routes only to CTFd; access each stage using its own host port.
- **CTFd shows an empty/new event after deployment:** Confirm whether the included `ctfd-backup.sql` was restored into the active `db` service. The backup file sitting in the project directory is not imported automatically.
- **A container is not running:** Review `docker compose ps` and `docker compose logs --tail=50 <service>`.
- **Kali cannot reach Ubuntu:** Verify both VMs are on the intended lab network, confirm the Ubuntu lab-interface IP, and check host firewall rules for the required lab-only ports.

## 8. Deployment security notes

This project intentionally contains vulnerable challenge applications. Run it only in an isolated, authorized lab environment. The current Compose file publishes the gateway and challenge ports on the host; do not port-forward them from a router or expose them to an untrusted network.

The Compose file also contains hard-coded development database credentials (`ctfd` / `ctfd` and root password `ctfd_root_pw`). Treat these as local lab defaults, not production credentials. If they are changed, update the matching CTFd database URL and MariaDB environment settings consistently.
