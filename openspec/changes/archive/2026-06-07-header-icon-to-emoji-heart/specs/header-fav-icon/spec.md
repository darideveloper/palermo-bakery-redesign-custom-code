## ADDED Requirements

### Requirement: Header favorites icon displays filled red heart
The header `.mini-wishlist .tongle` SHALL display a filled red heart emoji (`❤️`) instead of the Font Awesome birthday cake icon. The icon SHALL always appear filled and red—it SHALL NOT toggle between outline/filled states since the header is a shortcut link, not a toggle button.

#### Scenario: Header icon renders as filled red heart
- **WHEN** the page loads and the header renders
- **THEN** the `.mini-wishlist .tongle:before` pseudo-element SHALL display `❤️` as its content with a red color (`#e74c3c`)

#### Scenario: Header icon matches gallery heart style
- **WHEN** a user views the header favorites icon next to gallery card favorite buttons
- **THEN** both SHALL use the same `❤️` emoji character for visual consistency

### Requirement: Count badge behavior unchanged
The `.mini-wishlist .tongle .number` badge SHALL continue to display the current count of favorited items. The count SHALL be updated by the existing JavaScript in `fav-button.js`.

#### Scenario: Badge count updates on fav toggle
- **WHEN** a user adds or removes a cake from favorites
- **THEN** the `.number` badge SHALL update its text content to reflect the new count

#### Scenario: Badge styling unchanged
- **WHEN** the page renders
- **THEN** the `.number` badge SHALL retain its existing position, size, border, and font styling

### Requirement: No JavaScript changes to header icon
The header icon SHALL be implemented entirely in CSS. No JavaScript SHALL toggle, animate, or modify the header icon's content or color.

#### Scenario: No JS toggles header icon
- **WHEN** a user toggles a favorite on any product
- **THEN** the JavaScript in `fav-button.js` SHALL update only the `.number` badge count, NOT the icon itself
