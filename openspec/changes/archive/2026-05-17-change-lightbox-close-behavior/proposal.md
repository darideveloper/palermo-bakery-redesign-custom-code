## Why

The current gallery lightbox close button utilizes the default `prettyPhoto` close logic, which might not be consistent with other UI interactions or might have limitations in specific environments. By redirecting the close button click to trigger a click on the lightbox overlay, we ensure a unified closing mechanism and cleaner state handling.

## What Changes

- **Modified**: The event handler for the gallery lightbox close button (`.pp_close`).
- **New Logic**: Clicking the close button will now programmatically trigger a click on the lightbox overlay (`.pp_overlay`).
- **UI Cleanup**: The lightbox "expand" button (`.pp_expand`) is hidden globally to simplify the interface.
- **Cleanup**: Overrides the internal library event handlers by intercepting the click at the document level, ensuring the default behavior is completely superseded.

## Capabilities

### New Capabilities
- `lightbox-close-redirection`: Implementation of a global event listener to intercept close button clicks and redirect them to the overlay.

### Modified Capabilities
<!-- No requirement-level changes to existing specs; this is an implementation-specific behavior update. -->

## Impact

- **JavaScript**: `image-lightbox.js` or a new dedicated script will need to implement the event delegation.
- **Library**: Overrides the default behavior of the `prettyPhoto` close button.
- **UI/UX**: Ensures consistent closing behavior whether the user clicks the "X" or the background overlay.
