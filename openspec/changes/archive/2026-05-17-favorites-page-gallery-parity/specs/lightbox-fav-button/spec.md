## MODIFIED Requirements

### Requirement: Fav button reflects correct state on open
The system SHALL display the correct ❤️ (favorited) or 🤍 (not favorited) state based on the user's current favorites list when the lightbox opens, whether the lightbox was opened from the cake gallery or from the favorites page.

#### Scenario: Product already in favorites (opened from gallery)
- **WHEN** the lightbox opens for a cake that is already in the user's favorites list, triggered from the cake gallery
- **THEN** the button shows ❤️ and has the `is-favorited` class

#### Scenario: Product not in favorites (opened from gallery)
- **WHEN** the lightbox opens for a cake that is not in the user's favorites list, triggered from the cake gallery
- **THEN** the button shows 🤍 and does not have the `is-favorited` class

#### Scenario: Product already in favorites (opened from favorites page)
- **WHEN** the lightbox opens for a cake from the favorites page grid
- **THEN** the button shows ❤️ and has the `is-favorited` class (all cakes on the favorites page are by definition favorited)

#### Scenario: Product not in favorites (opened from favorites page — edge case)
- **WHEN** the lightbox opens for a cake from the favorites page and the product ID cannot be resolved
- **THEN** the button shows 🤍 as a safe default

### Requirement: Fav button toggles the product's favorite state
The system SHALL toggle the displayed cake's favorite state when the lightbox fav button is clicked, including all side effects: localStorage update, WordPress AJAX sync (if logged in), gallery card state update (if on gallery page), favorites card removal (if on favorites page), and header counter update.

#### Scenario: User favorites a cake from the lightbox (gallery context)
- **WHEN** the lightbox is open (triggered from the gallery) and the user clicks the 🤍 button
- **THEN** the button switches to ❤️, the `heartPopLightbox` animation plays, the product ID is added to localStorage, the gallery card heart for the same cake updates to ❤️, and (if logged in) the server is synced via AJAX

#### Scenario: User un-favorites a cake from the lightbox (gallery context)
- **WHEN** the lightbox is open (triggered from the gallery) and the user clicks the ❤️ button
- **THEN** the button switches to 🤍, the product ID is removed from localStorage, and the gallery card heart for the same cake updates to 🤍

#### Scenario: User un-favorites a cake from the lightbox (favorites page context)
- **WHEN** the lightbox is open (triggered from the favorites page) and the user clicks the ❤️ button
- **THEN** the button switches to 🤍, the product ID is removed from localStorage and synced to the server (if logged in), and the corresponding `.masonry-item` card is faded out and removed from the favorites grid

## ADDED Requirements

### Requirement: Lightbox fav button click triggers masonry card removal on favorites page
The system SHALL fade out and remove the corresponding `.masonry-item` card from the favorites grid when the lightbox fav button is clicked to un-favorite a cake while the lightbox was opened from the favorites page.

#### Scenario: Un-favorite via lightbox button on favorites page
- **WHEN** the lightbox is open (triggered from the favorites page) and the user clicks the ❤️ lightbox fav button
- **THEN** `testToggleFav(currentLightboxProductId)` SHALL be called, removing the product from favorites
- **AND** the element `#fav-item-{currentLightboxProductId}` SHALL fade out (opacity → 0 over 0.3s) and be removed from the DOM
- **AND** if no favorites remain after removal, `renderUserFavoritesGrid()` SHALL be called to show the empty state

#### Scenario: Lightbox button click on gallery page does not attempt card removal
- **WHEN** the lightbox is open on the gallery page and the user clicks the lightbox fav button
- **THEN** no `.masonry-item` removal SHALL be attempted (no `#fav-item-*` elements exist on the gallery page)

### Requirement: Lightbox product ID resolves correctly from the favorites page context
The system SHALL resolve the current lightbox product ID from the `data-product-id` attribute on the clicked `<a>` link when the lightbox is opened from the favorites page (`.masonry-item` context), falling back to the `.yith-wcwl-add-to-wishlist[data-fragment-ref]` method only when inside `.product-inner` (gallery context).

#### Scenario: Product ID resolved from gallery link
- **WHEN** the user clicks a `a[data-rel^="prettyPhoto"]` link inside a `.product-inner` card
- **THEN** `currentLightboxProductId` SHALL be set from `link.closest(".product-inner").querySelector(".yith-wcwl-add-to-wishlist").dataset.fragmentRef`

#### Scenario: Product ID resolved from favorites page link
- **WHEN** the user clicks a `a[data-rel^="prettyPhoto"]` link inside a `.masonry-item` card (favorites page)
- **THEN** `currentLightboxProductId` SHALL be set from `link.dataset.productId`

#### Scenario: Product ID is null when unresolvable
- **WHEN** a prettyPhoto link is clicked but neither a `.product-inner` YITH element nor a `data-product-id` attribute is available
- **THEN** `currentLightboxProductId` SHALL remain `null` and the lightbox fav button injection SHALL be skipped
