## Why

The popup contact form uses a hardcoded Contact Form 7 element ID (`#wpcf7-f1874-o1`) to locate and relocate the form. CF7 appends an occurrence suffix (`-o1`, `-o2`, etc.) that varies by page depending on form count and order. On pages with multiple CF7 forms (e.g., `/order-wedding-cake/`), the ID becomes `wpcf7-f1874-o2`, causing the selector to fail. This leaves the cupcake button invisible (flash-prevention CSS keeps the wrapper `display:none`) and the raw form visible at the bottom of the page.

Additionally, page-specific theme CSS (e.g., `.page-id-1122 .wpcf7-form`) leaks into the popup, adding double padding, margin, background, and border-radius that distort the popup layout.

## What Changes

- Replace the hardcoded `#wpcf7-f1874-o1` selector in JS with an attribute-starts-with selector `[id^="wpcf7-f1874"]` that matches any occurrence suffix.
- Replace the hardcoded `#wpcf7-f1874-o1` flash-prevention CSS rule with `[id^="wpcf7-f1874"]` to hide all occurrences of the form.
- Add a `#popup-form-container .wpcf7-form` reset rule to neutralize page-specific theme styles (padding, margin, max-width, background, border-radius) that leak into the popup.

## Capabilities

### New Capabilities

_(None)_

### Modified Capabilities

- `form-frontend`: Relax the CF7 form identification requirement to use attribute-starts-with matching instead of a hardcoded occurrence suffix; add a requirement to insulate the popup form from page-specific theme CSS overrides.

## Impact

- **`src/features/popup-form/custom-popup-form.js`**: CF7 form selector changed from `#wpcf7-f1874-o1 .wpcf7-form` to `[id^="wpcf7-f1874"] .wpcf7-form`.
- **`src/features/popup-form/custom-popup-form.css`**: Flash-prevention selector changed from `#wpcf7-f1874-o1` to `[id^="wpcf7-f1874"]`; new reset rule added for `#popup-form-container .wpcf7-form`.
- **`openspec/specs/form-frontend/spec.md`**: Requirements updated to reflect flexible ID matching and theme CSS insulation.