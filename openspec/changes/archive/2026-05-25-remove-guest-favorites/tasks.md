## 1. Backend Updates

- [x] 1.1 Update `inject_cake_favs_data` in `src/core/functions.php` to include `loginUrl` via `wp_login_url(get_permalink())`.

## 2. Frontend State Management Refactor

- [x] 2.1 Remove all `localStorage` logic (`getItem`, `setItem`, `storageKey`) from `src/features/favorites/fav-button.js`.
- [x] 2.2 Introduce an in-memory variable `userFavs` to hold the session favorites and an `isFavsLoaded` flag.
- [x] 2.3 Refactor `initFavorites` to populate `userFavs` directly from the server if `cakeFavsData.isLoggedIn` is true, set `isFavsLoaded = true`, and clear the `my_cake_favs` localStorage key to clean up the legacy data.
- [x] 2.4 Refactor `testToggleFav` to update the in-memory `userFavs` array instead of local storage, ensuring it only proceeds if `isFavsLoaded` is true.

## 3. Frontend Authentication Enforcement

- [x] 3.1 Update the master click event listener in `src/features/favorites/fav-button.js` to intercept clicks on `.my-custom-fav-btn`.
- [x] 3.2 If `cakeFavsData.isLoggedIn` is false, prevent default action and redirect to `cakeFavsData.loginUrl`.
- [x] 3.3 Ensure the redirect logic also applies to `.save-shared-btn` clicks in the shared section.

## 4. Lightbox Updates

- [x] 4.1 Update `updateLightboxFavBtn` to read from the in-memory `userFavs` array instead of `localStorage`.
- [x] 4.2 Update the lightbox heart button click listener in `injectLightboxFavBtn` to respect the authentication redirect logic before toggling.

## 5. UI Updates

- [x] 5.1 Update `updateUI` to read from the passed array (which will be `userFavs`) instead of local storage.
- [x] 5.2 Ensure `renderUserFavoritesGrid` uses the in-memory `userFavs` array to determine if the empty state message should be shown.
- [x] 5.3 On the Favorites page, if the user is not logged in, display a "Please login to save your favorite cakes" prompt with a link to the login page instead of the "empty favorites" message.