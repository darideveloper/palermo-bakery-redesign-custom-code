## Context

The current favorites system uses a hybrid approach, storing favorites in `localStorage` for guests and syncing with the server when logged in. We are removing the guest feature entirely to force users to log in to save their favorite cakes.

## Goals / Non-Goals

**Goals:**
- Completely remove `localStorage` dependency for the favorites feature.
- Ensure unauthenticated users are redirected to the login page when attempting to favorite a cake.
- Simplify the frontend logic by relying only on server state for favorites.
- Maintain existing functionality for authenticated users.

**Non-Goals:**
- Redesigning the favorites page UI.
- Changing how favorites are stored on the backend.
- Modifying the sharing functionality (other than ensuring it still works with the simplified state).

## Decisions

- **State Management**: We will use a simple Javascript array (`userFavs`) as the single source of truth for the session, populated from the server via AJAX on load if the user is authenticated. This replaces the complex `localStorage` merging logic.
- **Redirection**: We will pass the login URL from PHP via the localized `cakeFavsData` object. This ensures the redirect is dynamic and respects WordPress settings.
- **Frontend Interception**: We will intercept clicks on the `.my-custom-fav-btn` class. If `cakeFavsData.isLoggedIn` is false, we prevent the default action and use `window.location.href = cakeFavsData.loginUrl`.
- **Loading State**: We will implement a `isFavsLoaded` flag to ensure user interactions don't occur before the initial server sync is complete.
- **Cleanup**: We will explicitly clear the `my_cake_favs` localStorage key once it has been successfully migrated or upon first load of the new system to prevent stale data usage.

## Risks / Trade-offs

- **Risk**: Increased friction for users who just want to casually save a cake without creating an account immediately.
  **Mitigation**: The login/registration page handles the user flow smoothly.
- **Risk**: Regression in sharing feature functionality.
  **Mitigation**: The sharing feature will be carefully reviewed to ensure it doesn't depend on the removed local storage logic.