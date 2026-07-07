## Context

The cupcake trigger button uses a `.cupcake-img` class with a PNG image. It currently has `filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2))` for depth. Adding a white glow will increase visibility against varied backgrounds.

## Goals / Non-Goals

**Goals:**
- Add a white glow effect to the cupcake icon
- Preserve existing drop-shadow for depth

**Non-Goals:**
- No structural HTML/JS changes
- No new selectors or wrappers
- No behavior changes

## Decisions

- **Stack both shadows**: CSS `filter` accepts multiple `drop-shadow()` functions — adding the white glow after the existing black shadow keeps both effects.
- **`.cupcake-img` target**: The glow belongs on the image element, not the `.cupcake-container`, so the glow is visible and not clipped by the container bounds.
- **No media queries**: The glow is subtle enough to apply on all viewports uniformly.

## Risks / Trade-offs

- [Performance] `drop-shadow` is a GPU-accelerated filter; minimal impact with a single additional value.
- [Browser support] CSS `drop-shadow` is widely supported (IE excluded). No risk for modern browsers.
