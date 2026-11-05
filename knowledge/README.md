# Knowledge Base

## What This Is

This knowledge base is the PHP-yggdrasil-api-debugger project's memo to
itself, hosted and maintained within this project. It records internal
decisions; it does not define or override outward behavior. Outward behavior
is authoritative in HA-Contract (https://github.com/SamuelCheston/HA-Contract).
If an entry here ever conflicts with HA-Contract, fix the entry, not the
contract.

## Entries

| ID | Title | Status | Summary |
|----|-------|--------|---------|
| [0001](0001-thin-debug-client-with-php-proxy.md) | The Debugger Is a Thin HTTP Client With a PHP Relay, Not a Protocol Analyzer | accepted | Static HTML/JS endpoint forms send requests through a single `proxy.php` cURL relay and display raw responses; no JWT/signature parsing; TLS verification disabled for debug convenience. |
| [0002](0002-endpoint-coverage-drifted-from-ha-contract.md) | Endpoint Coverage Has Drifted From HA-Contract and the Live Go Service | accepted | 21 forms (10 HRPAuth core + 11 Zggdrasil); several targets are absent from the current HA-Contract surface, and others differ from the live Go service's routes. |
| [0003](0003-division-of-labor-with-php-zggdrasil-mock.md) | Division of Labor With PHP-zggdrasil-mock: This Repo Is the Client, That Repo Is the Archived Mock Server | accepted | The sibling repo is an archived mock Yggdrasil server (integrated into HRPAuth) while this repo consumes Yggdrasil servers as a debug client; the Zggdrasil tab follows the mock's endpoint surface. |
| [0004](0004-embedded-analysis-doc-describes-old-php-monolith.md) | The Embedded Endpoint Analysis Describes the Old PHP Monolith, Not the Current Contract | accepted | `api-endpoints-analysis.md` documents the old monolithic PHP HRPAuth backend; it is background material only, and HA-Contract wins any conflict. |

## Maintenance

- One file per entry: `NNNN-<slug>.md`, numbered sequentially starting at 0001;
  add a row to the table above whenever an entry is created or its status changes.
- Every entry uses the fixed sections: Status, Context, Decision,
  Consequences (including costs and limitations), Evidence.
- Entries describe the project's current state only: no process history, no
  dated work records, no descriptions of removed code. The single exception is
  a change in design intent that is needed to explain why the current state is
  the way it is.
- Evidence must be actually checked before it is written down. Accepted anchor
  forms: symbol `file::Symbol`, field `file (field: key_name)`, line range
  `file:Lstart-Lend`; citations of other repositories' files are marked
  `[xref]`. Anything that could not be verified is marked "not verified" —
  never fabricated.
- Entries describe internal decisions and orientation only. Outward
  behavior requirements belong in HA-Contract; when in doubt, HA-Contract wins.
