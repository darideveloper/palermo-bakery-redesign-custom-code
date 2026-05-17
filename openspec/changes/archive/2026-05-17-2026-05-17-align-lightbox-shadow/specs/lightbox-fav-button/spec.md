## ADDED Requirements

### Requirement: Fav button appears in the lightbox
The system SHALL inject a heart fav button inside the prettyPhoto lightbox whenever it is opened, positioned centered at the bottom of the displayed image.

#### Scenario: Lightbox opens from gallery click
- **WHEN** a user clicks a gallery cake image and the prettyPhoto lightbox opens
- **THEN** a heart button (🤍 or ❤️) appears centered at the bottom of the lightbox image

#### Scenario: Button does not duplicate on repeated opens
- **WHEN** a user closes and re-opens the lightbox multiple times
- **THEN** only one fav button is present inside the lightbox at any time

### Requirement: Fav button reflects correct state on open
The system SHALL display the correct ❤️ (favorited) or 🤍 (not favorited) state based on the user's current favorites list when the lightbox opens.

#### Scenario: Product already in favorites
- **WHEN** the lightbox opens for a cake that is already in the user's favorites list
- **THEN** the button shows ❤️ and has the `is-favorited` class

#### Scenario: Product not in favorites
- **WHEN** the lightbox opens for a cake that is not in the user's favorites list
- **THEN** the button shows 🤍 and does not have the `is-favorited` class

### Requirement: Fav button toggles the product's favorite state
The system SHALL toggle the displayed cake's favorite state when the lightbox fav button is clicked, including all side effects: localStorage update, WordPress AJAX sync (if logged in), gallery card state update, and header counter update.

#### Scenario: User favorites a cake from the lightbox
- **WHEN** the lightbox is open and the user clicks the 🤍 button
- **THEN** the button switches to ❤️, the `heartPopLightbox` animation plays (preserving centering), the product ID is added to localStorage, the gallery card heart for the same cake updates to ❤️, and (if logged in) the server is synced via AJAX

#### Scenario: User un-favorites a cake from the lightbox
- **WHEN** the lightbox is open and the user clicks the ❤️ button
- **THEN** the button switches to 🤍, the product ID is removed from localStorage, and the gallery card heart for the same cake updates to 🤍

### Requirement: Fav button state updates on lightbox navigation
The system SHALL update the fav button state to reflect the newly displayed cake whenever the user navigates between images inside the lightbox using the prev/next arrows.

#### Scenario: User navigates to a different cake
- **WHEN** the user clicks the next or previous arrow inside the lightbox
- **THEN** the fav button updates to show ❤️ or 🤍 matching the newly displayed cake's favorites status

#### Scenario: User navigates with keyboard arrows
- **WHEN** the user presses the left or right keyboard arrow while the lightbox is open
- **THEN** the fav button updates to show ❤️ or 🤍 matching the newly displayed cake

### Requirement: Existing gallery card fav buttons are unaffected
The system SHALL not alter the behavior, appearance, or state of the fav buttons on gallery product cards when the lightbox fav button feature is active.

#### Scenario: Gallery cards work independently
- **WHEN** the user toggles a fav from a gallery card (not the lightbox)
- **THEN** the card button state updates normally and the lightbox button (if open for the same cake) is not disrupted

### Requirement: Fav button inherits shared styles and animations
The system SHALL apply the existing `.my-custom-fav-btn` styles (size, hover scale, `heartPop` animation) to the lightbox fav button, but SHALL use a high-contrast shadow specifically for the lightbox version.

#### Scenario: Shadow visibility
- **WHEN** the lightbox fav button is rendered
- **THEN** it SHALL have a shadow of `rgba(0, 0, 0, 0.4) 0px 7px 20px 1px` to ensure contrast against high-resolution image content.

#### Scenario: Hover interaction
- **WHEN** the user hovers over the lightbox fav button
- **THEN** the button scales up via `translateX(-50%) scale(1.15)` — preserving centering while enlarging

#### Scenario: Toggle animation
- **WHEN** the user clicks the lightbox fav button to add a favorite
- **THEN** the `heartPopLightbox` keyframe animation plays on the button (with `translateX(-50%)` preserved at every step)
