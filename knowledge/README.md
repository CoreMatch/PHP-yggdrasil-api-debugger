# Knowledge Base

## What This Is

This knowledge base is the PHP-yggdrasil-api-debugger project's memo to itself,
hosted and maintained within this project. It records internal decisions and
history; it does not define or override outward behavior. Outward behavior is
authoritative in HA-Contract (https://github.com/SamuelCheston/HA-Contract). If an
entry here ever conflicts with HA-Contract, fix the entry, not the contract.

## Entries

| ID | Title | Status | Summary |
|----|-------|--------|---------|
| [0001](0001-thin-debug-client-with-php-proxy.md) | The Debugger Is a Thin HTTP Client With a PHP Relay, Not a Protocol Analyzer | accepted | Static HTML/JS endpoint forms send requests through a single `proxy.php` cURL relay and display raw responses; no JWT/signature parsing; TLS verification disabled for debug convenience. |
| [0002](0002-endpoint-coverage-frozen-at-php-era-surface.md) | Endpoint Coverage Is Frozen at the PHP-Era HRPAuth Surface and Has Drifted From HA-Contract | accepted | 21 forms (10 HRPAuth core + 11 Zggdrasil) match the April/May 2026 PHP backend surface; several targets no longer exist in the current HA-Contract surface. |
| [0003](0003-division-of-labor-with-php-zggdrasil-mock.md) | Division of Labor With PHP-zggdrasil-mock: This Repo Is the Client, That Repo Was the Mock Server | accepted | The sibling repo implemented a mock Yggdrasil server (now archived and integrated into HRPAuth) while this repo consumes Yggdrasil servers as a debug client; they share a code origin. |
| [0004](0004-embedded-analysis-doc-is-a-historical-snapshot.md) | The Embedded Endpoint Analysis Is a Historical Snapshot; the Repo Is Dormant Since 2026-06-19 | accepted | `api-endpoints-analysis.md` documents the old monolithic PHP HRPAuth backend, which has since been superseded by the Go HRPAuth-Yggdrasil-API plus HA-Contract. |

## Maintenance

- One file per entry: `NNNN-<slug>.md`, numbered sequentially starting at 0001;
  add a row to the table above whenever an entry is created or its status changes.
- Every entry uses the fixed sections: Status, Date, Context, Decision,
  Consequences (including costs and limitations), Evidence.
- Evidence must be actually checked before it is written down (`path/to/file:Lstart-Lend`
  and/or full commit hash + subject). Anything that could not be verified is marked
  "not verified" — never fabricated.
- Entries describe internal decisions, orientation, and history only. Outward
  behavior requirements belong in HA-Contract; when in doubt, HA-Contract wins.
