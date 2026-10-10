# Paper Zero — CTF Play Box

**Module:** IE3132 — Penetration Testing  
**Scenario:** The Lankan School of Technology (LST) examination breach  

## Overview

Paper Zero is an educational Capture The Flag (CTF) environment built around a fictional examination-data breach. It contains six challenge stages covering steganography, web security, digital forensics/networking, Linux system security, programming/scripting, and reverse engineering.

The project uses CTFd for challenge management and flag validation, with Docker Compose orchestrating CTFd, MariaDB, Redis, a gateway, and the six challenge services.

## Repository layout

- `docker-compose.yml` — service definitions, port mappings, networks, and persistent database volume.
- `gateway/nginx.conf` — HTTP gateway configuration for the CTFd entry point.
- `challenges/stage1-steg/` — Stage 1 image-analysis challenge.
- `challenges/stage2-web/` — Stage 2 web misconfiguration challenge.
- `challenges/stage3-forensics/` — Stage 3 packet-capture challenge.
- `challenges/stage4-linux/` — Stage 4 SSH/Linux challenge.
- `challenges/stage5-injection/` — Stage 5 Flask command-injection challenge.
- `challenges/stage6-reversing/` — Stage 6 binary-analysis challenge.
- `challenges/paper-zero.css` — shared styling used by challenge pages.
- `DEPLOYMENT.md` — deployment and player-facing addresses.
- `docs/` — setup, testing, known issues, and risk documentation.

## Requirements

- Linux host (the team uses Ubuntu in a VM).
- Docker Engine and Docker Compose plugin.
- A browser on a machine that can reach the host VM.
- Player tools as required by each challenge (for example, `strings`, Wireshark, SSH, and a debugger).

## Run the project

From the directory containing `docker-compose.yml`:

```bash
docker compose up -d --build
docker compose ps
```

The gateway publishes CTFd at:

- `http://<UBUNTU-VM-IP>:8000`

Challenge endpoints are documented in `DEPLOYMENT.md`. Use the Ubuntu VM's reachable IP address from other machines; `localhost` refers to the machine running the browser.

## CTFd setup

The Docker Compose file configures CTFd to use MariaDB (`db`) and Redis (`cache`). After the services start, open the URL above and complete any first-run setup shown by CTFd. Configure the six challenge records, flags, point values, hints, and challenge requirements in the CTFd admin interface, then test them.

For database migration and restore instructions, see [`docs/ctfd-setup.md`](docs/ctfd-setup.md).

## Stop and reset

Stop containers without deleting persisted data:

```bash
docker compose down
```

Restart one service:

```bash
docker compose restart stage1
```

Rebuild after changing challenge source or Dockerfiles:

```bash
docker compose up -d --build
```

**Do not run `docker compose down -v` unless you deliberately intend to delete the persisted MariaDB volume.** Back up the database before migration or maintenance.

## Security notice

This project intentionally includes vulnerable services for a controlled educational exercise. Run it only on an isolated, authorized lab network. Do not expose the vulnerable challenge ports to the public internet. Replace demonstration credentials and review the Compose configuration before use beyond a disposable lab.

## Team contribution areas

- **Member 1:** platform, architecture, Docker deployment, network configuration, and CTFd integration.
- **Member 2:** challenge stages 1–3.
- **Member 3:** challenge stages 4–6.
- **Member 4:** integration testing, evidence collection, risk documentation, and README/project documentation.

See the individual commit history and pull requests for the actual work completed by each member.
