## ADDED Requirements

### Requirement: Shared favorite buttons sync with user state and support toggling
The system SHALL synchronize the `.save-shared-btn` elements with the user's current favorites list on page load and allow the user to toggle (add or remove) these items.

#### Scenario: Shared button state sync on load
- **WHEN** the favorites page loads a shared link and renders the shared cakes grid
- **THEN** the `.save-shared-btn` for any cake already in the user's favorites SHALL visually display as saved (e.g., ❤️ instead of 🤍)

#### Scenario: User removes a saved shared cake
- **WHEN** the user clicks a `.save-shared-btn` on a shared cake that is already in their favorites
- **THEN** the product ID SHALL be removed from localStorage and synced to the server
- **AND** the `.save-shared-btn` SHALL revert to its unsaved visual state (🤍)
- **AND** the user's own favorites grid below SHALL update to reflect the removal

#### Scenario: User saves a new shared cake
- **WHEN** the user clicks a `.save-shared-btn` on a shared cake that is NOT in their favorites
- **THEN** the product ID SHALL be added to localStorage and synced to the server
- **AND** the `.save-shared-btn` SHALL update to its saved visual state (❤️)
- **AND** the user's own favorites grid below SHALL update to include the new cake
