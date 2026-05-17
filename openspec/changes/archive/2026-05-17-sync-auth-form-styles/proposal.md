## Why

The authentication forms (login and register) lacked consistent layout constraints with the rest of the new bakery gallery design. Specifically, the form container was too wide on desktop and lacked proper vertical centering, which reduced visual polish on the core user account pages.

## What Changes

- Applied a `max-width: 600px` limit to the Theme My Login (TML) form container to ensure readability and focus.
- Implemented centered horizontal alignment and vertical spacing (`margin: 50px auto`) for the form container.
- Used `!important` to ensure these layout rules override default plugin or theme styles.

## Capabilities

### New Capabilities
- `auth-form-layout`: Define the core layout and centering constraints for the authentication forms.

### Modified Capabilities

## Impact

- `custom-auth.css`: This file now contains the primary layout rules for the authentication forms.
- `/login` and `/register` pages: These pages are visually improved with a more focused and centered form layout.
