## Context

The header heart icon (`.mini-wishlist .tongle`) shows a "Favorites Board" tooltip via a CSS `::after` pseudo-element. The current implementation centers the tooltip horizontally over the icon using `left: 50%; transform: translateX(-50%)` and `white-space: nowrap`. On desktop this works fine because the icon is never at the viewport edge. On tablet and mobile the icon sits in the far-right corner of the header, so the centered tooltip overflows the viewport to the right.

Current rule in `src/features/favorites/header-fav.css`:
```css
#sns_header .main-header .mini-wishlist .tongle::after {
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
  /* ... */
}
```

## Goals / Non-Goals

**Goals:**
- Prevent tooltip from overflowing the viewport on screens ≤ 991px.
- Keep desktop (≥ 992px) tooltip centered — no change to existing desktop style.
- Pure CSS solution — no JS required.

**Non-Goals:**
- Changing tooltip text, color, animation, or any other visual property.
- Affecting any other tooltip or header element.
- Supporting hover on touch devices (native browser limitation — out of scope).

## Decisions

### Decision: Right-align tooltip on tablet/mobile via `@media` override

**Choice**: Add a `@media (max-width: 991px)` block that overrides only the positional properties of `.tongle::after`:
```css
@media (max-width: 991px) {
  #sns_header .main-header .mini-wishlist .tongle::after {
    left: auto;
    right: 0;
    transform: none;
  }
}
```

**Why `right: 0`**: Anchors the tooltip's right edge to the icon's right edge. Since the icon is at or near the right viewport boundary, the tooltip grows leftward — always staying in-bounds.

**Why `left: auto`**: Neutralises the inherited `left: 50%` from the base rule; without this, both `left` and `right` would be set and the browser's cascading would be ambiguous.

**Why `transform: none`**: The base rule uses `transform: translateX(-50%)` to shift the centered tooltip half its width to the left. With right-alignment that shift is no longer needed and would mis-position the tooltip.

**Why 991px breakpoint**: Matches the standard Bootstrap 3 `lg` boundary used by the theme (desktop ≥ 992px, everything below is tablet/mobile).

**Alternatives considered**:
- `max-width` + wrapping: More complex, produces a two-line tooltip, less elegant.
- Hide on mobile: Loses the label entirely; not desired.
- Change base rule and override for desktop: Inverts the complexity unnecessarily.

## Risks / Trade-offs

- **Risk**: Theme may load its own media queries that re-override these rules.  
  → Mitigation: The selector is highly specific (`#sns_header .main-header .mini-wishlist .tongle::after`) — unlikely to be overridden.

- **Trade-off**: The tooltip right-aligns at exactly 992px and below — there is no intermediate "medium" alignment. Acceptable given the problem only manifests when the icon is near the right edge.

- **Trade-off**: 991px breakpoint is hardcoded. If the theme's breakpoints change, this rule may need updating. Acceptable — breakpoints rarely change in mature themes.
