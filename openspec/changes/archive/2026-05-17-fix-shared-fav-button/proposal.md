## Why

The "fav" button on the "Cakes Shared With You" section (`.save-shared-btn`) behaves incorrectly when opening a shared link. Its active state is not synchronized on page load, and the button ignores user clicks if the cake is already saved, preventing users from removing shared items from their favorites or toggling their state.

## What Changes

- Synchronize `.save-shared-btn` UI state on page load by including it in the `updateUI` sync function in `fav-button.js`.
- Allow the button in the shared grid to toggle the favorite state (both add and remove) by removing the strict `!favs.includes(productId)` block in the click handler.
- Update the click behavior so the shared item can be visually un-saved if removed from favorites, rather than doing nothing.

## Capabilities

### New Capabilities

### Modified Capabilities
- `favorites-heart-button`: The shared favorites save button will now properly sync its visual state and support toggling (removal) rather than only adding items.

## Impact

- **Affected code:** `fav-button.js` (specifically `updateUI` and the master click handler).
- **User Impact:** Users viewing a shared link can now see which shared cakes are already in their favorites, and they can toggle them off directly from the shared view.
