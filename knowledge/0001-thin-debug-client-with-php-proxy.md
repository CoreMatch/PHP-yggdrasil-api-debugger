# 0001. The Debugger Is a Thin HTTP Client With a PHP Relay, Not a Protocol Analyzer

## Status
accepted

## Date
2026-05-06

## Context
The project's stated purpose is "To debug any yggdrasil-api server" (README). A
browser page alone cannot do this: browsers block cross-origin requests to
arbitrary auth servers and restrict custom headers, while Yggdrasil flows need
`Authorization: Bearer` headers and multipart texture uploads. Before the current
shape, the repo went through two earlier generations: standalone PHP test pages
(`test-yggdrasil.php`, `testhaapi.php`) and a unified PHP debugger with a
server-side backend that also did database debugging (`debugger.php`,
`debugger-api.php`, portal `index.php`). The current implementation replaced all
of them in one rewrite.

## Decision
The tool is a static HTML/JS frontend plus one generic PHP relay:

- `index.html` renders one form per endpoint, grouped into two tabs (HRPAuth API
  and Zggdrasil API), plus an editable "API Base URL" field and a Test Connection
  button.
- `script.js` holds the endpoint table (`API_CONFIG`, 21 entries), builds request
  bodies per endpoint, and POSTs `api_url` / `method` / `endpoint` / `body` /
  optional `accessToken` / optional multipart `file` to `proxy.php`.
- `proxy.php` is a generic cURL relay: it joins base URL and endpoint, forwards
  the method and body, attaches `Authorization: Bearer` when a token is given,
  builds multipart bodies for texture uploads, and returns the upstream response
  body with the upstream status code.
- Responses are displayed verbatim (JSON/Raw views, HTTP status, latency). There
  is no JWT decoding, no signature verification, and no session parsing anywhere
  in the frontend — interpreting the response is left to the human operator.
- The proxy disables TLS peer and host verification to keep debugging against
  self-signed dev servers friction-free.

## Consequences
- The tool works against any reachable Yggdrasil-compatible URL; the earlier test
  page even defaulted to the official Mojang authserver, and the UI base URL is
  free-form.
- Zero build tooling and no dependencies beyond PHP with the cURL extension; the
  whole tool is four files.
- Costs and limitations:
  - Disabled TLS verification and an open relay that fetches any client-supplied
    URL are debug-only properties; the tool must not be exposed on a public host.
  - Because nothing is parsed, the tool cannot tell the operator whether tokens,
    signatures, or texture payloads are structurally correct — only the raw bytes.
  - Running it requires a PHP-capable web server serving the repo root (the page
    calls `proxy.php` by relative path); the repo contains no run instructions
    (gap, not verified against any documented workflow).
  - The earlier database-debugging capability was deliberately dropped; direct
    server-side inspection is out of scope for this tool.

## Evidence
- README.md (field: To debug any yggdrasil-api server) — purpose statement "To debug any yggdrasil-api server".
- script.js::API_CONFIG — `API_CONFIG` endpoint table (21 entries).
- script.js::setupTestConnection — Test Connection flow via `proxy.php` (endpoint `/status`).
- script.js::makeRequest — `makeRequest`: builds per-endpoint bodies, sends everything through `proxy.php`.
- script.js::displayResponse — `displayResponse`: verbatim JSON/Raw rendering only.
- proxy.php (field: Access-Control-Allow-Origin: *) — permissive CORS headers (`Access-Control-Allow-Origin: *`).
- proxy.php (field: api_url), proxy.php (field: $fullUrl) — request parameters (`api_url`, `method`, `endpoint`, `body`, `accessToken`) and URL joining.
- proxy.php (field: CURLOPT_SSL_VERIFYPEER) — `CURLOPT_SSL_VERIFYPEER` / `CURLOPT_SSL_VERIFYHOST` set to false.
- proxy.php (field: Authorization: Bearer) — optional `Authorization: Bearer` header.
- proxy.php (field: multipart/form-data) — multipart file upload handling for texture endpoints.
- proxy.php (field: echo $response) — upstream status code and body passed through.
- index.html (field: HRPAuth & Zggdrasil API Debugger), index.html (field: HRPAuth & Zggdrasil API Testing Tool) — titles "HRPAuth & Zggdrasil API Debugger" / "HRPAuth & Zggdrasil API Testing Tool".
- index.html (field: api-url), index.html (field: data-tab="zggdrasil") — free-form base URL input, Test Connection button, two API tabs.
- Verified absence of JWT/signature/decode logic in script.js (search for jwt/atob/signature/decode finds only form keys for email/TOTP verification).
- 96a58d4d966a14551e1e1f1c6541163f0909c609 "feat: add API debugger tool with proxy and documentation" — introduced index.html/script.js/styles.css/proxy.php and deleted debugger.php, debugger-api.php, index.php, api-doc.json.
- 7094ade6e2ebadd5ea6071d53e51f0fb92987ca2 "feat: add unified api debugger and portal interface" — previous generation; its portal described debugger-api.php as handling database queries (index.php:L11-L15 @ 7094ade6e2ebadd5ea6071d53e51f0fb92987ca2).
