# PVTL Site Search

WordPress plugin that adds an **admin-only** tool to find **published** posts and pages whose **body content** contains a given search term. Useful for audits, migrations, or locating copy across custom post types without opening each item in the editor.

## Features

- **Admin menu** — “Site Search” appears under the main admin menu (dashicon: search).
- **Capability** — Only users who can `manage_options` (typically Administrators) can access the screen.
- **Scope** — All public post types, `post_status` = `publish`, via `post_type => any`.
- **Matching** — Case-insensitive search in `post_content` only (not titles or meta).
- **Results** — Permalink, post type, ID, occurrence count, contextual excerpts (~150 chars before/after each match) with the term highlighted, plus a textarea of plain URLs for copying.

## Requirements

- WordPress 5.0 or higher (uses standard APIs; not formally version-gated in code).
- PHP 8.0 or higher (see `composer.json`).

## Performance note

Each search loads **all** published posts into memory (`posts_per_page => -1`) and scans content in PHP. On very large sites this can be slow or hit memory limits; consider running during off-peak hours or on a staging copy.

## Installation

### Composer (e.g. Bedrock)

```bash
composer require pvtl/pvtl-site-search
```

Ensure your setup installs packages of type `wordpress-plugin` into `wp-content/plugins` (or your configured `installer-paths`).

### Manual

1. Copy the plugin folder to `wp-content/plugins/pvtl-site-search/`.
2. Activate **PVTL Site Search** in **Plugins**.

## Usage

1. In wp-admin, open **Site Search**.
2. Enter a term and submit.
3. Review matches and copy URLs from the list at the bottom if needed.

## Releases and versioning

Releases are **Git tags** (e.g. `v1.0.0`). [`composer.json`](composer.json) intentionally has **no** `"version"` field so [Packagist](https://packagist.org) and Composer take the version from the tag. If you add `"version"` back, it must **exactly** match each tag or Packagist may skip tags with a “version mismatch” warning.

Keep the WordPress plugin header `Version:` in `pvtl-site-search.php` in sync with the tag you ship (for wp-admin display).

## Package metadata

| Item         | Value |
|-------------|--------|
| Composer    | `pvtl/pvtl-site-search` |
| Plugin slug | `pvtl-site-search` |

## Author

Pivotal Agency Pty Ltd — [pivotalagency.com.au](https://www.pivotalagency.com.au/)

## License

MIT — see [LICENSE.md](LICENSE.md).
