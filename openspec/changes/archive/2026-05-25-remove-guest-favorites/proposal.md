## Why

The current system allows guests to favorite cakes using `localStorage`, which creates a disconnected experience and doesn't incentivize users to create accounts. Removing guest favorites and forcing users to log in to save cakes will drive user registration and ensure a consistent favorites list across devices.

## What Changes

- **BREAKING**: The ability to save favorite cakes as a guest without an account will be removed.
- Clicks on the favorite heart icon by unauthenticated users will redirect them to the login screen.
- The `localStorage` mechanism for storing favorites locally will be completely stripped out.
- The UI will be simplified to rely solely on the server-side state for authenticated users.

## Capabilities

### New Capabilities
- `guest-favorites-removal`: Enforce authentication for saving favorite cakes and redirecting guests to the login page.

### Modified Capabilities
- `favorites-heart-button`: Update behavior to redirect unauthenticated users instead of saving locally.

## Impact

- `src/features/favorites/fav-button.js`: Major rewrite to remove `localStorage` logic and add redirect.
- `src/core/functions.php`: Minor update to `cakeFavsData` localization to include the `loginUrl`.
- User flow will be impacted as guest saving is no longer supported.