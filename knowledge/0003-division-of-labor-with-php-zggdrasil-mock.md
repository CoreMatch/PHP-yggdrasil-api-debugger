# 0003. Division of Labor With PHP-zggdrasil-mock: This Repo Is the Client, That Repo Is the Archived Mock Server

## Status
accepted

## Context
This repo's README links to `CoreMatch/PHP-zggdrasil-mock` and
`CoreMatch/HRPAuth`. PHP-zggdrasil-mock is a mock Yggdrasil server; this repo
is a debug client for Yggdrasil servers.

## Decision
The split of responsibilities, as evidenced by the two repos' contents:

- PHP-zggdrasil-mock implements a Yggdrasil *server*: full endpoint handlers
  (auth/session/profile/texture/meta) plus MySQL schema and demo credentials,
  explicitly "a mock server for testing purposes"; the repository is archived,
  and its README states it has been integrated into HRPAuth.
- This repo is the *debug client*: it sends requests at any Yggdrasil server and
  shows the responses (entry 0001). Its Zggdrasil tab follows the mock's endpoint
  surface, including the same texture paths; the only omission is the mock's
  batch profile lookup `POST /api/profiles/minecraft`.

## Consequences
- Costs and limitations:
  - The pairing is documented only on this side; the mock's README suggests
    Postman/curl for testing and never links back to this debugger.
  - The mock repo is archived ("do not use it in production environments"), so
    the counterpart no longer evolves; this debugger effectively targets the
    live Go service and the HRPAuth core, whose surfaces drift from the forms as
    described in entry 0002.
  - Because the Zggdrasil tab follows the archived mock's endpoint set, the same
    drift affects it and any assumption inherited from the mock.

## Evidence
- README.md (field: CoreMatch/PHP-zggdrasil-mock) — links to `CoreMatch/PHP-zggdrasil-mock` and `CoreMatch/HRPAuth`.
- /home/lnb/Desktop/HA/PHP-zggdrasil-mock/README.md (field: It is a mock server for testing purposes) [xref] — "A PHP implementation of the next generation Yggdrasil API… a mock server for testing purposes… integrated with HRPAuth… This repository has been archived."
- /home/lnb/Desktop/HA/PHP-zggdrasil-mock/README.md (field: ## API Endpoints) [xref] — mock endpoint list; this repo's Zggdrasil tab follows the same surface.
- /home/lnb/Desktop/HA/PHP-zggdrasil-mock/db.sql [xref] — MySQL schema seeded with demo credentials.
- script.js (field: isZgg: true) — this repo's Zggdrasil endpoint set for comparison.
