## Context

The `.my-custom-fav-btn` and `.save-shared-btn` buttons use raw Unicode emoji characters (`❤️`, `🤍`) as their icon content. This renders inconsistently on iOS Safari — Apple Color Emoji glyphs ignore CSS `font-size`/`line-height`, and Safari has a known WebKit bug where `display: flex` on `<button>` elements does not create a proper flex formatting context, causing the emoji to be misaligned.

A previous CSS-only attempt (`overflow: hidden`, `@supports (-webkit-touch-callout: none)` font-size override) failed to fix the issue in iOS emulator testing.

## Goals / Non-Goals

**Goals:**
- Replace all raw emoji characters in favorite buttons with inline SVG icons
- SVG renders pixel-identical on iOS Safari, Chrome, Firefox, and desktop Safari
- SVG sizes correctly at 20×20px in card buttons and 28×28px in the lightbox
- Remove the `@supports` bandaid and `overflow: hidden` workaround (no longer needed)
- Visual appearance remains the same (filled red heart, outline white heart, 40×40 circle)

**Non-Goals:**
- No changes to button layout, positioning, hover effects, or animations
- No changes to the favorites page grid, masonry layout, or lightbox
- No behavior changes — only the icon rendering implementation changes

## Decisions

**1. Inline SVG over external .svg file**
- Avoids extra HTTP request
- No path resolution issues across subdirectories
- Can embed directly in PHP template strings and JS template literals
- Easier to maintain (single source of truth per variant)

**2. Two SVG variants: filled heart + outline heart**

Filled heart (replaces `❤️` — favorited state):

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="100%" height="100%" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#d63031"/></svg>
```

Outline heart (replaces `🤍` — not-favorited / save-shared state):

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="100%" height="100%" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="#999" stroke-width="2"/></svg>
```

Colors:
- Filled: `fill="#d63031"` (material red, matches `header-fav.css` #e74c3c closely)
- Outline: `stroke="#999"` (visible gray on white background, matches 🤍 semantics)
- Both: `viewBox="0 0 24 24"`, `aria-hidden="true"` (screen readers use button's `aria-label`)

**3. CSS controls SVG size; colors are hardcoded in SVG**
- `.my-custom-fav-btn svg, .save-shared-btn svg { width: 20px; height: 20px; min-width: 20px; display: block; }`
- `#lightbox-fav-btn svg { width: 28px; height: 28px; min-width: 28px; display: block; }`
- SVG fill/stroke colors are hardcoded in the markup (not via `currentColor`)
  — avoids relying on inherited CSS `color` property which isn't set on the button
- `min-width` prevents SVG from collapsing to 0 width in flex container — SVGs lack intrinsic inline-size in `display: flex` context, so `width` alone is ignored by the flex shrink algorithm

## Risks / Trade-offs

| Risk | Mitigation |
|------|-----------|
| SVG path dimensions differ from emoji appearance | Use standard 24×24 heart path (same as heroicons/feather) — widely recognized heart shape |
| PHP/JS string escaping issues with `<svg>` markup | Use single-quoted PHP strings; in JS, escape backticks inside template literals |
| `+` vs `-` sign for filled vs active state | `updateUI()` already toggles `.is-favorited` class — CSS target the class; JS swaps SVG innerHTML |
