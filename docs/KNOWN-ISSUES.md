# Known Issues and Verification Items

This file tracks issues and checks identified during project review. Items should be updated as the team reproduces, fixes, and retests them. Do not describe an item as fixed until the change has been tested.

## KI-01 — Stage 1 has two JPEG-named files

**Observation:** The project contains both `suspicious_photo.jpeg` and `suspicious_photo.jpg`, plus `base.jpeg` and `secret.txt`.

**Risk:** The Dockerfile may serve a different file than the one intended by the challenge, which could make the solution trivial or inconsistent.

**Action:** Inspect the Stage 1 Dockerfile and the actual container web root. Confirm the served image is a valid JPEG and that the flag is appended to the intended binary image. Remove misleading duplicates only after confirming they are not required.

**Verification:** Use `file` on the served artifact and solve the challenge from a separate player machine.

## KI-02 — CTFd challenge records may not be created automatically

**Observation:** Docker Compose starts the challenge services, but container startup alone does not guarantee that six challenge records, flags, hints, point values, and requirements exist in CTFd.

**Action:** Confirm each challenge in the CTFd admin interface. Configure and test the approved values and any sequential requirements.

**Verification:** Submit correct and incorrect flags and test stage locking.

## KI-03 — Host IP and VirtualBox networking vary by machine

**Observation:** URLs that work on the Ubuntu host may not work from a different VM or physical host. VirtualBox NAT addresses are not always reachable directly from peer machines.

**Action:** Use a lab network topology that permits the player VM to reach the Ubuntu VM. Use the reachable Ubuntu IP in challenge descriptions. Check firewall rules and published Docker ports.

**Verification:** Test each required endpoint from the actual player machine.

## KI-04 — HTTP versus HTTPS

**Observation:** The current Compose gateway maps host port `8000` to Nginx port `80`; TLS is not configured in the shown gateway setup.

**Symptom:** Firefox may show `SSL_ERROR_RX_RECORD_TOO_LONG` if it tries to use HTTPS against this HTTP endpoint.

**Action:** Use `http://<UBUNTU-VM-IP>:8000` for the current local lab, unless TLS is separately configured.

**Verification:** Load the page from a client machine and confirm the address uses HTTP.

## KI-05 — Database backup can be stale or imported into the wrong database

**Observation:** A SQL backup captures database contents at the time it was created. Importing it into one MariaDB instance will not affect a different database volume or a different Compose project.

**Action:** Create a fresh dump on the source machine. On the target, confirm the active `DATABASE_URL`, service names, and database contents after import. Back up any existing target data first.

**Verification:** Compare expected users and challenge records, then log in and verify scores/submissions.

## KI-06 — Demo credentials are present in Compose configuration

**Observation:** The current Compose file contains example database credentials in environment values.

**Risk:** These values are unsuitable for a public or shared production deployment and should not be treated as secrets.

**Action:** Keep the repository private while developing; do not commit real production secrets. For any deployment beyond a disposable isolated lab, move credentials to an appropriately protected environment file or secret mechanism, update configuration consistently, and rotate any credential that has been exposed.

**Verification:** Scan tracked files and Git history for credentials before making the repository public.

## KI-07 — Temporary and backup files are included in the project archive

**Observation:** The archive contains a Compose backup file and a Python `__pycache__` artifact. The archive also contains a database SQL dump at a level above the application project.

**Action:** Decide which source files are required. Remove generated cache files and obsolete backups from version control. Keep database dumps private and outside the public repository.

**Verification:** Review `git status` and the final repository file list before pushing.

## KI-08 — Reset behavior needs per-stage validation

**Observation:** Restarting a container restores only the container's current filesystem/process state; it does not automatically undo modifications to bind-mounted host files or persisted volumes.

**Action:** Document whether each stage needs restart or image rebuild, and avoid writable host mounts for challenge artifacts unless needed.

**Verification:** Deliberately modify a disposable challenge instance, run the documented reset procedure, and confirm the intended initial state returns.

## Issue tracking format

For each new issue, record: ID, date found, reporter, affected service, reproduction steps, impact, fix commit, retest result, and evidence path.
