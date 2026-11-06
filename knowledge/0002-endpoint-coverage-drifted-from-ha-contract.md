# 0002. Endpoint Coverage Has Drifted From HA-Contract and the Live Go Service

## Status
accepted

## Context
Outward behavior in this ecosystem is contracted in HA-Contract, and the live
Yggdrasil implementation is the Go service HRPAuth-Yggdrasil-API. The
debugger's 21 endpoint forms target a surface that differs from that contract
in several places.

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
Drift between the forms and HA-Contract / the live Go service:

- Core login in HA-Contract is `POST /oauth/login-ticket` with optional
  TOTP/WebAuthn/email second factors; the debugger's plain `POST /login` form
  has no counterpart in the current core contract.
- `/generate-key` is absent from the current core OpenAPI; it appears only in
  HA-Contract's archive of PHP-era docs.
- The current Go service does not expose `PUT|DELETE /api/user/profile/{uuid}/
  {textureType}` (HA-Contract states this explicitly); texture writes are
  `POST /texture/upload/{uuid}` and `DELETE /texture/delete/{uuid}/{type}` there,
  so both texture forms fail against the live service.
- Test Connection's `GET /status` exists in the core business contract but is not
  routed by the Go Yggdrasil service, whose meta endpoint is `GET /`; probing a
  Yggdrasil-only deployment reports failure.
- Batch profile lookup `POST /api/profiles/minecraft` is routed by the Go service
  but has no form in the current UI.
- Costs and limitations: the tool cannot exercise the full live surface without
  updates; its stable value is the authserver/sessionserver surface and read
  paths, which the live Go service still routes. The drift is incidental rather
  than a design decision.

## Evidence
- script.js::API_CONFIG — the 21-entry `API_CONFIG` table defining all covered endpoints.
- script.js::setupTestConnection — Test Connection uses endpoint `/status`.
- script.js (field: zgg-upload-texture) — texture forms target `PUT|DELETE /api/user/profile/`.
- script.js (field: {uuid}/${textureType}) — texture endpoint path construction with `{uuid}/{textureType}`.
- index.html (field: data-tab="zggdrasil") — the two tabs (HRPAuth API / Zggdrasil API).
- /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /status:), /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /oauth/login-ticket:), /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /register:), /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /user:), /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /email-verification:), /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /totp/setup:), /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /totp/verify:), /home/lnb/Desktop/HA/HA-Contract/docs/api/openapi/hrpauth-business.yaml (field: /change-username:) [xref] — current core contract paths; `/generate-key` absent (verified by search, 0 matches).
- /home/lnb/Desktop/HA/HRPAuth-Yggdrasil-API/knowledge/0010-yggdrasil-api-surface.md (field: are not exposed directly by the current service implementation) [xref] — "The standard write endpoints `PUT/DELETE /api/user/profile/:uuid/:textureType` are not exposed directly by the current service implementation."
- /home/lnb/Desktop/HA/HRPAuth-Yggdrasil-API/main.go::Meta, /home/lnb/Desktop/HA/HRPAuth-Yggdrasil-API/main.go::UploadTexture, /home/lnb/Desktop/HA/HRPAuth-Yggdrasil-API/main.go::DeleteTexture, /home/lnb/Desktop/HA/HRPAuth-Yggdrasil-API/main.go::BatchProfiles [xref] — current route table (meta at `GET /`; texture wrappers; batch profiles; no `/status` route).
- /home/lnb/Desktop/HA/HRPAuth/knowledge/archive/endpoints/keygen.md [xref] — PHP-era keygen doc kept in the HA-Contract archive.
