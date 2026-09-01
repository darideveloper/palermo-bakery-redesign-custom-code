## 1. Implement the callback-guard tab filter

- [x] 1.1 In `src/core/functions.php`, add a `woocommerce_product_tabs` filter (priority `200`) that returns early if `$tabs` is not an array, then `unset()`s any tab whose `callback` is missing or not callable, and returns `$tabs`.
- [x] 1.2 Confirm the filter is added in a `is_product()`-applicable location and does not conflict with existing tab logic in `src/core/functions.php`.
- [x] 1.3 Run `php -l src/core/functions.php` to confirm no PHP syntax errors (if PHP CLI is available) or a JS-neutral equivalent check.

## 2. Deploy identical change to prod and update prod mirror

- [x] 2.1 Back up the live `wp-content/themes/snsvicky/functions.php` on the server.
- [x] 2.2 Upload the updated `src/core/functions.php` to the live theme `functions.php` via SFTP.
- [x] 2.3 Confirm the live file is byte-identical to the repo canonical `src/core/functions.php` (diff).
- [x] 2.4 Update the repo-side mirror `src/core/functions_prod.php` to the identical content (maintainer-approved exception to the AGENTS.md no-edit rule).

## 3. Verify

- [x] 3.1 Confirm empty-description products now return HTTP 200 and render: `sesame-street-smash-cake`, `watercolor-blooms-cake`, `roblox-birthday-cake`, `rangers-hockey-cake`.
- [x] 3.2 Confirm a previously-working product still returns 200: `tuxedo-birthday-cake-2`.
- [x] 3.3 Confirm no regressions on other product pages (spot-check `spring-rustic-ivory-white-wedding-cake`, `exquisite-wedding-cake`).
- [x] 3.4 Confirm `src/core/functions_prod.php` is byte-identical to `src/core/functions.php` (diff).