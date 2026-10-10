# Paper Zero Risk Register

This register describes project-specific risks and proposed controls. The team should review the ratings against the actual deployment and update them as implementation and testing progress.

## Rating scale

- **Likelihood:** Low / Medium / High.
- **Impact:** Low / Medium / High.
- **Residual risk:** Expected risk after the listed controls; confirm through testing.

| ID | Risk | Likelihood | Impact | Proposed controls | Verification / owner |
|---|---|---|---|---|---|
| R-01 | Vulnerable challenge services are exposed outside the intended lab network. | Medium | High | Use an isolated VMware/VirtualBox lab network; do not forward challenge ports to the public internet; restrict host firewall rules to the player subnet. | Test reachability from intended and unintended networks. Member 1. |
| R-02 | A compromised challenge container affects the host or other services. | Medium | High | Avoid privileged containers; use least privilege, drop unnecessary capabilities, isolate networks, and keep the Docker host patched. | Review Compose security settings and test boundaries. Member 1. |
| R-03 | Database backup or account data is disclosed. | Medium | High | Keep SQL backups private; restrict file permissions; exclude backups from public Git; transfer through trusted channels; delete temporary copies securely when no longer needed. | Inspect repository and transfer locations. Member 1 / Member 4. |
| R-04 | Demo database credentials are reused or exposed. | High in shared/public repos | High | Use non-production credentials only in the isolated lab; use protected environment configuration for deployment; rotate exposed credentials. | Scan tracked files and repository history. Member 1. |
| R-05 | CTFd imports into the wrong database or a stale backup is restored. | Medium | Medium | Confirm `DATABASE_URL`, create timestamped backups, record backup source/date, restore to a clean target, and verify users/challenges after import. | Run documented restore test. Member 1 / Member 4. |
| R-06 | A challenge is unsolvable or can be bypassed unintentionally. | Medium | High | Test each intended solution path from a player machine; test wrong flags and unintended bypasses; track defects and retest fixes. | Use `docs/TESTING.md`. Members 2 and 3; Member 4 coordinates. |
| R-07 | Challenge files or service state are corrupted during play. | Medium | Medium | Keep challenge containers reproducible; use read-only filesystems where compatible; document restart/rebuild steps; keep source-controlled artifacts. | Run reset/recovery tests. Challenge owners. |
| R-08 | Resource exhaustion causes the host or CTFd to become unavailable. | Medium | Medium/High | Monitor resource usage; limit container resources where supported; avoid unnecessary services; stop the lab when unused. | Observe CPU, RAM, and disk during concurrent use. Member 1. |
| R-09 | VirtualBox/VMware network configuration prevents team members from reaching the host. | Medium | Medium | Document the selected adapter/network mode; verify the host IP; check firewall and published ports from each player VM. | Test connectivity from all team machines. Member 1. |
| R-10 | Project documentation overstates test coverage or implementation status. | Medium | Medium | Mark tests as pass only after execution; attach real screenshots/logs; record known issues and design changes. | Review evidence and commit history. Member 4. |
| R-11 | Secrets or generated files are accidentally committed to GitHub. | Medium | High | Use `.gitignore`; review `git status` before commits; keep SQL backups, environment files, logs, and Python cache artifacts out of the repository. | Review staged diff before every push. All members. |
| R-12 | A team member overwrites another member's changes or loses work. | Medium | Medium | Assign file ownership; use feature branches and pull requests; commit small changes; back up important work. | Review branches and pull requests. All members. |

## Deployment-specific notes

The Compose file currently publishes the gateway and challenge ports on host interfaces. That makes host firewall rules and the selected VM network important security controls. MariaDB and Redis should remain without public host-port mappings.

The current database credentials in Compose are demonstration values. They must not be considered secure for production use.


