## ADDED Requirements

### Requirement: Guest users see Login and Sign Up buttons on the gallery page
The system SHALL render a Login button (linking to `/login`) and a Sign Up button (linking to `/register`) on the cake gallery page when the current visitor is not logged in. The buttons SHALL be output server-side via a PHP hook so they are present in the initial HTML response.

#### Scenario: Guest visitor loads the gallery
- **WHEN** a non-authenticated user loads the WooCommerce cake gallery page
- **THEN** a button pair containing "Login" and "Sign Up" SHALL be visible in the page between the category filter row and the product grid

#### Scenario: Logged-in user loads the gallery
- **WHEN** an authenticated user loads the WooCommerce cake gallery page
- **THEN** the Login and Sign Up buttons SHALL NOT be present in the rendered HTML

#### Scenario: Login button click
- **WHEN** a guest clicks the "Login" button
- **THEN** the browser SHALL navigate to `/login`

#### Scenario: Sign Up button click
- **WHEN** a guest clicks the "Sign Up" button
- **THEN** the browser SHALL navigate to `/register`

### Requirement: Auth buttons match the site's visual style
The Login and Sign Up buttons SHALL visually match the existing cake gallery pill-button aesthetic: rounded corners (pill shape), border, and hover state consistent with the category filter pills.

#### Scenario: Button renders with correct base style
- **WHEN** the auth button block is rendered
- **THEN** both buttons SHALL have `border-radius: 50px`, a light border, and appropriate padding matching the existing pill buttons

#### Scenario: Hover state
- **WHEN** a guest hovers over either auth button
- **THEN** the button SHALL display a subtle background color change and scale transform consistent with the existing pill hover style
