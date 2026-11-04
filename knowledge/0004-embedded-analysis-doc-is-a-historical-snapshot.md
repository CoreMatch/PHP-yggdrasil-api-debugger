# 0004. The Embedded Endpoint Analysis Is a Historical Snapshot; the Repo Is Dormant Since 2026-06-19

## Status
accepted

## Date
2026-05-05

## Context
The repo ships `api-endpoints-analysis.md`, a ~1090-line Chinese-language
analysis of the HRPAuth backend API. It describes a monolithic PHP backend: a
`public/index.php` front controller routing to `controllers/AuthController`,
`controllers/TOTPController`, `controllers/KeyGenController`, and a bundled
`modules/zggdrasilapi` module. The ecosystem has since been re-architected: the
Yggdrasil surface lives in the Go service HRPAuth-Yggdrasil-API, outward
documentation moved to HA-Contract as the single source of truth, and the
PHP-era endpoint docs were moved to HA-Contract's archive.

## Decision
Keep `api-endpoints-analysis.md` in this repo as background material for the
debugger's forms. It was added together with the unified debugger generation and
expanded with texture file-system/database details in the same commit that
shipped the current UI.

## Consequences
- The document is a snapshot of an architecture that no longer exists in that
  shape: `POST /login` with a remember token, `/generate-key` writing
  `keys/*.pem` and self-disabling, and in-process `modules/zggdrasilapi` routing
  are all PHP-era descriptions. It is historical context, not a contract.
- Where it disagrees with HA-Contract, HA-Contract wins (this knowledge base's
  stated order of authority).
- Costs and limitations:
  - The doc can mislead newcomers into thinking the described routes/DB layouts
    still exist; its own language (Chinese) also differs from HA-Contract's
    English-only documentation convention.
  - The repo itself is dormant: no functional change since the 2026-05-06
    rewrite; the last commit (2026-06-19) only fixed README links. Meanwhile the
    live Go service is under active development (last checked commit
    2026-11-04). Any future maintenance of this debugger should re-baseline the
    forms and this doc against HA-Contract first.
  - Relationship to the Go service: no code-level integration exists (this tool
    only speaks HTTP to whatever base URL is entered); the connection is purely
    that the Go service now implements the surface this tool was built to probe.

## Evidence
- api-endpoints-analysis.md:1 — title "HRPAuth 后端 API 端点分析报告".
- api-endpoints-analysis.md:74 — `/login` routed to `controllers/AuthController@login`.
- api-endpoints-analysis.md:581-585 — `/generate-key` notes (RSA key pair for Yggdrasil signing, self-disabling endpoint, `signaturePublickey`).
- api-endpoints-analysis.md:644 — `/authserver/authenticate` routed to `modules/zggdrasilapi/src/auth/authenticate.php`.
- api-endpoints-analysis.md:1062-1090 — PHP front-controller routing table for the monolith.
- 7094ade6e2ebadd5ea6071d53e51f0fb92987ca2 "feat: add unified api debugger and portal interface" (2026-05-05) — added api-endpoints-analysis.md.
- 96a58d4d966a14551e1e1f1c6541163f0909c609 "feat: add API debugger tool with proxy and documentation" (2026-05-06) — expanded the doc with texture file/DB details; last functional commit.
- 6442f0920fd70a8ef60405d2b14aec86edd87ed5 "Update README.md" (2026-06-19) — last commit of this repo to date (README link fixes only).
- HA-Contract README.md:15-17 — "Documentation Center (Single Source of Truth)".
- HA-Contract README.md:39-41 — old/drifted documents archived and no longer used as development references.
- HA-Contract README.md:43-45 — all HA-Contract documentation must be written in English.
- HA-Contract archive/docs/HRPAuth/endpoints/ — archived PHP-era endpoint docs (auth.md, keygen.md, totp.md, texture.md, yggdrasil.md, etc.).
- HRPAuth-Yggdrasil-API README.md:5 — current Go service description; README.md:68-73 — its API contract is maintained in HA-Contract.
- HRPAuth-Yggdrasil-API commit 124b2fa1ab42e88e90b227aea9e18465378df0af (2026-11-04) — evidence the Go service is actively developed at the time of writing.
