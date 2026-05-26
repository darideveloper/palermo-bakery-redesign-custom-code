## Context

The popup form currently uses an inline SVG cupcake icon. To improve brand consistency, we are replacing it with a specific remotely hosted PNG image (`https://ccdev2026.wpenginepowered.com/wp-content/uploads/2026/05/cupcake-help-icon-120.png`).

## Goals / Non-Goals

**Goals:**
- Replace the inline SVG `<svg class="cupcake-svg">` with an `<img>` tag in `custom-popup-form.js`.
- Update `custom-popup-form.css` to properly style the new `<img>` element so it fits the floating button design seamlessly.

**Non-Goals:**
- Modifying the popup form's visibility logic.
- Updating other icons on the site.

## Decisions

**1. Update DOM Structure in JS:**
We will update the template literal `popupShellHTML` in `src/features/popup-form/custom-popup-form.js` to use an `<img>` tag instead of the `<svg>` element. The new image will use a descriptive class like `.cupcake-img`.

**2. Update CSS Styling:**
We will update `src/features/popup-form/custom-popup-form.css` to change the `.cupcake-svg` selector to `.cupcake-img`. We'll apply styles like `width: 100%`, `height: 100%`, and `object-fit: contain` to ensure the image scales correctly within the `.cupcake-container`, and importantly, we will retain the existing `filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));` to preserve the visual depth of the button.

## Risks / Trade-offs

- [Risk] Image loading delay or failure. → The image is hosted on WP Engine (CDN), which is generally fast, but we should include an `alt` attribute (e.g., `alt="Ask Me"`) as a fallback for accessibility and in case the image fails to load.
