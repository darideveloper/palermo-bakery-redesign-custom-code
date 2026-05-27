## Context

The lightbox (prettyPhoto modal) in the cake gallery has two action buttons at the bottom center: a share button (`#lightbox-share-btn`) on the left and a favorite button (`#lightbox-fav-btn`) on the right. The share button already has a CSS-only tooltip ("Copy Link") implemented via a `::after` pseudo-element that appears on hover. The favorite button has no equivalent tooltip — users must infer its purpose from the heart icon alone.

## Goals / Non-Goals

**Goals:**
- Add an "Add to Favorites" hover tooltip to `#lightbox-fav-btn` that matches the share button's tooltip in appearance and behavior
- Maintain consistency with the existing `::after` tooltip pattern already used for the share button

**Non-Goals:**
- Changing the tooltip text dynamically based on favorited state (e.g., "Remove from Favorites" when already favorited) — a static label is sufficient
- Adding tooltips to gallery grid buttons (only the lightbox buttons are in scope)
- Modifying the JavaScript that creates or manages the fav button

## Decisions

### 1. CSS-only `::after` pseudo-element approach

**Decision:** Use the same `::after` / `hover::after` pattern already implemented for `#lightbox-share-btn`.

**Rationale:** The share button tooltip is proven, lightweight, and requires no JavaScript. Duplicating this pattern for the fav button ensures visual consistency and zero runtime cost.

**Alternative considered:** JavaScript-driven tooltip (e.g., injecting a `<span>` on hover). Rejected because it adds unnecessary DOM manipulation when a CSS-only solution exists and is already in use.

### 2. Tooltip text: "Add to Favorites"

**Decision:** Use "Add to Favorites" as the tooltip content, matching the button's existing `aria-label="Add to favorites"` set in `fav-button.js:97`.

**Rationale:** Accessibility label and tooltip should convey the same meaning. Using sentence case ("Add to Favorites") rather than the lowercase `aria-label` variant is consistent with the "Copy Link" tooltip's casing convention.

### 3. Position: `relative !important` on `#lightbox-fav-btn`

**Decision:** Add `position: relative !important` to `#lightbox-fav-btn` so the `::after` pseudo-element positions relative to the button.

**Rationale:** The current `#lightbox-fav-btn` style already resets position to `static !important`. The `::after` tooltip needs a positioned ancestor to anchor correctly. The `!important` flag is required to override the existing `position: static !important` rule on the same selector.

### 4. Exact styling parity with share button tooltip

**Decision:** Copy all `::after` and `hover::after` properties from `#lightbox-share-btn` verbatim, changing only `content`.

**Rationale:** Visual consistency between the two buttons. Both tooltips should have the same dark background, border-radius, font size, opacity transition, and upward offset.

## Risks / Trade-offs

- **[Risk] `position: relative !important` override conflict** → The fav button currently has `position: static !important` to reset its original absolute positioning within the lightbox. Changing to `relative !important` should not break layout because the button is now inside a flex container (`#lightbox-btn-container`) where `position: static` and `position: relative` behave identically for flex layout — both participate in the flex flow. Verified by the existing `#lightbox-share-btn` which already uses `position: relative !important` inside the same container without issues.