## Context

The popup contact form (`src/features/popup-form/`) uses a Cupcake icon floating button to toggle visibility of a popup container that houses a Contact Form 7 form. The JS script identifies the CF7 form by a hardcoded ID (`#wpcf7-f1874-o1`) and relocates it into the popup. A CSS flash-prevention rule also targets that same hardcoded ID.

On the `/order-wedding-cake/` page, a second CF7 shortcode exists (the wedding cake form), which shifts the popup form's occurrence suffix from `-o1` to `-o2`. The hardcoded selector fails, so: (1) the form is never relocated, (2) `#custom-popup-wrapper` stays `display:none`, hiding the cupcake button, and (3) the raw form renders at the bottom of the page unstyled.

Additionally, the wedding cake page has page-specific theme CSS (`.page-id-1122 .wpcf7-form`) that adds `padding: 35px`, `margin: 0 auto 50px`, `max-width: 900px`, `background`, and `border-radius` intended for the full-page wedding cake form. These rules leak into the popup's relocated form because there was no reset rule for `#popup-form-container .wpcf7-form`.

## Goals / Non-Goals

**Goals:**
- Make the popup form work on any page regardless of CF7 occurrence suffix
- Insulate popup form styling from page-specific theme CSS overrides
- Maintain identical visual appearance across all pages

**Non-Goals:**
- Changing the popup's visual design or layout
- Supporting multiple CF7 forms in the same popup
- Modifying the CF7 plugin or theme CSS files

## Decisions

1. **Attribute-starts-with selector `[id^="wpcf7-f1874"]`** over regex or JS string matching — CSS attribute selectors are supported in all target browsers, keep selectors consistent between JS and CSS, and avoid the fragility of hardcoded occurrence suffixes.

2. **`!important` on `.wpcf7-form` reset rules** — Page-specific theme selectors like `.page-id-1122 .wpcf7-form` have equal specificity (0,0,2,0) to our `#popup-form-container .wpcf7-form` (0,1,1,0) which already wins. However, to guarantee insulation against any future theme specificity escalation (e.g., `body .page-id-1122 .wpcf7-form`), `!important` ensures the reset is definitive. This is acceptable because the popup form should never inherit page-specific form styling.

3. **Reset rule targets `#popup-form-container .wpcf7-form`** instead of individual properties — A consolidated reset is cleaner and more maintainable than overriding each leaking property individually. The reset zeroes out `padding`, `margin`, `max-width`, `background`, and `border-radius` — the properties commonly set by theme page-specific form rules.

## Risks / Trade-offs

- **[Selector broadness]** `[id^="wpcf7-f1874"]` matches any element whose ID starts with `wpcf7-f1874`. If form ID 1874 is rendered multiple times on a page (unlikely but possible), all instances would be hidden by the flash-prevention rule and the first one found would be relocated. → Mitigation: CF7 rarely renders the same form twice; the script only relocates the first match via `querySelector`.

- **[!important escalation]** Using `!important` on reset properties could conflict with future popup-specific overrides. → Mitigation: Future popup overrides should also use `!important` or increase specificity, which is the established pattern in this CSS file (e.g., submit button already uses `!important`).