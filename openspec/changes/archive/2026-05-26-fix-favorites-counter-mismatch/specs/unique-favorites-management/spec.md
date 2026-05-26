## ADDED Requirements

### Requirement: ID Deduplication in Frontend
The system SHALL ensure that the `userFavs` array in JavaScript contains only unique product IDs.

#### Scenario: Loading favorites from server
- **WHEN** the favorites list is fetched from the server via AJAX in `initFavorites`
- **THEN** the system SHALL deduplicate the IDs before assigning them to the `userFavs` array
- **AND** the UI counter SHALL reflect the count of unique IDs only

### Requirement: ID Deduplication in Backend
The system SHALL ensure that favorites stored in the user meta contain only unique IDs.

#### Scenario: Saving favorites via AJAX
- **WHEN** a request is made to `save_user_favorites`
- **THEN** the system SHALL deduplicate the IDs before updating the database
- **AND** the resulting string SHALL be a comma-separated list of unique numeric IDs
