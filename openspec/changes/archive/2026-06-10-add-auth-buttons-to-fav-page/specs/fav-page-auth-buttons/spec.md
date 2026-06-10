## ADDED Requirements

### Requirement: Guest users see Login and Sign Up buttons on the favorites page
The system SHALL render a Login button (linking to `/login`) and a Sign Up button (linking to `/register`) on the favorites page (`/favorite-cakes/`) when the current visitor is not logged in. The buttons SHALL be output server-side in the shortcode HTML so they are present in the initial response, positioned between the "My Favorite Cakes" heading and the loading message element.

#### Scenario: Guest visitor loads the favorites page
- **WHEN** a non-authenticated user loads `/favorite-cakes/`
- **THEN** a button pair containing "Login" and "Sign Up" SHALL be visible between the "My Favorite Cakes" heading and the loading message paragraph

#### Scenario: Logged-in user loads the favorites page
- **WHEN** an authenticated user loads `/favorite-cakes/`
- **THEN** the Login and Sign Up buttons SHALL NOT be present in the rendered HTML

#### Scenario: Login button navigates to /login
- **WHEN** a guest clicks the "Login" button
- **THEN** the browser SHALL navigate to `/login`

#### Scenario: Sign Up button navigates to /register
- **WHEN** a guest clicks the "Sign Up" button
- **THEN** the browser SHALL navigate to `/register`

### Requirement: Auth buttons match the site's visual style
The Login and Sign Up buttons SHALL use the existing `.gallery-auth-buttons` and `.gallery-auth-btn` CSS classes, inheriting the pill-button aesthetic: rounded corners (`border-radius: 50px`), border, hover scale transform, and the same filled/outlined distinction for Sign Up.

#### Scenario: Button renders with correct base style
- **WHEN** the auth button block is rendered on the favorites page
- **THEN** both buttons SHALL have `border-radius: 50px`, a light border, and appropriate padding matching the gallery pill buttons

#### Scenario: Hover state
- **WHEN** a guest hovers over either auth button
- **THEN** the button SHALL display a subtle background color change and scale transform consistent with the existing pill hover style
