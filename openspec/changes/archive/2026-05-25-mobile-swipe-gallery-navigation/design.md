## Context

The cake gallery uses a customized `prettyPhoto` lightbox. While navigation buttons have been visually improved and hit areas expanded, mobile users still lack the intuitive "swipe" gesture typical of modern image galleries.

## Goals / Non-Goals

**Goals:**
- Implement horizontal swipe gestures (left/right) to navigate the lightbox.
- Maintain compatibility with existing custom UI elements (Favorite/Share buttons).
- Keep implementation lightweight without adding new external libraries.

**Non-Goals:**
- Implementing multi-touch gestures (pinch-to-zoom).
- Changing the underlying lightbox library.

## Decisions

- **Direct DOM Interaction**: We will trigger `.click()` on the existing `.pp_next` and `.pp_previous` buttons. This ensures we leverage the library's built-in image loading and transition logic.
- **Simple Gesture Tracking**: We will use `touchstart` and `touchend` events to calculate the vector of movement. This avoids the overhead of a full gesture library.
- **Capture Phase Listeners**: Listeners will be attached to `document` using the **capture phase** (`useCapture: true`). This ensures we intercept touch events before `prettyPhoto`'s internal listeners can potentially call `stopPropagation()`.
- **CSS Hinting**: Add `touch-action: pan-y` to the lightbox container via CSS. This tells the browser to only handle vertical scrolling, preventing accidental "back/forward" browser navigation and background jumping during horizontal swipes.
- **Event Delegation & Exclusion**: Since the lightbox DOM is dynamic, listeners will be persistent. We will explicitly ignore swipes that originate from interactive UI elements like the "Favorite" or "Share" buttons using `e.target.closest('#lightbox-btn-container')`.
- **Thresholds**: A 50px horizontal threshold will be used to distinguish swipes from taps, and a delta check (`Math.abs(deltaX) > Math.abs(deltaY)`) will prevent accidental triggers during vertical scrolling.

## Risks / Trade-offs

- **[Risk] Interaction Conflict** → Swiping over the Favorite or Share buttons might accidentally trigger navigation. 
  - **Mitigation**: Use `e.target.closest('#lightbox-btn-container')` to ignore swipe logic if the touch started on interactive UI elements.
- **[Risk] Library Incompatibility** → `prettyPhoto` might stop propagation of certain events. 
  - **Mitigation**: Use the `capture` phase or attach listeners to the highest possible level in the modal hierarchy.
