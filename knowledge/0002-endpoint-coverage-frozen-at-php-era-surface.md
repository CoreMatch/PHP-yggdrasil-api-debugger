# 0002. Endpoint Coverage Is Frozen at the PHP-Era HRPAuth Surface and Has Drifted From HA-Contract

## Status
accepted

## Date
2026-05-06

## Context
The 21 endpoint forms were written in April/May 2026 against the then-current
monolithic PHP HRPAuth backend and the PHP Zggdrasil mock. Since then the
ecosystem moved on: outward behavior is now contracted in HA-Contract, and the
live Yggdrasil implementation is the Go service HRPAuth-Yggdrasil-API. The
debugger's forms were never re-baselined (see entry 0004).

## Decision
Cover 21 operations, split into two tabs:

- HRPAuth core tab (10 forms): `/login`, `/register`, `/user`,
  `/email-verification` (send code / verify code), `/totp/setup`, `/totp/verify`,
  `/change-username`, `/generate-key`, `GET /status`.
- Zggdrasil tab (11 forms): `/authserver/authenticate|refresh|validate|
  invalidate|signout`, `/sessionserver/session/minecraft/join|hasJoined|
  profile/{uuid}`, meta `GET /`, and texture upload/delete via
  `PUT|DELETE /api/user/profile/{uuid}/{textureType}`.
- Test Connection probes `GET /status` on the entered base URL.

## Consequences
Known drift from HA-Contract and the live Go service (checked 2026-11-04):

- Core login is now `POST /oauth/login-ticket` with optional TOTP/WebAuthn/email
  second factors; the debugger's plain `POST /login` form does not match the
  current contract.
- `/generate-key` no longer exists in the current core OpenAPI; it survives only
  in HA-Contract's archive of PHP-era docs.
- The current Go service does not expose `PUT|DELETE /api/user/profile/{uuid}/
  {textureType}` (HA-Contract states this explicitly); texture writes are
  `POST /texture/upload/{uuid}` and `DELETE /texture/delete/{uuid}/{type}` there,
  so both texture forms fail against the live service.
- Test Connection's `GET /status` exists in the core business contract but is not
  routed by the Go Yggdrasil service, whose meta endpoint is `GET /`; probing a
  Yggdrasil-only deployment reports failure.
- Batch profile lookup `POST /api/profiles/minecraft` has no form in the current
  UI, although the earliest test page covered it.
- Costs and limitations: the tool can no longer exercise the full live surface
  without updates; its stable value is the unchanged authserver/sessionserver
  surface and read paths. Keeping the forms frozen was never an explicit
  decision — it is a consequence of the repo being dormant (entry 0004).

## Evidence
- script.js:1-23 — the 21-entry `API_CONFIG` table defining all covered endpoints.
- script.js:91 — Test Connection uses endpoint `/status`.
- script.js:21-22 — texture forms target `PUT|DELETE /api/user/profile/`.
- script.js:132-139 — texture endpoint path construction with `{uuid}/{textureType}`.
- index.html:23-24 — the two tabs (HRPAuth API / Zggdrasil API).
- HA-Contract docs/api/openapi/hrpauth-business.yaml:10 (`/status`), :30 (`/oauth/login-ticket`), :128 (`/register`), :182 (`/user`), :453 (`/email-verification`), :517-524 (`/totp/setup`, `/totp/verify`), :668 (`/change-username`); `/generate-key` absent (verified by search, 0 matches).
- HA-Contract docs/dev/HRPAuth/yggdrasil-api.md:170-173 — "The standard write endpoints `PUT/DELETE /api/user/profile/:uuid/:textureType` are not exposed directly by the current service implementation."
- HRPAuth-Yggdrasil-API main.go:147-199 — current route table (meta at `GET /` line 147; texture wrappers at lines 191-192; no `/status` route).
- HA-Contract archive/docs/HRPAuth/endpoints/keygen.md — PHP-era keygen doc, archived.
- 35893b5bcae77433223dc2659c6f13741eb55da2 "mock version", test-yggdrasil.php — earlier page covered `POST /api/profiles/minecraft` (`testBatchProfiles`) and defaulted to `https://authserver.mojang.com` (lines 5-6).
