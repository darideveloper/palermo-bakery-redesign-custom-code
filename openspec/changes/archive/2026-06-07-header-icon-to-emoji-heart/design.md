## Context

The header favorites shortcut icon is implemented as a CSS-only override in `src/features/favorites/header-fav.css`. It uses the `.mini-wishlist .tongle:before` pseudo-element to swap the YITH Wishlist default heart icon with a Font Awesome birthday cake (`\f1fd`). The icon is never toggled by JavaScript — only the `.number` count badge is updated dynamically. The gallery and lightbox favorite buttons use inline heart emoji (`❤️`/`🤍`) set via JS `innerHTML`.

This is a one-file, two-line CSS change with no architectural complexity.

## Goals / Non-Goals

**Goals:**
- Replace the Font Awesome birthday cake glyph with a filled red heart emoji (`❤️`)
- Set the icon color to always-red (`#e74c3c`)
- Keep the count badge position, styling, and JS update logic unchanged
- Zero JavaScript changes

**Non-Goals:**
- Not changing the toggle behavior (the header icon always stays filled — it is a shortcut link, not a toggle)
- Not modifying gallery card or lightbox favorite buttons
- Not changing the count badge styling or behavior
- Not touching PHP or theme template files

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Emoji vs Font Awesome icon | **Emoji** (`❤️`) | Matches the gallery/lightbox hearts exactly for visual consistency. The gallery uses `❤️` and `🤍` via `innerHTML` — using the same emoji in CSS `content` ensures identical rendering. |
| CSS-only vs JS injection | **CSS-only** | The existing icon is already a `::before` pseudo-element with no JS toggle. No reason to add JS complexity for a static visual swap. |
| Color via `color` vs inline SVG/tint | **`color` property** | The `::before` pseudo-element inherits text color. Setting `color: #e74c3c` is the simplest way to make the icon red. Emoji `❤️` already renders red by default on most platforms, but an explicit `color` override guarantees consistency. |

**Alternatives considered:**
- **Font Awesome `\f004` (fa-heart)**: Would work but renders slightly differently from the gallery's emoji `❤️`. Gallery consistency was prioritized over Font Awesome consistency with the theme.
- **CSS mask with SVG**: Overkill for a single icon swap.

## Risks / Trade-offs

| Risk | Mitigation |
|---|---|
| Emoji rendering varies slightly across OS/browser (shade of red, shape) | Acceptable — `❤️` is one of the most consistently rendered emoji cross-platform. The explicit `color: #e74c3c` declaration normalizes the shade on most renderers. |
| Font-family inheritance on `::before` could override emoji rendering | The pseudo-element inherits the theme's font stack. Emoji fallback is well-supported — if the primary font lacks the glyph, the OS emoji font is used. |
| If YITH Wishlist plugin updates and changes its markup | This is an existing risk for all header-fav.css overrides. The selectors are standard and stable across YITH Wishlist versions. |
