## Why

The current popup form trigger uses an inline SVG cupcake icon. The user wants to replace this with a specific, remotely hosted PNG image to update the visual style and brand consistency of the "Ask Me" floating button.

## What Changes

- Remove the inline SVG `<svg class="cupcake-svg">` from the popup shell HTML wrapper.
- Add an `<img>` tag pointing to the remotely hosted PNG (`https://ccdev2026.wpenginepowered.com/wp-content/uploads/2026/05/cupcake-help-icon-120.png`).
- Update the CSS to target the new image tag instead of the SVG element, ensuring it fits perfectly within the existing `.cupcake-container` floating button.

## Capabilities

### New Capabilities
<!-- Capabilities being introduced. Replace <name> with kebab-case identifier (e.g., user-auth, data-export, api-rate-limiting). Each creates specs/<name>/spec.md -->
None

### Modified Capabilities
<!-- Existing capabilities whose REQUIREMENTS are changing (not just implementation).
     Only list here if spec-level behavior changes. Each needs a delta spec file.
     Use existing spec names from openspec/specs/. Leave empty if no requirement changes. -->
- `form-frontend`: Update the visual presentation of the trigger button by replacing the SVG icon with an image.

## Impact

- `src/features/popup-form/custom-popup-form.js` (DOM structure of the trigger)
- `src/features/popup-form/custom-popup-form.css` (Styling for the new image tag)
