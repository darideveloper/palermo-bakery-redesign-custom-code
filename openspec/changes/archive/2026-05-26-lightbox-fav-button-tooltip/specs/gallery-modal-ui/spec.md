## MODIFIED Requirements

### Requirement: Modal Favorite Button Hover State
The modal favorite button SHALL display a tooltip with the text "Add to Favorites" when hovered by the user.

#### Scenario: User hovers over the favorite button
- **WHEN** the user hovers the cursor over the favorite button in the modal
- **THEN** a tooltip positioned above the button appears displaying the text "Add to Favorites"
- **AND** the tooltip disappears when the cursor moves away from the button

#### Scenario: Tooltip styling matches share button tooltip
- **WHEN** the favorite button tooltip is visible
- **THEN** it SHALL use the same visual styling as the share button's "Copy Link" tooltip (dark background, white text, rounded corners, opacity transition)