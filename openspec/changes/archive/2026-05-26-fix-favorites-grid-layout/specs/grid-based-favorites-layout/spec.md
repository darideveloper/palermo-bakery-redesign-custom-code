## ADDED Requirements

### Requirement: CSS Grid for Favorites Layout
The system SHALL use CSS Grid to layout the favorites cards, ensuring they populate columns from left to right.

#### Scenario: Desktop Grid Layout
- **WHEN** the viewport width is 992px or greater
- **THEN** the `.cake-masonry-grid` SHALL display items in 3 equal-width columns
- **AND** items SHALL fill the grid starting from the first column of the first row

#### Scenario: Tablet Grid Layout
- **WHEN** the viewport width is between 576px and 991px
- **THEN** the `.cake-masonry-grid` SHALL display items in 2 equal-width columns

#### Scenario: Mobile Grid Layout
- **WHEN** the viewport width is less than 576px
- **THEN** the `.cake-masonry-grid` SHALL display items in 1 column

### Requirement: Uniform Spacing
The system SHALL maintain a consistent 20px gap between grid items.

#### Scenario: Grid Gaps
- **WHEN** multiple items are rendered in the grid
- **THEN** there SHALL be a 20px horizontal and vertical gap between items
