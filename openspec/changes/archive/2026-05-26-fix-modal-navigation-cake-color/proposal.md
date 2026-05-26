## Why

When users navigate between wedding cake variants inside the modal lightbox, the image does not update to match the selected color (White/Ivory), even though the caption reflects the change. This creates a confusing user experience where the visual representation is inconsistent with the label. Additionally, the modal lacks visual feedback when images are loading, and the default color selection logic needed to be more robust.

## What Changes

- **Modal Synchronization**: Implemented logic to detect color requirements (White/Ivory) from the modal's variant caption and update the image accordingly.
- **Extended Image Targeting**: Expanded the color-switching script to include images inside the modal lightbox using a broader CSS selector.
- **Event Delegation**: Added a global click listener to catch modal navigation events (Next/Previous arrows) and initial modal opening.
- **Loading Feedback**: Implemented a centered, gold-themed loading spinner in the modal that appears during navigation or color changes and hides once the image has fully loaded (or failed).
- **Robust Defaulting**: Re-enabled and refined the automatic "White" selection on page load to check if it's already active before clicking, preventing redundant event triggers. This logic runs at multiple intervals (500ms, 1000ms, 2000ms) and on `window.onload` to ensure persistence against third-party form resets.
- **Resilient Image Loading**: Added `onload` and `onerror` handlers to ensure the loader is always dismissed correctly when image updates occur, providing a fail-safe visual experience.

## Capabilities

### New Capabilities
- `modal-cake-color-sync`: Real-time synchronization of cake images inside the modal lightbox based on the detected color variant in the caption.
- `modal-loading-feedback`: Integrated visual loading state for modal image transitions and color swaps.

### Modified Capabilities
- `wedding-cake-color-defaults`: Updated to include modal images in the color-switching logic and improved the reliability of the initial page-load default selection.

## Impact

- `src/features/order-cake/order-wedding-cake-change-cake-color.js`: Consolidated all logic, including CSS injection for the loader and event delegation.
- No changes to existing plugin files or CSS files; all logic is self-contained in the custom feature script for easier maintenance.
