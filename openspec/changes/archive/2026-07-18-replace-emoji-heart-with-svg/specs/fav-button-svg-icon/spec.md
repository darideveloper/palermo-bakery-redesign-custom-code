## ADDED Requirements

### Requirement: SVG heart renders consistently on all platforms
The favorite button SHALL use inline SVG icons instead of raw Unicode emoji characters. The SVG SHALL render identically on iOS Safari, macOS Safari, Chrome, Firefox, and Edge.

#### Scenario: SVG icon replaces emoji on favorites page grid
- **WHEN** a logged-in user visits `/favorite-cakes/`
- **THEN** each `.my-custom-fav-btn` SHALL contain an inline SVG heart icon instead of the `❤️` emoji character
- **AND** each `.save-shared-btn` SHALL contain an inline SVG outline heart icon instead of the `🤍` emoji character

#### Scenario: SVG icon replaces emoji in gallery heart injection
- **WHEN** a logged-in user visits a gallery page
- **THEN** each dynamically injected `.my-custom-fav-btn` SHALL contain an inline SVG heart icon instead of the `❤️` emoji character

#### Scenario: SVG icon replaces emoji in lightbox
- **WHEN** a logged-in user opens the lightbox on a product image
- **THEN** the `#lightbox-fav-btn` SHALL contain an inline SVG heart icon instead of the `🤍` emoji character

### Requirement: SVG sizes correctly in all button contexts
The SVG icon SHALL respect CSS-driven sizing at 20×20px in card buttons and 28×28px in the lightbox button.

#### Scenario: SVG is 20×20px in card buttons
- **WHEN** a `.my-custom-fav-btn` or `.save-shared-btn` is rendered on the favorites page grid
- **THEN** the SVG element SHALL have a computed size of 20×20px

#### Scenario: SVG is 28×28px in lightbox button
- **WHEN** the `#lightbox-fav-btn` is rendered inside the lightbox
- **THEN** the SVG element SHALL have a computed size of 28×28px

### Requirement: No emoji text remains in button HTML
All raw Unicode emoji characters SHALL be removed from favorite button innerHTML. The `@supports` bandaid and `overflow: hidden` workaround SHALL be removed from CSS.

#### Scenario: No emoji characters in rendered buttons
- **WHEN** inspecting the innerHTML of `.my-custom-fav-btn` and `.save-shared-btn`
- **THEN** the button content SHALL NOT contain `❤️` or `🤍` emoji characters

#### Scenario: CSS bandaids are removed
- **WHEN** inspecting `favorite-page.css`
- **THEN** there SHALL be no `@supports (-webkit-touch-callout: none)` block targeting `.my-custom-fav-btn`
- **AND** there SHALL be no `overflow: hidden` on `.my-custom-fav-btn` that was added solely for the emoji fix
- **AND** there SHALL be no `.my-custom-fav-btn img.emoji` rule
