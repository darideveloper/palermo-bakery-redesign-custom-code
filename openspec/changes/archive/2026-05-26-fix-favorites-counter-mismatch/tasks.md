## 1. Backend Deduplication and Validation

- [x] 1.1 Update `ajax_get_user_favorites` in `src/core/functions.php` to validate each ID (ensure it exists and is a published product) and deduplicate before returning the string.
- [x] 1.2 Update `ajax_save_user_favorites` in `src/core/functions.php` to deduplicate IDs using `array_unique` and `array_filter` before saving.
- [x] 1.3 Update `ajax_render_favorite_products` in `src/core/functions.php` to deduplicate IDs before querying for items.

## 2. Frontend Deduplication

- [x] 2.1 Update `initFavorites` in `src/features/favorites/fav-button.js` to deduplicate IDs using `[...new Set(array)]` after splitting the server response.
- [x] 2.2 Update `testToggleFav` in `src/features/favorites/fav-button.js` to ensure the final array remains unique (safety check).

## 3. Verification

- [x] 3.1 Verify that the favorites counter correctly matches the number of unique items rendered in the grid.
- [x] 3.2 Verify that toggling favorites on and off correctly updates the counter without introducing duplicates.
