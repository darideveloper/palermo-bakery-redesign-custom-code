## Context

The project uses a custom script `order-wedding-cake-change-cake-color.js` to handle switching between "White" and "Ivory" variants of wedding cakes. The page also features a modal lightbox (managed by a plugin) that allows users to cycle through different cake styles. Currently, the color-switching script only targets images in the gallery grid and does not observe or react to state changes within the modal lightbox.

## Goals / Non-Goals

**Goals:**
- Synchronize modal images with the selected global color (White/Ivory).
- Ensure modal navigation (arrows) triggers a color check and image update.
- Reuse existing filename transformation logic to maintain consistency.

**Non-Goals:**
- Refactoring the entire lightbox plugin.
- Changing how the White/Ivory selection itself works.

## Decisions

### 1. Extend CSS Selectors for Image Targeting
- **Decision**: Update the `cakeImages` selection in `order-wedding-cake-change-cake-color.js` to include the modal image class `.product-image-lightbox-image`.
- **Rationale**: This allows the existing `updateImagesColor` function to automatically include the modal image when a user toggles the White/Ivory checkboxes while the modal is open.

### 2. Event Delegation for Modal Navigation
- **Decision**: Attach `click` listeners to the modal's "Next" and "Previous" buttons using event delegation on the document.
- **Rationale**: The modal buttons are present in the DOM but their state changes dynamically. Event delegation is robust and avoids issues with rebinding.

### 3. Timing-Based Synchronization
- **Decision**: Use a `setTimeout(..., 250)` within the navigation click handlers before reading the caption text.
- **Rationale**: The modal lightbox plugin updates the caption asynchronously or after a transition. A short delay ensures we read the *new* variant's caption rather than the previous one.

### 4. Caption-Based Color Detection
- **Decision**: Parse the text content of `.product-image-lightbox-caption` to determine if the new variant should be White or Ivory.
- **Rationale**: The caption is the most reliable source of truth for which variant is being displayed after a navigation event.

### 5. Modal Loader Implementation
- **Decision**: Dynamically inject a loading spinner element into the modal container `.product-image-lightbox-main`. Use CSS to center it and toggle visibility via an `.is-loading` class. Added `onload` and `onerror` listeners to the modal image to ensure the spinner is dismissed regardless of the load result.
- **Rationale**: Provides better UX by signaling that an image update is in progress. The gold-themed spinner matches the site's palette.

### 6. Robust Default Selection
- **Decision**: Implemented `setWhiteDefault` with a guard (`!whiteCheckbox.checked && !ivoryCheckbox.checked`). This function is executed at 500ms, 1000ms, and 2000ms intervals, and also on `window.onload` with a 500ms delay.
- **Rationale**: Ensures the default selection sticks even if third-party scripts (like Contact Form 7) reset the form state during their own initialization.

## Risks / Trade-offs

- **[Risk]**: Plugin Update Conflict → If the lightbox plugin updates the `src` after our script runs, the change might be reverted.
- **[Mitigation]**: The 250ms delay is usually sufficient to bypass plugin transitions. If not, we might need a `MutationObserver` on the modal image.
- **[Risk]**: Caption Text Mismatch → If a cake name contains "White" or "Ivory" as part of its style name rather than its variant name.
- **[Mitigation]**: Use the established `replaceColor` logic which handles specific filename strings rather than just global text replacement.
