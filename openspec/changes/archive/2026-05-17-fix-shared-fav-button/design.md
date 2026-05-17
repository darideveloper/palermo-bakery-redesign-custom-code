## Context

The favorites page (`/favorite-cakes/`) allows users to view their saved cakes and open shared links containing cakes shared by others. Cakes shared with the user are rendered via a server-side PHP AJAX call with a hardcoded unselected heart button (`🤍`, class `.save-shared-btn`). The JavaScript responsible for managing favorite state (`fav-button.js`) currently ignores these shared buttons on page load (failing to mark already-saved items with `❤️`) and restricts the click handler so that it only processes "add" actions. This prevents users from toggling off a shared cake if they have already saved it.

## Goals / Non-Goals

**Goals:**
- Ensure the state of `.save-shared-btn` elements is synchronized on page load to accurately reflect if the user has already favorited them.
- Allow users to toggle (both save and remove) the favorite status of shared cakes directly from the "Cakes Shared With You" section.

**Non-Goals:**
- We are not changing the visual design or layout of the buttons.
- We are not altering the core `testToggleFav` storage/API logic, just how the UI reacts to it.

## Decisions

**1. Update `updateUI` for both button structures**
- **Rationale:** The `updateUI` function currently assumes buttons are `.my-custom-fav-btn` inside a `.product-inner` container using a YITH wishlist element to get the product ID. The shared buttons (`.save-shared-btn`) store the ID directly in `data-product-id` and have a different DOM structure. We must update `updateUI` to handle both cases (e.g., query for `.my-custom-fav-btn, .save-shared-btn` and use the appropriate logic to extract the product ID for each).

**2. Fix the race condition in state synchronization**
- **Rationale:** The shared cakes grid is rendered asynchronously via `renderGrid()`. When `initFavorites()` calls `updateUI()` on load, the shared items might not be in the DOM yet. To fix this, we will call `updateUI(JSON.parse(localStorage.getItem(storageKey)) || [])` inside the `.then()` block of `renderGrid` right after `listContainer.innerHTML = response.data;`.

**3. Refactor the `.save-shared-btn` click handler (DRY Principle)**
- **Rationale:** The current handler explicitly wraps its logic in `if (!favs.includes(productId))`, which blocks removal. We will remove this restriction and directly call `window.testToggleFav(productId)`. Since `testToggleFav` internally calls `updateUI()`, we do not need manual DOM manipulation (like fading out or changing the inner HTML manually) in the click handler—the updated `updateUI` will automatically handle toggling the `❤️` / `🤍` icon. We will keep `renderUserFavoritesGrid()` so the bottom grid stays in sync.

## Risks / Trade-offs

- **Risk:** Re-rendering the user's favorites grid (`renderUserFavoritesGrid()`) on every toggle of a shared item might trigger unnecessary network requests if clicked rapidly.
- **Mitigation:** This is consistent with the existing behavior when adding a shared item. Since this is a specific user-initiated action, the minor network overhead is acceptable for guaranteed UI consistency.
