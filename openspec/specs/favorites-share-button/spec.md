## ADDED Requirements

### Requirement: Share button is styled as a pill with an icon and hover interaction
The system SHALL style the `#share-favs-page-btn` button as a pill-shaped element consistent with the site's category filter pill buttons, with a share icon (📤), smooth hover transitions, and no inline styles.

#### Scenario: Button appearance when visible
- **WHEN** the share button is displayed (after the favorites grid renders with at least one item)
- **THEN** it SHALL appear as a pill shape (`border-radius: 50px`), dark background (`#333`), white text, with a 📤 icon to the left of the label text, and a subtle box shadow

#### Scenario: Hover interaction
- **WHEN** the user hovers over the share button
- **THEN** the button SHALL lift slightly (`translateY(-2px) scale(1.03)`) with a deeper shadow and a slightly darker background (`#1a1a1a`), transitioning smoothly over 0.2s

#### Scenario: Active/press interaction
- **WHEN** the user clicks and holds the share button
- **THEN** the button SHALL compress slightly (`translateY(0) scale(0.98)`) to provide tactile feedback

### Requirement: Share button inline styles are removed and replaced with a CSS rule
The system SHALL remove all visual inline styles from the `#share-favs-page-btn` HTML in `functions.php`, keeping only `style="display:none"` for JS-controlled visibility. All visual rules SHALL reside in `favorite-page.css` under the `#share-favs-page-btn` selector.

#### Scenario: Button HTML contains no visual inline styles
- **WHEN** inspecting the rendered HTML of the favorites page shortcode
- **THEN** the `#share-favs-page-btn` element SHALL have no inline `padding`, `background`, `color`, `border`, `border-radius`, `cursor`, `font-weight`, or `box-shadow` attributes

#### Scenario: Button CSS is in the stylesheet
- **WHEN** inspecting `favorite-page.css`
- **THEN** a `#share-favs-page-btn` rule SHALL exist defining all visual properties

### Requirement: Share button structure uses icon and text spans
The system SHALL render the share button content as two child spans — one for the icon and one for the label text — to allow independent styling of each.

#### Scenario: Icon and text are separate elements
- **WHEN** inspecting the rendered share button HTML
- **THEN** it SHALL contain a `<span class="share-btn-icon">📤</span>` and a `<span class="share-btn-text">Share My Favorites</span>` as direct children

### Requirement: Share button visibility is controlled with inline-flex
The system SHALL show the share button using `display: inline-flex` (not `inline-block`) so that the CSS flex layout (gap, icon alignment) is preserved when JS makes the button visible.

#### Scenario: Button shown after grid renders
- **WHEN** `renderUserFavoritesGrid()` resolves with at least one favorite
- **THEN** `sharePageBtn.style.display` SHALL be set to `"inline-flex"`
- **AND** the icon and text SHALL be horizontally aligned with the correct gap

### Requirement: Share button copy-to-clipboard feedback preserves icon and text span structure
The system SHALL display the clipboard copy feedback using the same icon+text span structure so that the button layout does not jump during the 2-second feedback window.

#### Scenario: Clipboard copy feedback
- **WHEN** the user clicks the share button and the URL is copied
- **THEN** the button SHALL temporarily display a ✅ icon span and a "Link Copied!" text span for 2 seconds
- **AND** after 2 seconds the button SHALL revert to the 📤 icon span and "Share My Favorites" text span
- **AND** the button layout (pill shape, flex alignment) SHALL remain visually consistent throughout
