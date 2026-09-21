# Thunder Local Docs

A database-free documentation plugin for ThunderPHP. It serves the bundled framework documentation at `/docs` using Markdown files and a generated PHP search index.

## Included content

- 9 topics
- 18 subtopics
- 235 documentation sections
- Full-content local search with weighted ranking
- Responsive sidebar navigation
- Markdown rendering, heading table of contents, code-copy buttons, reading time, related documents, and previous/next navigation
- No MySQL tables or migrations

## Installation

Copy the `thunder-local-docs` folder into your ThunderPHP plugins directory and enable it. The plugin route is already configured as `/docs`.

## Editing documentation

Content is stored under `content/<topic>/<subtopic>/`:

- `topic.json` contains topic metadata.
- `subtopic.json` contains subtopic metadata.
- `_intro.md` is the optional subtopic introduction.
- Numbered `.md` files contain documentation sections and JSON-compatible front matter.

After changing, adding, moving, or removing documentation files, rebuild the catalog and search index:

```bash
php plugins/thunder-local-docs/tools/build-index.php
```

The generated files are:

- `content/catalog.php`
- `storage/search-index.php`

Both are bundled, so users can search immediately without running the builder.

## Versioning

The plugin has its own semantic version and `core_requires` declaration. Documentation-content version selection is intentionally omitted because each framework package ships with the matching documentation.

## Search routing fix in 1.0.1

Search works immediately with the bundled index; no initial build is required. Version 1.0.1 uses one deterministic dispatcher for the entire `/docs` route branch. This prevents `/docs/search` from also being interpreted as the dynamic `/docs/{topic}` route on framework installations that retain more than one matching named route.

## Updates

- **1.0.2:** Increased overall typography to a Bootstrap-like 16px base and enlarged navigation, cards, article text, code, tables, search results, and supporting labels.

## Installation guide

The bundled Getting Started → Installation documentation covers local PHP/MySQL requirements, XAMPP setup, downloading from GitHub, environment checks, the `php thunder serve` command, custom host and port options, and common setup problems.
