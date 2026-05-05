# Project Context

## Purpose
The purpose of this project is to transform a standard WooCommerce product grid into a beautiful, custom-designed gallery for Palermo Bakery. It focuses on creating a professional, modern visual experience with high-quality visuals and a smooth user experience, since product sales are disabled and the site acts as a visual gallery.

## Tech Stack
- Vanilla HTML
- Vanilla CSS
- Vanilla JavaScript
- PHP (for WordPress customization snippets)

## Project Conventions

### Code Style
- Use strictly vanilla technologies (HTML, CSS, JS, PHP).
- Avoid external libraries as much as possible.
- Code must be split into logical files (e.g., one for the gallery, filters, modal, etc.) for clean organization.
- Visual consistency: product images should maintain a plain white background.

### Architecture Patterns
- **Layered Approach**: Modifications are made purely on the visual layer (HTML, CSS, JS) rather than editing WordPress's internal core code.
- All custom scripts and styles are intended to be injected via the "Simple Custom CSS and JS" WordPress plugin.
- Page builder is used for structural elements like custom section separators.

### Testing Strategy
- Manual visual testing is required across different devices (Mobile Phone, Tablet, Laptop, and Desktop PC).
- Tests must be conducted in Private or Incognito browser windows to bypass cache and verify the latest code.

### Git Workflow
- Standard branching and commit conventions. (No specific build or CI/CD pipelines as the project is not run or built locally).

## Domain Context
- **Palermo Bakery**: The website is for a bakery.
- **Gallery Mode**: WooCommerce is used as a backend for managing products, but the frontend is strictly a visual gallery (buying features are hidden).
- **Environment**: Changes are deployed to a WordPress staging/production environment (e.g., `ccdev2026.wpenginepowered.com`).

## Important Constraints
- **Not a Standalone App**: This project contains only customization scripts. It is not functional by itself and should **never** be run or built locally.
- **Non-Destructive**: Updates are purely visual ("skinning"). Core WordPress and WooCommerce data and settings must remain untouched.
- **Library Restrictions**: Future scripts must be created using the same vanilla technologies, minimizing the use of external dependencies.

## External Dependencies
- **WordPress & WooCommerce**: The underlying platform and data management system.
- **Simple Custom CSS and JS**: The WordPress plugin used to inject these files.
- **AOS (Animate On Scroll)**: Used for smooth entrance animations on titles and images.
- **prettyPhoto**: Used for the professional gallery pop-ups/lightbox feature.