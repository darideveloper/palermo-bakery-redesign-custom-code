## Context

The `prettyPhoto` lightbox library handles the close button (`.pp_close`) click internally. To modify this behavior without hacking the library's source code, we need an external listener that intercepts these clicks and redirects the intent to the overlay (`.pp_overlay`).

## Goals / Non-Goals

**Goals:**
- Completely override the default behavior of the `.pp_close` button.
- Ensure clicking the close button results in the same outcome as clicking the overlay.
- Maintain compatibility with the existing `image-lightbox.js` and `product-gallery.css`.

**Non-Goals:**
- Modifying the core `prettyPhoto` JavaScript file.
- Changing the visual appearance of the close button (handled by CSS).

## Decisions

- **Event Delegation**: Use a global `document.addEventListener('click', ...)` to catch clicks on `.pp_close`. This handles the fact that the lightbox (and its close button) are dynamically added and removed from the DOM.
- **Stop Propagation**: Use `e.stopImmediatePropagation()` and `e.preventDefault()` to ensure the library's internal listeners never receive the click event.
- **Trigger Overlay Click**: Programmatically call `click()` on the `.pp_overlay` element to trigger the library's overlay-close sequence. Include a safety check to ensure the overlay element exists before attempting to trigger the click.
- **Global CSS Hide**: Use `display: none !important` in the global `product-gallery.css` file to remove `.pp_expand` from all viewports, ensuring a cleaner UI.

## Risks / Trade-offs

- **Risk**: Timing issues where the listener might fire before or after the library's own listeners depending on how they are attached.
- **Mitigation**: Using `capture: true` or ensuring the listener is attached to the document early, combined with `stopImmediatePropagation`, should reliably intercept the event.
