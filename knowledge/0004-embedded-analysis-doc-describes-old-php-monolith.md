# 0004. The Embedded Endpoint Analysis Describes the Old PHP Monolith, Not the Current Contract

## Status
accepted

## Context
The repo ships `api-endpoints-analysis.md`, a ~1090-line Chinese-language
analysis of the HRPAuth backend API. It describes a monolithic PHP backend: a
`public/index.php` front controller routing to `controllers/AuthController`,
`controllers/TOTPController`, `controllers/KeyGenController`, and a bundled
`modules/zggdrasilapi` module. That architecture is not the current one: the
Yggdrasil surface lives in the Go service HRPAuth-Yggdrasil-API, outward
documentation is maintained in HA-Contract as the single source of truth, and
PHP-era endpoint docs are kept in HA-Contract's archive.

## Decision
Keep `api-endpoints-analysis.md` in this repo as background material for the
debugger's forms.

## Consequences
- The document describes an architecture that no longer exists in that shape:
  `POST /login` with a remember token, `/generate-key` writing `keys/*.pem` and
  self-disabling, and in-process `modules/zggdrasilapi` routing are all PHP-era
  descriptions. It is background context, not a contract.
- Where it disagrees with HA-Contract, HA-Contract wins (this knowledge base's
  stated order of authority).
- Costs and limitations:
  - The doc can mislead newcomers into thinking the described routes/DB layouts
    still exist; its own language (Chinese) also differs from HA-Contract's
    English-only documentation convention.
  - Any future maintenance of this debugger should re-baseline the forms and
    this doc against HA-Contract first.
  - Relationship to the Go service: no code-level integration exists (this tool
    only speaks HTTP to whatever base URL is entered); the connection is purely
    that the Go service implements the surface this tool probes.

## Evidence
- api-endpoints-analysis.md (field: HRPAuth 后端 API 端点分析报告) — title "HRPAuth 后端 API 端点分析报告".
- api-endpoints-analysis.md (field: AuthController@login) — `/login` routed to `controllers/AuthController@login`.
- api-endpoints-analysis.md (field: 此端点用于生成 Yggdrasil API 签名所需的 RSA 密钥对) — `/generate-key` notes (RSA key pair for Yggdrasil signing, self-disabling endpoint, `signaturePublickey`).
- api-endpoints-analysis.md (field: modules/zggdrasilapi/src/auth/authenticate.php) — `/authserver/authenticate` routed to `modules/zggdrasilapi/src/auth/authenticate.php`.
- api-endpoints-analysis.md (field: 路由结构) — PHP front-controller routing table for the monolith.
- /home/lnb/Desktop/HA/HA-Contract/README.md (field: Documentation Center) [xref] — "Documentation Center (Single Source of Truth)".
- /home/lnb/Desktop/HA/HA-Contract/README.md (field: no longer used as development references) [xref] — old/drifted documents archived and no longer used as development references.
- /home/lnb/Desktop/HA/HA-Contract/README.md (field: must be written in English) [xref] — all HA-Contract documentation must be written in English.
- /home/lnb/Desktop/HA/HA-Contract/archive/docs/HRPAuth/endpoints/ [xref] — archived PHP-era endpoint docs (auth.md, keygen.md, totp.md, texture.md, yggdrasil.md, etc.).
- /home/lnb/Desktop/HA/HRPAuth-Yggdrasil-API/README.md (field: Yggdrasil-compatible Minecraft authentication service) [xref] — current Go service description.
- /home/lnb/Desktop/HA/HRPAuth-Yggdrasil-API/README.md (field: The API contract is maintained in) [xref] — the Go service's API contract is maintained in HA-Contract.
