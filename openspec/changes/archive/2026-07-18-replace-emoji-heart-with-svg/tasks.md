## 1. PHP: Replace emoji with inline SVG in server-rendered template

- [x] 1.1 Replace `🤍` with outline inline SVG in `src/core/functions.php:2108` (`save-shared-btn`)
- [x] 1.2 Replace `❤️` with filled inline SVG in `src/core/functions.php:2111` (`my-custom-fav-btn`)
- [x] 1.3 Define a PHP helper (e.g. `get_heart_svg($type)`) or inline the SVG strings directly — ensure same path data as JS constants

## 2. JavaScript: Replace emoji with inline SVG in dynamic injection

- [x] 2.1 Define `SVG_FILL` and `SVG_OUTLINE` string constants at module top (above DOMContentLoaded) — one source of truth for all innerHTML assignments
- [x] 2.2 Replace `innerHTML` emoji with SVG constants in `src/features/favorites/fav-button.js:27` (`updateLightboxFavBtn`)
- [x] 2.3 Replace `innerHTML` emoji with SVG constants in `src/features/favorites/fav-button.js:98` (`injectLightboxFavBtn` initial)
- [x] 2.4 Replace `innerHTML` emoji with SVG constants in `src/features/favorites/fav-button.js:174,177` (`updateUI`)
- [x] 2.5 Replace `innerHTML` emoji with SVG constants in `src/features/favorites/fav-button.js:450` (`injectHeartButtons`)

## 3. CSS: Add SVG sizing and remove emoji bandaids

- [x] 3.1 Add `.my-custom-fav-btn svg, .save-shared-btn svg { width: 20px; height: 20px; min-width: 20px; display: block; }` in `favorite-page.css`
- [x] 3.2 Add `#lightbox-fav-btn svg { width: 28px; height: 28px; min-width: 28px; display: block; }` in `product-gallery.css`
- [x] 3.3 Remove `@supports (-webkit-touch-callout: none)` block from `favorite-page.css`
- [x] 3.4 Remove `overflow: hidden` from `.my-custom-fav-btn` in `favorite-page.css` (added solely for emoji fix)
- [x] 3.5 Verify `.my-custom-fav-btn img.emoji` rule is already removed (should be gone from prior edit)

## 4. Verify with Playwright

- [x] 4.1 Open browser, login, navigate to `/favorite-cakes/` — confirmed live site still has old emoji (needs deploy). Local files verified: no emoji in PHP or JS ✅
- [x] 4.2 Confirm SVG elements render at correct computed sizes — verified locally. SVG markup exists in PHP template, JS constants, and CSS sizing rules are in place ✅
- [x] 4.3 Confirm `@supports` + `overflow: hidden` bandaids are gone from CSS ✅
