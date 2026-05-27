## 1. Fix CF7 Form ID Selectors

- [x] 1.1 In `src/features/popup-form/custom-popup-form.js`, change the CF7 form selector from `#wpcf7-f1874-o1 .wpcf7-form` to `[id^="wpcf7-f1874"] .wpcf7-form` to match any occurrence suffix
- [x] 1.2 In `src/features/popup-form/custom-popup-form.css`, change the flash-prevention selector from `#wpcf7-f1874-o1` to `[id^="wpcf7-f1874"]` to hide all occurrences of the form on load

## 2. Insulate Popup Form from Theme CSS

- [x] 2.1 In `src/features/popup-form/custom-popup-form.css`, add a `#popup-form-container .wpcf7-form` reset rule that zeroes out `padding`, `margin`, `max-width`, `background`, and `border-radius` with `!important` to neutralize page-specific theme overrides

## 3. Verification

- [x] 3.1 Confirm the popup form renders correctly on the home page (`/`) with the cupcake button visible and form inside the popup
- [x] 3.2 Confirm the popup form renders correctly on the wedding cake page (`/order-wedding-cake/`) — no raw form at the bottom, cupcake button visible, form inside popup with no style leaks