# 0003. Division of Labor With PHP-zggdrasil-mock: This Repo Is the Client, That Repo Was the Mock Server

## Status
accepted

## Date
2026-04-10

## Context
Two sibling repositories were created within days in April 2026 by the same
author: PHP-zggdrasil-mock and this repo (then brand new). This repo's README
links to the mock repo and to HRPAuth. Both repos evolved in lockstep — their
last README updates (org link fixes to CoreMatch) landed on the same day.

## Decision
The split of responsibilities, as evidenced by the two repos' contents:

- PHP-zggdrasil-mock implements a Yggdrasil *server*: full endpoint handlers
  (auth/session/profile/texture/meta) plus MySQL schema and demo credentials,
  explicitly "a mock server for testing purposes", later integrated into HRPAuth
  and archived.
- This repo is the *debug client*: it sends requests at any Yggdrasil server and
  shows the responses (entry 0001). Its Zggdrasil tab covers exactly the endpoint
  set the mock implements, including the same texture paths.
- Shared origin: this repo's first code commit ("mock version") contained the
  mock's server-side router `index.php` — byte-identical to the mock repo's
  `zggdrasilapi/index.php` except one word in a comment — alongside the test
  page. The server skeleton was later deleted, leaving a pure client.

## Consequences
- During the PHP era the pair formed a closed test loop: mock server as system
  under test, this tool as the exerciser; the mock's demo credentials gave the
  debugger ready-made test data.
- Costs and limitations:
  - The pairing is documented only on this side; the mock's README suggests
    Postman/curl for testing and never links back to this debugger.
  - The mock repo is archived ("Do not use it in production"), so the intended
    counterpart no longer evolves; this debugger now effectively targets the
    live Go service and the HRPAuth core, whose surfaces have drifted (entry
    0002).
  - Because both repos froze the same endpoint set, the same drift affects the
    debugger's Zggdrasil tab and any assumption inherited from the mock.

## Evidence
- README.md (field: CoreMatch/PHP-zggdrasil-mock) — links to `CoreMatch/PHP-zggdrasil-mock` and `CoreMatch/HRPAuth`.
- /home/lnb/Desktop/HA/PHP-zggdrasil-mock/README.md (field: It is a mock server for testing purposes) — "A PHP implementation of the next generation Yggdrasil API… a mock server for testing purposes… integrated with HRPAuth… This repository has been archived."
- /home/lnb/Desktop/HA/PHP-zggdrasil-mock/README.md (field: ## API Endpoints) — mock endpoint list, matching this repo's Zggdrasil tab one-to-one.
- script.js (field: isZgg: true) — this repo's Zggdrasil endpoint set for comparison.
- 35893b5bcae77433223dc2659c6f13741eb55da2 "mock version" — first code commit of this repo; its `index.php` differs from PHP-zggdrasil-mock `zggdrasilapi/index.php` (current HEAD) only in one comment word ("Yggdrasil" vs "Zggdrasil"), verified by diff.
- 96a58d4d966a14551e1e1f1c6541163f0909c609 "feat: add API debugger tool with proxy and documentation" — deleted the server-side `index.php` skeleton from this repo.
- PHP-zggdrasil-mock commits: e3f3c649c16241338c928eb410c524636eca6844 "First release" (2026-04-10) and 2475e9594d96e8283e5ccec900df0ff1c479c005 "final version" (2026-04-10) — developed the same day as this repo's "mock version" commit.
- 6442f0920fd70a8ef60405d2b14aec86edd87ed5 "Update README.md" (this repo, 2026-06-19) and da0601ce27fd113306ec2e1f1630aeb244dbef89 "Update README.md" (mock repo, 2026-06-19) — same-day org link updates in both repos.
