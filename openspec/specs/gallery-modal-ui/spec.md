# gallery-modal-ui Specification

## Purpose
TBD - created by archiving change increase-modal-button-size. Update Purpose after archive.
## Requirements
### Requirement: Modal Button Sizing
The gallery modal SHALL display favorite and share buttons at an increased, accessible size of approximately 56x56 pixels with a 28px icon font size.

#### Scenario: User views the modal
- **WHEN** the user opens the product gallery modal
- **THEN** the favorite and share buttons are displayed prominently and are easy to tap on mobile devices

### Requirement: Modal Share Button Hover State
The modal share button SHALL display a tooltip with the text "Copy Link" when hovered by the user.

#### Scenario: User hovers over the share button
- **WHEN** the user hovers the cursor over the share button in the modal
- **THEN** a tooltip positioned above the button appears displaying the text "Copy Link"

### Requirement: Modal Favorite Button Hover State
The modal favorite button SHALL display a tooltip with the text "Add to Favorites" when hovered by the user.

#### Scenario: User hovers over the favorite button
- **WHEN** the user hovers the cursor over the favorite button in the modal
- **THEN** a tooltip positioned above the button appears displaying the text "Add to Favorites"
- **AND** the tooltip disappears when the cursor moves away from the button

#### Scenario: Tooltip styling matches share button tooltip
- **WHEN** the favorite button tooltip is visible
- **THEN** it SHALL use the same visual styling as the share button's "Copy Link" tooltip (dark background, white text, rounded corners, opacity transition)

