## Context

The system currently allows duplicate product IDs to be stored in the user's favorites list. While the PHP rendering logic (`WP_Query`) naturally deduplicates these items when displaying the grid, the JavaScript frontend simply counts the number of items in the array to update the counter, leading to a visual mismatch.

## Goals / Non-Goals

**Goals:**
- Synchronize the counter count with the actual number of unique cakes in the grid.
- Prevent future duplication of IDs in the state or database.
- Clean up existing duplicate data in a seamless way.

**Non-Goals:**
- Changing the grid layout (handled by a separate task).
- Modifying the login/auth flow.

## Decisions

- **JS Array Sanitization**: When fetching favorites from the server in `initFavorites`, we will wrap the resulting array in `[...new Set(array)]` to ensure immediate uniqueness.
- **Toggle Logic**: `testToggleFav` is already relatively safe because it uses `.includes()` and `.filter()`, but starting with a clean set will make it 100% reliable.
- **Server-Side Sanitization**: 
    - In `ajax_get_user_favorites`, we will validate that each ID corresponds to a published post of type `product`. This ensures that if a product is deleted or its ID is invalid, it won't be counted in the header, solving the "3 when 0" bug.
    - In `ajax_save_user_favorites`, we will split the input string, run `array_unique` and `array_filter`, and then re-join it before saving to `update_user_meta`. 
    - In `ajax_render_favorite_products`, we will also deduplicate the incoming IDs to ensure the counts are accurate during the rendering process.
- **Counter Logic**: The `updateUI` function will continue to use `list.length`, but since `list` is guaranteed unique, the count will be correct.

## Risks / Trade-offs

- **Risk**: Potential data loss if the deduplication logic accidentally removes valid IDs.
  **Mitigation**: Using `new Set()` and `array_unique` are standard, safe operations for identifying unique primitives like IDs.
