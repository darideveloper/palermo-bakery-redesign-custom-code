## MODIFIED Requirements

### Requirement: Clicking the heart button on a favorites card removes it from favorites and fades the card out
The system SHALL toggle the cake's favorite state when the heart button is clicked on a `.masonry-item` card. Since the card is already in the favorites list, this SHALL always result in removal. The card SHALL fade out and be removed from the DOM.

#### Scenario: User clicks heart on a favorites card
- **WHEN** the user clicks the ❤️ heart button on a `.masonry-item` favorites card
- **THEN** the product ID SHALL be removed from the session state and synced to the server via AJAX
- **AND** the `.masonry-item` SHALL fade out (opacity → 0 over 0.3s) and then be removed from the DOM
- **AND** the header favorites counter SHALL decrement by exactly 1, reflecting the unique state
