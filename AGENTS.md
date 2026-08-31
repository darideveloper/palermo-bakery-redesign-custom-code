# Project Conventions

## File roles

- `src/core/functions.php` is the **canonical PHP source** for the WordPress
  theme. The maintainer copies it to `<theme>/functions.php` on deploy.
  Edit this file when changing PHP behavior.

- `src/core/functions_prod.php` is a **read-only mirror** of the prod-stable
  copy kept in the repo for reference. It is updated manually by the maintainer
  to mirror the live prod file. **AGENTS MUST NOT EDIT THIS FILE.** If you find
  yourself needing to change it, you are working on the wrong file — make the
  change in `src/core/functions.php` instead and remind the maintainer to
  re-mirror the prod copy.

- `docs/client-readme.md` is the bakery-team-facing documentation. Update when
  behavior changes; do not rewrite for style.

- `openspec/` is the change-management workspace. New work goes under
  `openspec/changes/<name>/`. Archived work lives under
  `openspec/changes/archive/`.

## Deployment model

This repo is a **reference mirror** of WordPress customizations. There is no
automatic build or deploy. Every file change is hand-copied into the live
WordPress install by the maintainer. Treat the repo as documentation-of-intent,
not a build artifact.

## How to handle the `functions.php` files

- Do not add a root-level `functions.php` loader. The maintainer copies
  `src/core/functions.php` directly to the WordPress theme root.
- Do not edit `src/core/functions_prod.php`. That file is the maintainer's
  prod mirror.
- When you change `src/core/functions.php`, the maintainer will mirror the
  change into `src/core/functions_prod.php` on their own schedule.
