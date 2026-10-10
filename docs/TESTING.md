# Testing and Validation Plan

## Purpose

This document records the checks that must be performed against the Paper Zero implementation. Items below are test procedures, not claims that every test has passed. Update the result and evidence columns only after running each test.

## Test record template

For each test, record the date, tester, commit ID, environment, result (`PASS`, `FAIL`, or `BLOCKED`), and evidence filename or log reference.

## Platform and deployment tests

| ID | Test | Procedure | Expected result |
|---|---|---|---|
| P-01 | Compose configuration | Run `docker compose config` from the project directory. | Configuration renders without errors. |
| P-02 | Service startup | Run `docker compose up -d --build`, then `docker compose ps`. | Gateway, CTFd, DB, cache, and all six stages start or any failure is recorded. |
| P-03 | CTFd access | Browse to `http://<UBUNTU-VM-IP>:8000`. | CTFd page loads over HTTP from an authorized lab client. |
| P-04 | Database connectivity | Inspect DB and CTFd logs; verify CTFd can connect to MariaDB. | No persistent DB connection or DNS errors. |
| P-05 | Remote access | From the player VM, test the host IP and required ports. | Only intended services are reachable on the isolated lab network. |
| P-06 | Restart recovery | Run `docker compose restart stage1`, then revisit Stage 1. | The service returns to its initial container state. |
| P-07 | Persistent CTFd data | Create a test record, run `docker compose down` (without `-v`), then start again. | Persisted database records remain. |
| P-08 | Backup and restore | Dump the source database and restore it to a clean target database. | Expected accounts, challenge records, scores, and submissions are present. |

## CTFd functional tests

| ID | Test | Procedure | Expected result |
|---|---|---|---|
| C-01 | Account login | Sign in with a known test account. | Correct credentials work; incorrect credentials are rejected. |
| C-02 | Challenge records | Review all six challenge entries in CTFd. | Names, categories, descriptions, points, and flags match the approved specifications. |
| C-03 | Correct flag | Submit a valid flag for each challenge. | CTFd accepts the flag and awards the configured score. |
| C-04 | Incorrect flag | Submit an incorrect flag. | CTFd rejects it and does not award points. |
| C-05 | Stage requirements | Attempt later challenges before and after satisfying configured requirements. | Access/locking behaves as configured. |
| C-06 | Hints | Open each hint and verify any point penalty. | Hint text and penalties match the approved configuration. |
| C-07 | Scoreboard | Solve a challenge with a test account. | Scoreboard reflects the expected score. |

## Challenge tests

| ID | Stage | Checks to perform |
|---|---|---|
| S1 | The Suspicious Snapshot | Page loads; intended JPEG is served as a valid image; the intended evidence can be downloaded; hidden text is recoverable using the documented solution path. |
| S2 | The Leaked Portal | Portal loads; intended backup-file clue is available; flag can be retrieved using the intended discovery path. |
| S3 | Shadows on the Wire | Evidence page and PCAP download work; PCAP opens in Wireshark/tshark; intended HTTP stream contains the designed evidence. |
| S4 | The Janitor's Keys | SSH is reachable on the documented host port; intended credentials and SUID configuration work in the challenge container; reset restores the initial state. |
| S5 | The Admin's Dashboard | Flask app loads; intended command-injection exercise works only inside the challenge environment; restart recovers the service. |
| S6 | Paper Zero | Binary download works; binary architecture and instructions are documented; intended analysis path reveals the flag; restart/rebuild restores the file. |

## Security and recovery tests

- Verify MariaDB and Redis are not published directly to the host's network interfaces.
- Verify the host's exposed ports match the approved lab design.
- Verify the lab is not exposed to the public internet.
- Verify no challenge container is running in privileged mode unless specifically justified.
- Verify challenge reset/rebuild procedures restore the intended files and services.
- Check for accidental secrets in Git history, environment files, SQL backups, and logs.
- Test unintended bypasses and record any finding with reproduction steps and a fix.

