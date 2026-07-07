## Why

The cupcake floating trigger button lacks visual distinction against the page background, making it harder to notice at a glance. A subtle white glow around the cupcake icon will make it pop, increasing discoverability of the contact form.

## What Changes

- Add `filter: drop-shadow(0px 0px 15px rgba(255, 255, 255, 0.6));` to the `.cupcake-img` rule in `src/features/popup-form/custom-popup-form.css`
- This stacks on top of the existing drop-shadow for a combined glow + depth effect

## Capabilities

### New Capabilities
<!-- No new capabilities — this is a purely visual CSS tweak within the existing form-frontend spec -->
(none)

### Modified Capabilities
<!-- No spec-level behavior changes — purely presentational adjustment -->
(none)

## Impact

- **File touched**: `src/features/popup-form/custom-popup-form.css` — one property added
- **No JS, HTML, or structural changes**
- **No breaking changes** — existing shadow is preserved; glow is additive
