# Contributing to Horizon Homes

Thank you for your interest in contributing! This project is a Joomla 6
real-estate component (`com_estate`) plus the `hornbill` site template.

## How to contribute

1. **Fork** the repository and create a feature branch from `main`.
2. Follow the existing code conventions (see below).
3. Test your changes on a local XAMPP install before opening a PR.
4. Open a **pull request** with a clear description of what changed and why.

## Branch naming

| Type | Prefix | Example |
|------|--------|---------|
| Bug fix | `fix/` | `fix/listing-price-format` |
| New feature | `feature/` | `feature/compare-tray` |
| Docs | `docs/` | `docs/readme-uml` |
| Refactor | `refactor/` | `refactor/model-queries` |

## Code conventions

### PHP (3-tier architecture)

- **Presentation tier** (`tmpl/` templates) must contain **no business logic** —
  only output rendering. If you need a new computed value, add it to the View
  or Model class.
- **Business logic tier** (`src/Controller/`, `src/Model/`) holds all validation,
  filtering, and data-access rules.
- **Data tier** (`sql/*.sql`) — schema changes go in `install.mysql.utf8.sql`
  (and the matching `uninstall.mysql.utf8.sql`). Never ship a migration that
  is not idempotent (`CREATE TABLE IF NOT EXISTS`, `ALTER TABLE` guarded).
- Use Joomla's query builder (`$db->getQuery(true)`) — never string-concatenate
  user input into SQL.
- All template files start with `\defined('_JEXEC') or die;`
- Follow PSR-12. Match the surrounding file's style (tabs, brace placement).
- **No inline comments** unless the logic is genuinely non-obvious; prefer
  clear names over narration.

### JavaScript

- Vanilla JS only — no frameworks. Use the IIFE pattern already used in
  `estate-i18n.js` and `estate-compare.js`.
- Must degrade gracefully when `localStorage` is unavailable.

### CSS

- All site CSS lives in `templates/hornbill/css/template.css`.
- Use the existing CSS custom properties (`--color-primary`, `--color-bg`,
  `--radius`, etc.) — no hard-coded hex values for brand colours.
- BEM-ish naming: `.estate-card__body`, `.estate-compare-tray__item`.

## Commit messages

- Use the imperative mood: `Add compare tray to listings page`.
- Keep the subject line under 72 characters.
- Reference an issue when one exists: `Fix #42 — price not formatted`.

## Testing checklist

Before submitting a PR:

- [ ] Site loads without PHP warnings/errors (`display_errors = On` locally).
- [ ] Admin backend still works (listings CRUD, publish/unpublish).
- [ ] Responsive: check at 640px, 820px, and desktop widths.
- [ ] New DB columns/tables are covered by both install and uninstall SQL.
- [ ] No secrets, API keys, or credentials committed.

## Reporting bugs

Open an issue with:

1. What you expected vs. what happened.
2. Steps to reproduce (URL, actions taken).
3. PHP/Joomla versions and error output.

## License

By contributing, you agree that your contributions will be licensed under the
[MIT License](LICENSE).
