# Plugin Manager

`plugin-manager` is the ThunderPHP admin plugin for managing plugins and looks from the admin area.

## Main Features

- List installed plugins from their `config.json` files
- Display plugin metadata, thumbnails and `README.md`
- Show routes and permissions declared by each plugin
- Show required and optional dependency status
- Show core compatibility warnings using `outdated_plugins()`
- Enable or disable plugins by updating `active` in `config.json`
- Change plugin priority by editing `index` in `config.json`
- Export a plugin as a ZIP package for transport to another website
- Upload a plugin ZIP, inspect it, then install or update it
- Upload, activate and delete looks
- Edit plugin `config.json` with validation
- Create automatic backup ZIPs before destructive changes
- Display migration status using the existing `thunder_migrations` table
- Run plugin migrations through the namespaced `run_migrations()` helper
- Browse a remote marketplace JSON feed and install/update plugins from it
- Verify marketplace downloads with optional SHA-256 checksums

## Admin Routes

- `/admin/plugins`
- `/admin/plugins/view/{plugin_id}`
- `/admin/plugins/install`
- `/admin/plugins/config/{plugin_id}`
- `/admin/plugins/looks/{plugin_id}`
- `/admin/plugins/dependencies`
- `/admin/plugins/migrations`
- `/admin/plugins/migrations/{plugin_id}`
- `/admin/plugins/marketplace`
- `/admin/plugins/marketplace-settings`

## Required Dependency

This plugin expects the `thunder-admin` plugin to provide the admin shell and hooks:

- `admin_before_links`
- `admin_main_content`

## Permissions

The plugin registers these permissions:

- `manage-plugins`
- `edit-plugin-config`
- `delete-plugins`
- `manage-plugin-looks`
- `install-marketplace-plugins`

## Marketplace JSON Format

A simple marketplace server only needs to expose a JSON feed and plugin ZIP files.

Example:

```json
{
  "marketplace": "ThunderPHP Marketplace",
  "version": "1.0.0",
  "plugins": [
    {
      "id": "basic-blog",
      "name": "Basic Blog",
      "version": "1.0.0",
      "description": "A simple blog plugin for ThunderPHP.",
      "author": "Eathorne Choongo",
      "category": "Content",
      "type": "free",
      "price": 0,
      "core_requires": "^1.0.0",
      "requires": [],
      "optional": ["media-uploader"],
      "thumbnail": "https://example.com/marketplace/images/basic-blog.jpg",
      "readme": "https://example.com/marketplace/readmes/basic-blog.md",
      "download_url": "https://example.com/marketplace/packages/basic-blog-1.0.0.zip",
      "checksum": "sha256-hash-here"
    }
  ]
}
```

The `checksum` field is optional, but strongly recommended. It should be the raw SHA-256 hash of the ZIP file, or prefixed as `sha256-...`.

## Marketplace Install Flow

1. The local plugin manager fetches the marketplace JSON feed.
2. It compares remote plugin IDs and versions against local plugin configs.
3. The user clicks Install, Update or Reinstall.
4. The ZIP package is downloaded into a temporary marketplace cache folder.
5. The package checksum is verified when supplied.
6. The ZIP is inspected for unsafe paths and a valid `config.json`.
7. Existing plugin files are backed up if this is an update.
8. The package is extracted into the plugins folder.
9. Migrations are run using `run_migrations()`.

## Migration Status

Migration status uses your existing table:

```sql
CREATE TABLE `thunder_migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_id` varchar(150) NOT NULL,
  `migration_name` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL DEFAULT 1,
  `checksum` varchar(64) DEFAULT NULL,
  `ran_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_plugin_migration` (`plugin_id`,`migration_name`)
);
```

The manager scans each plugin's `migrations/` folder and compares file names with `thunder_migrations.migration_name` for the matching `plugin_id`.

Statuses:

- `pending`: file exists but has not run
- `ran`: file exists and has run
- `changed`: file exists and has run, but checksum differs
- `missing-file`: migration is recorded in the DB but the file is missing

## Migration Runner Hookup

The helper `PluginManager\run_migrations()` avoids `shell_exec()` and captures migration output with `ob_start()` / `ob_get_clean()`.

Because migration runner class names can vary, connect this helper to your actual migration class inside `functions.php` if needed.

## Package Format

Plugin ZIP packages should contain one root folder:

```text
basic-blog.zip
└── basic-blog/
    ├── config.json
    ├── plugin.php
    ├── README.md
    ├── controllers/
    ├── models/
    ├── looks/
    ├── migrations/
    └── assets/
```

Look ZIP packages should contain one root folder:

```text
main.zip
└── main/
    ├── look.json
    ├── admin/
    ├── frontend/
    └── assets/
```

## Safety Notes

- Packages are inspected before install.
- ZIP paths containing `../`, absolute paths or Windows drive paths are rejected.
- Backups are created before updates, deletes and config edits.
- Marketplace downloads support SHA-256 verification.
- Required marketplace dependencies block installation until installed.

## Interface Improvements in 1.2.0

- Click anywhere on an installed-plugin card to open its details page.
- Search installed plugins by name, ID, author or description.
- Filter the list by active, disabled or health-issue status.
- View installed, active, disabled and attention totals at a glance.
- Read `README.md` files as styled Markdown rather than raw text.
- Use the application's `Parsedown` class when available, with safe mode enabled.
- Fall back to the plugin's bundled safe Markdown renderer when Parsedown is unavailable.
- Resolve relative README images and links against the plugin folder in the fallback renderer.
- Copy the original README Markdown from the details page.
- Navigate long details pages through section anchors.
- Use keyboard access on clickable plugin cards.
- Respect reduced-motion browser preferences.
- Show loading feedback for submitted actions.
