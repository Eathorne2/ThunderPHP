# Node-based tutorials

Thunder Visual IDE tutorials are loaded from the `tutorials/` directory. They do not require a database, migration, authentication system, or admin CRUD page.

## Directory structure

```text
tutorials/
├── index.json
├── getting-started/
│   └── first-route-and-view/
│       ├── tutorial.json
│       ├── content.html
│       ├── thumbnail.svg
│       ├── video.mp4          # optional
│       └── files/             # optional companion downloads
└── views-and-forms/
    └── visual-form-validation/
```

`index.json` defines category names and the ordered tutorial list. Each tutorial entry points to its folder:

```json
{
  "path": "getting-started/first-route-and-view"
}
```

## Tutorial metadata

Each tutorial folder requires `tutorial.json`:

```json
{
  "slug": "first-route-and-view",
  "title": "Build Your First Route and View",
  "summary": "Create a route, Controller and View.",
  "category": "getting-started",
  "order": 20,
  "level": "Beginner",
  "duration": "12 min",
  "tags": ["route", "controller", "view"],
  "thumbnail": "thumbnail.svg",
  "content": "content.html",
  "video": {
    "type": "youtube",
    "id": "YOUR_VIDEO_ID"
  },
  "published": true
}
```

### Video types

YouTube:

```json
{"type":"youtube","id":"VIDEO_ID"}
```

Vimeo:

```json
{"type":"vimeo","id":"VIDEO_ID"}
```

Local video file inside the tutorial folder:

```json
{"type":"file","file":"video.mp4"}
```

An empty video ID displays a clean “Video coming soon” placeholder while keeping the written tutorial available.

## Lesson content

`content.html` contains the article body. It is trusted plugin content and may use normal HTML. The tutorial page already styles headings, lists, code, preformatted blocks, and this optional callout pattern:

```html
<div class="tvi-tutorial-callout">
    <strong>Tip</strong>
    <p>Your useful note.</p>
</div>
```

## Companion files

Put any tutorial resources under `files/`. The detail page lists them and provides one generated ZIP download. Nested directories are supported.

## Creating a tutorial

1. Copy `tutorial-package-template/` into a category folder under `tutorials/`.
2. Rename the folder and edit `tutorial.json`.
3. Write `content.html` and replace `thumbnail.svg`.
4. Add the tutorial folder path to `tutorials/index.json`.
5. Reload `/thunder-ide/tutorials`.

No PHP registry or JavaScript file needs to be edited.
