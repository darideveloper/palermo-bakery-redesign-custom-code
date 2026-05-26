## Why

The favorite and share buttons inside the gallery modal (lightbox) are currently too small for comfortable interaction, particularly on mobile devices. Increasing their size will improve usability and accessibility. Additionally, adding a "Copy Link" text on hover for the share button will clarify its function, improving the overall user experience without cluttering the interface.

## What Changes

- Increase the size of the modal's favorite button (`#lightbox-fav-btn`) by approximately 40% (from 40x40px to 56x56px, and icon font size from 20px to 28px).
- Increase the size of the modal's share button (`#lightbox-share-btn`) by approximately 40% (from 40x40px to 56x56px, and icon font size from 20px to 28px).
- Maintain proper UI alignment by ensuring the buttons remain perfectly centered inside the `#lightbox-btn-container` and don't overlap with other elements.
- Introduce a CSS-only tooltip that displays "Copy Link" when the user hovers over the share button to clarify its behavior.

## Capabilities

### New Capabilities

- `gallery-modal-ui`: UI styling and interaction requirements for the modal buttons (favorite, share, etc.), including specific dimensions and hover states.

### Modified Capabilities

- None

## Impact

- **Affected code**: `src/features/gallery/product-gallery.css` (Only CSS changes are required).
- **Systems**: No impact on the JavaScript logic or WordPress backend. The change is strictly visual.
