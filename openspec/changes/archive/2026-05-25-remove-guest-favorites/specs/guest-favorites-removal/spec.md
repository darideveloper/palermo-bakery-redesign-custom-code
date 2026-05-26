## ADDED Requirements

### Requirement: Force authentication to save favorites
The system SHALL require a user to be authenticated in order to add a cake to their favorites. Guest users SHALL NOT be able to save favorites locally.

#### Scenario: Unauthenticated user clicks heart button
- **WHEN** an unauthenticated user clicks the `.my-custom-fav-btn` heart button anywhere on the site (gallery, lightbox, shared section)
- **THEN** the default action (saving the favorite) SHALL be prevented
- **AND** the user SHALL be redirected to the login page

#### Scenario: Authenticated user clicks heart button
- **WHEN** an authenticated user clicks the `.my-custom-fav-btn` heart button
- **THEN** the favorite SHALL be saved to their server profile
- **AND** the UI SHALL update immediately

### Requirement: Remove local storage dependency
The system SHALL no longer use `localStorage` key `my_cake_favs` to store or merge favorite states. The source of truth for the session SHALL be an in-memory variable populated from the server.

#### Scenario: App initialization for unauthenticated user
- **WHEN** the application initializes for an unauthenticated user
- **THEN** it SHALL NOT attempt to read or write `my_cake_favs` in `localStorage`
- **AND** the session state SHALL remain empty

#### Scenario: App initialization for authenticated user
- **WHEN** the application initializes for an authenticated user
- **THEN** it SHALL request the favorites from the server via AJAX
- **AND** it SHALL NOT attempt to read, write, or merge with `my_cake_favs` in `localStorage`