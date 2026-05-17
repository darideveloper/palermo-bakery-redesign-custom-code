## ADDED Requirements

### Requirement: Intercept Lightbox Close Button Click
The system SHALL intercept all click events on the gallery lightbox close button (`.pp_close`) and redirect them to trigger a click on the lightbox overlay (`.pp_overlay`).

#### Scenario: Redirect Close Click to Overlay
- **WHEN** the user clicks the close button with the class `.pp_close`
- **THEN** the system SHALL programmatically trigger a click event on the element with the class `.pp_overlay`
- **AND** the default `prettyPhoto` close behavior for that button SHALL be bypassed or superseded by the overlay's close logic.

### Requirement: Hide Lightbox Expand Button
The system SHALL hide the "expand" button (`.pp_expand`) across all screen sizes to simplify the gallery interface.

#### Scenario: Verify Expand Button is Hidden
- **WHEN** the gallery lightbox is opened
- **THEN** the `.pp_expand` element SHALL NOT be visible to the user.
