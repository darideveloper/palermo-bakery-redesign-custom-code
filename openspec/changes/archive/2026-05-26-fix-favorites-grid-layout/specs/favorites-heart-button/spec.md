## MODIFIED Requirements

### Requirement: Heart button visual style matches the gallery heart button exactly
The system SHALL apply the existing `.my-custom-fav-btn` CSS rules to the favorites-page heart button without any additional styles, producing a 40×40px circular white button positioned at the top-right corner of the card (15px offset).

#### Scenario: Button appearance on favorites page
- **WHEN** the favorites grid is displayed
- **THEN** each card's heart button SHALL appear as a 40×40px circle with `rgba(255, 255, 255, 0.9)` background, positioned 15px from the top-right corner of the card image
- **AND** the button SHALL be correctly positioned relative to the `.masonry-item` within the new CSS Grid layout
