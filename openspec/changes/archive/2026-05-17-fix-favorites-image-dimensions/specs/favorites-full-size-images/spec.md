## ADDED Requirements

### Requirement: Full-Resolution Image Sources
The Favorite Cakes grid AJAX handler SHALL retrieve the original full-size image URL for each product instead of the 'large' dimension-specific thumbnail.

#### Scenario: Verify image URL format
- **WHEN** the favorites grid is rendered via AJAX
- **THEN** the `src` attribute of the `<img>` tags MUST NOT contain dimension suffixes (e.g., `-1024x1024`)

### Requirement: Full-Resolution Lightbox Links
The Favorite Cakes grid links SHALL use the original full-size image URL for the `href` attribute to ensure the lightbox displays high-quality images.

#### Scenario: Verify lightbox link format
- **WHEN** a product in the favorites grid is clicked
- **THEN** the URL opened in the prettyPhoto lightbox MUST be the original un-suffixed image file
