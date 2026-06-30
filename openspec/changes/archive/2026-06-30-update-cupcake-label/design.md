## Context

The floating cupcake trigger button injects HTML via `custom-popup-form.js` with a toothpick flag reading "Ask Me". The label appears in three places inside the JS template literal and is referenced in the `form-frontend` spec. This is a purely cosmetic text change — no structural, behavioral, or styling changes needed.

## Goals / Non-Goals

**Goals:**
- Replace "Ask Me" with "Contact Us" on the visible toothpick flag
- Update `aria-label` and `alt` attributes to match
- Update the `form-frontend` spec to reflect the new label

**Non-Goals:**
- No CSS changes
- No behavioral or logic changes
- No new capabilities
- No i18n/l10n (hardcoded string remains)

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Label value | "Contact Us" | Client request; clearer CTA than "Ask Me" |
| String update strategy | Direct replacement in JS template | Single source of truth — no abstraction needed for one string |
| Spec update | Delta spec on `form-frontend` | Modified requirement — changes the spec-level label contract |

## Risks / Trade-offs

- [Trivial] Image alt text changes impacts screen reader output — positive, "Contact Us" is more descriptive than "Ask Me"
- The string appears 3x in the same file — must update all occurrences to stay consistent
