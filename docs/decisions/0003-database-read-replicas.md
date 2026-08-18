# ADR-0003: Database Read Replicas

**Status:** Accepted (2026-06-26)

## Context
Production MySQL has read-replica nodes available. Application needs to read from replicas for read-heavy queries while writes go to the primary node.

## Decision
Configure Laravel's read/write database connections in `config/database.php`:
- `mysql.read` — array of read-replica hosts (`DB_READ_HOST_1`, `DB_READ_HOST_2`)
- `mysql.write` — single write host (`DB_WRITE_HOST`)
- All `SELECT` queries automatically route to read replicas
- All `INSERT/UPDATE/DELETE` queries route to the write host

## Consequences
- No code changes needed — Laravel handles routing transparently
- Read replicas reduce load on primary
- Slight replication lag possible for read-after-write patterns
