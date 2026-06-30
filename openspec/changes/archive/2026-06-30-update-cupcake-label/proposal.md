## Why

The floating cupcake trigger currently displays "Ask Me" as its call-to-action label. Client wants the label updated to "Contact Us" for clearer, more professional messaging.

## What Changes

- Change the visible text in the toothpick flag from "Ask Me" to "Contact Us"
- Update the `aria-label` on the trigger button to match
- Update the `alt` text on the cupcake image to match
- Update the corresponding spec requirement in `form-frontend` to reflect the new label

## Capabilities

### New Capabilities
*(none)*

### Modified Capabilities
- `form-frontend`: The toothpick flag label requirement changes from "Ask Me" to "Contact Us" — superseding the old spec text

## Impact

- `src/features/popup-form/custom-popup-form.js` — 3 string changes (visible text, aria-label, alt)
- `openspec/specs/form-frontend/spec.md` — update requirement wording
- No API, CSS, or functional logic changes
