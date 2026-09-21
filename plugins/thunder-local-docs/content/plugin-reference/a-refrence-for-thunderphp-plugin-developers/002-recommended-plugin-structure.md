---
title: "Recommended Plugin Structure"
slug: "recommended-plugin-structure"
description: "Best-practice folder structure for ThunderPHP plugins and what each folder is for."
published: true
order: 2
source_id: 225
keywords: ["recommended", "plugin", "structure", "best-practice", "folder", "thunderphp", "plugins", "each", "reference", "refrence", "developers", "part", "does", "important", "note"]
---

ThunderPHP supports flexible plugin design, but the recommended structure is important because it is consistent, easier to maintain, and works well with the CLI tool.

Recommended structure:

```php
plugin-name/
├── controllers/
│   ├── admin/
│   └── frontend/
├── migrations/
├── models/
├── looks/
│   └── main/
│       ├── admin/
│       ├── frontend/
│       ├── assets/
│       │   ├── js/
│       │   └── css/
│       └── look.json - `required`
├── assets/images/
├── plugin.php - `required`
└── config.json - `required`
```

**What Each Part Does**

- `plugin.php` holds the plugin's hooks and filters
- `config.json` describes the plugin and how it loads
- `controllers/` can hold procedural controller files for cleaner separation
- `models/` contains plugin models and uses the plugin namespace
- `migrations/` holds migration classes and table-building logic
- `looks/` contains views and alternate looks
- `assets/images/` is for static images outside look-specific assets

**Important Note**

This structure is a best practice, not a hard requirement. A plugin author is still free to organize a plugin differently. The framework allows that, but the recommended structure is easier to understand and is supported by the `thunder` CLI generators.
