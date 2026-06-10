## Context

The `/favorite-cakes/` page renders via a shortcode `[my_favorite_cakes]` that outputs an HTML skeleton. A JavaScript module (`fav-button.js`) populates the grid via AJAX. For guest users, the JS shows a text "Please login" link inside the loading message. The cake gallery page already has pill-style Login and Sign Up buttons rendered server-side via a `woocommerce_before_shop_loop` hook, styled by `.gallery-auth-buttons` classes in `category-filter-menu.css` (loaded globally).

## Goals / Non-Goals

**Goals:**
- Guest users see pill-style Login and Sign Up buttons on `/favorite-cakes/` between the page title and the loading message area
- Logged-in users see no buttons (HTML not rendered)
- Reuse existing `.gallery-auth-buttons` CSS with no new styles
- The existing text-based "Please login" link inside the loading message is preserved

**Non-Goals:**
- No changes to the gallery page auth buttons
- No changes to the login/register page templates
- No new CSS — reuse existing classes
- No changes to the logged-in user experience
- No changes to the JS loading message behavior

## Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Rendering layer | **PHP shortcode** (not JS injection) | Consistent with gallery page approach; buttons present in initial HTML with no FOUC; matches existing pattern of `is_user_logged_in()` gating |
| Placement | **Between `<h2>` title and `<p>` loading message** in shortcode output | Title provides context for guest ("My Favorite Cakes" still makes sense), buttons are a natural CTA before the loading area |
| JS loading message | **Preserved as-is** — not hidden | The text "login" link provides an additional CTA alongside the pill buttons; minimal change scope |
| CSS | **Reuse `.gallery-auth-buttons`** from `category-filter-menu.css` | Classes are already global; identical pill styling; zero new CSS |

## Risks / Trade-offs

- **[None] CSS leakage** — `.gallery-auth-buttons` is a specific class with no side effects. Safe to reuse on any page.
- **[Low] User refreshes the favorites page while logged out** — Buttons are re-rendered server-side on every pageload. No state concerns.
