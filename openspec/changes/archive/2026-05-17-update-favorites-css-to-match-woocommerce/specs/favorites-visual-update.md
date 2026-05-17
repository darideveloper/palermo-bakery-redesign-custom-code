## ADDED Requirements

### Requirement: Favorites Grid Card Hover Effect
The favorites masonry grid cards SHALL have hover effects identical to the WooCommerce product grid.

#### Scenario: Card Hover Scale
- **WHEN** user hovers over a `.masonry-item` card
- **THEN** the card SHALL scale to `transform: scale(1.05)` with `z-index: 10` and box-shadow `0 10px 25px rgba(0, 0, 0, 0.15)`

#### Scenario: Card Hover Exit
- **WHEN** user moves cursor away from the card
- **THEN** the card SHALL return to original scale with smooth transition

### Requirement: Favorites Grid Image Hover Effect
The images within favorites masonry grid cards SHALL have zoom effects identical to the WooCommerce product grid.

#### Scenario: Image Hover Zoom
- **WHEN** user hovers over a `.masonry-item` card
- **THEN** the image inside SHALL scale to `transform: scale(1.1)` with transition duration of 0.6s

#### Scenario: Image Hover Exit
- **WHEN** user moves cursor away from the card
- **THEN** the image SHALL return to normal scale with smooth transition

### Requirement: Favorites Grid Image Container Styling
The anchor wrapping each image in the favorites grid SHALL have container styling matching WooCommerce product grid.

#### Scenario: Image Container Properties
- **WHEN** the page loads
- **THEN** each `.masonry-item a` SHALL have:
  - `display: block`
  - `width: 100%`
  - `aspect-ratio: 1 / 1`
  - `border-radius: 12px`
  - `overflow: hidden`
  - `box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05)`

#### Scenario: Image Fit Behavior
- **WHEN** images load in the container
- **THEN** images SHALL use `object-fit: cover` to fill the square container without distortion

### Requirement: Remove Button Styling
The remove button (✖) on favorites cards SHALL be styled to match the heart button positioning in the WooCommerce grid.

#### Scenario: Button Positioning
- **WHEN** the page loads
- **THEN** each `.remove-fav-btn` SHALL be positioned:
  - `position: absolute`
  - `top: 15px`
  - `right: 15px`

#### Scenario: Button Appearance
- **WHEN** the page loads
- **THEN** each `.remove-fav-btn` SHALL have:
  - `background: rgba(255, 255, 255, 0.9)`
  - `border: none`
  - `border-radius: 50%`
  - `width: 40px`
  - `height: 40px`
  - `font-size: 20px`
  - `box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15)`
  - `z-index: 99`

#### Scenario: Button Hover
- **WHEN** user hovers over the remove button
- **THEN** the button SHALL scale to `transform: scale(1.15)` with smooth transition

### Requirement: Shared Section Button Styling
The save button (🤍) on shared cakes cards SHALL be styled identically to the remove button for visual consistency.

#### Scenario: Shared Button Positioning
- **WHEN** the page loads
- **THEN** each `.save-shared-btn` SHALL be positioned:
  - `position: absolute`
  - `top: 15px`
  - `right: 15px`

#### Scenario: Shared Button Appearance
- **WHEN** the page loads
- **THEN** each `.save-shared-btn` SHALL have:
  - `background: rgba(255, 255, 255, 0.9)`
  - `border: none`
  - `border-radius: 50%`
  - `width: 40px`
  - `height: 40px`
  - `font-size: 20px`
  - `box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15)`
  - `z-index: 99`

#### Scenario: Shared Button Hover
- **WHEN** user hovers over the save button
- **THEN** the button SHALL scale to `transform: scale(1.15)` with smooth transition

### Requirement: Masonry Label Styling
The product name label beneath each image SHALL maintain consistent styling.

#### Scenario: Label Appearance
- **WHEN** the page loads
- **THEN** each `.masonry-label` SHALL have:
  - `padding: 15px`
  - `text-align: center`
  - `font-weight: 600`
  - `color: #333`
  - `background: #fff`

### Requirement: Responsive Grid Behavior
The favorites masonry grid SHALL maintain responsive behavior matching WooCommerce breakpoints.

#### Scenario: Desktop Columns
- **WHEN** viewport width is >= 992px
- **THEN** the grid SHALL display 3 columns

#### Scenario: Tablet Columns
- **WHEN** viewport width is between 576px and 991px
- **THEN** the grid SHALL display 2 columns

#### Scenario: Mobile Columns
- **WHEN** viewport width is <= 575px
- **THEN** the grid SHALL display 1 column