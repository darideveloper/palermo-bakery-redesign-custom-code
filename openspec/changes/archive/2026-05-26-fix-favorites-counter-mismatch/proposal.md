## Why

The favorites counter at the top of the screen is displaying an incorrect count (e.g., 11 instead of 8). This is due to duplicate IDs being stored in the user's favorites list and subsequently fetched and counted by the JavaScript frontend, even though the grid only renders unique cakes.

## What Changes

- Implement deduplication of favorite IDs in both the frontend (JavaScript) and backend (PHP).
- Ensure the `userFavs` array in `fav-button.js` always contains a unique set of IDs.
- Clean up any existing duplicate data in the database during the save process.

## Capabilities

### New Capabilities
- `unique-favorites-management`: Enforce uniqueness of favorite IDs throughout the system.

### Modified Capabilities
- `favorites-heart-button`: (Requirement remains same, but implementation of state management is more robust).

## Impact

- `src/features/favorites/fav-button.js`: Update state management logic to use unique sets.
- `src/core/functions.php`: Update AJAX handlers to deduplicate IDs before saving or rendering.
