## Context

The wedding cake order form requires a default color selection ("White") to be active on load. The existing implementation manipulated the checkbox state directly, which occasionally resulted in inconsistencies if other scripts were still processing the DOM or if the checkbox expected a physical click to trigger associated behaviors (like image updates).

## Goals / Non-Goals

**Goals:**
- Ensure the "White" color checkbox is reliably selected on page load.
- Trigger all side effects (image swaps) associated with selecting "White".
- Provide a small buffer (100ms) to allow the environment to initialize.

**Non-Goals:**
- Refactoring the entire color-changing logic.
- Changing the filenames or paths of the images.

## Decisions

- **Use `setTimeout` with 100ms delay**: A 100ms delay is a standard "safe" threshold to wait for common DOM/script initialization without being perceptible to the user.
- **Use `click()` instead of `checked = true`**: Calling `.click()` on the element simulates a real user interaction, which guarantees that all event listeners (including those added by third-party libraries or standard DOM listeners) are triggered in the correct order.

## Risks / Trade-offs

- **[Risk]**: 100ms might not be enough on extremely slow devices. → **Mitigation**: 100ms is generally sufficient for DOMContentLoaded-based scripts. If issues persist, the delay could be increased, but it should remain low to prevent visible "flicker" of the selection.
- **[Risk]**: The checkbox might not be in the DOM yet. → **Mitigation**: The script is wrapped in `DOMContentLoaded`, and we added a null check before calling `.click()`.
