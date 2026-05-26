## ADDED Requirements

### Requirement: Lightbox Swipe Navigation
The system SHALL detect horizontal swipe gestures on the lightbox modal and trigger the corresponding navigation action.

#### Scenario: Swipe left to next image
- **WHEN** the user performs a horizontal swipe from right to left on the lightbox container (`.pp_pic_holder`)
- **THEN** the system SHALL trigger a click event on the "Next" button (`.pp_next`)

#### Scenario: Swipe right to previous image
- **WHEN** the user performs a horizontal swipe from left to right on the lightbox container (`.pp_pic_holder`)
- **THEN** the system SHALL trigger a click event on the "Previous" button (`.pp_previous`)

### Requirement: Gesture Interception via Capture Phase
The system SHALL use capture phase event listeners for touch events to ensure compatibility with third-party libraries that may attempt to stop event propagation.

#### Scenario: Listeners with useCapture
- **WHEN** initializing touch event listeners for swipe detection
- **THEN** the system SHALL set the `useCapture` flag to `true`

### Requirement: CSS Gesture Optimization
The system SHALL provide CSS hints to the browser to optimize touch behavior on the lightbox container.

#### Scenario: Touch-Action configuration
- **WHEN** the lightbox is displayed
- **THEN** the `.pp_pic_holder` element SHALL have `touch-action: pan-y` applied to it

### Requirement: Swipe Sensitivity and Intent Detection
The system SHALL implement a minimum movement threshold and axis check to distinguish between navigation swipes and accidental touches or vertical scrolling.

#### Scenario: Horizontal threshold check
- **WHEN** the horizontal distance of a touch movement exceeds 50 pixels
- **THEN** the system SHALL consider the movement a valid swipe for navigation

#### Scenario: Vertical scrolling suppression
- **WHEN** the vertical distance of a touch movement is greater than the horizontal distance
- **THEN** the system SHALL ignore the movement to allow for potential vertical interactions or to prevent accidental navigation while scrolling
