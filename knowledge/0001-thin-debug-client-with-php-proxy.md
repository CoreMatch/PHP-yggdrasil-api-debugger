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
- README.md:1-2 — purpose statement "To debug any yggdrasil-api server".
- script.js:1-23 — `API_CONFIG` endpoint table (21 entries).
- script.js:79-112 — Test Connection flow via `proxy.php` (endpoint `/status` at line 91).
- script.js:118-212 — `makeRequest`: builds per-endpoint bodies, sends everything through `proxy.php`.
- script.js:265-273 — `displayResponse`: verbatim JSON/Raw rendering only.
- proxy.php:2-4 — permissive CORS headers (`Access-Control-Allow-Origin: *`).
- proxy.php:11-15, 23 — request parameters (`api_url`, `method`, `endpoint`, `body`, `accessToken`) and URL joining.
- proxy.php:32-33 — `CURLOPT_SSL_VERIFYPEER` / `CURLOPT_SSL_VERIFYHOST` set to false.
- proxy.php:39-41 — optional `Authorization: Bearer` header.
- proxy.php:43-67 — multipart file upload handling for texture endpoints.
- proxy.php:88-96 — upstream status code and body passed through.
- index.html:6, 13 — titles "HRPAuth & Zggdrasil API Debugger" / "HRPAuth & Zggdrasil API Testing Tool".
- index.html:16-25 — free-form base URL input, Test Connection button, two API tabs.
- Verified absence of JWT/signature/decode logic in script.js (search for jwt/atob/signature/decode finds only form keys for email/TOTP verification).
- 96a58d4d966a14551e1e1f1c6541163f0909c609 "feat: add API debugger tool with proxy and documentation" — introduced index.html/script.js/styles.css/proxy.php and deleted debugger.php, debugger-api.php, index.php, api-doc.json.
- 7094ade6e2ebadd5ea6071d53e51f0fb92987ca2 "feat: add unified api debugger and portal interface" — previous generation; its portal described debugger-api.php as handling database queries (7094ade:index.php:11-15).
