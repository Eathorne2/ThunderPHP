# Thunder Blog Block Format

The block browser reads lightweight metadata from `index.json`. Each indexed slug points to `blocks/{slug}/block.json`.

Thunder Blog uses two control levels:

- **Block settings** affect the complete block.
- **Element settings** affect one selected element or one item in a repeatable element group.

## Manifest example

```json
{
  "slug": "feature-list",
  "name": "Feature List",
  "category": "Content",
  "description": "A repeatable list of features.",
  "version": "1.0.0",
  "contexts": ["post", "page"],
  "settings": {
    "defaults": {
      "columns": "3"
    },
    "schema": [
      {
        "key": "columns",
        "label": "Columns",
        "type": "select",
        "options": {
          "2": "Two",
          "3": "Three",
          "4": "Four"
        },
        "default": "3"
      }
    ]
  },
  "elements": [
    {
      "key": "items",
      "label": "Feature",
      "repeatable": true,
      "min": 1,
      "max": 12,
      "controls": {
        "duplicate": true,
        "clone": true,
        "delete": true,
        "move": true
      },
      "appearance": {
        "color": true,
        "background_color": true,
        "defaults": {
          "color": "",
          "background_color": ""
        }
      },
      "defaults": {
        "title": "Feature title",
        "text": "Explain the feature."
      },
      "default_items": [
        {"title": "Fast", "text": "Describe the first feature."},
        {"title": "Flexible", "text": "Describe the second feature."}
      ],
      "schema": [
        {
          "key": "title",
          "label": "Title",
          "type": "text",
          "selector": ".tb-feature__title"
        },
        {
          "key": "text",
          "label": "Text",
          "type": "textarea",
          "selector": ".tb-feature__text"
        }
      ],
      "template": "<article class=\"tb-feature__item\"><h3 class=\"tb-feature__title\">{{title}}</h3><p class=\"tb-feature__text\">{{text}}</p></article>"
    }
  ],
  "html": "<div class=\"tb-block tb-block--features\" style=\"--tb-feature-columns:{{columns}}\">{{elements:items}}</div>",
  "css": ".tb-block--features{display:grid;grid-template-columns:repeat(var(--tb-feature-columns),minmax(0,1fr));gap:20px}.tb-feature__item{min-width:0}",
  "js": ""
}
```

## Block settings

`settings.defaults` supplies initial values. `settings.schema` registers inspector fields that apply to the complete block. Settings can be used in the block `html` and `css` with `{{setting_key}}` tokens.

## Elements

Every element definition requires:

```text
key
label
defaults
schema
template
```

The element template must have one root HTML element. Add a `contexts` array to both `index.json` metadata and the full manifest to control whether the block appears for posts, pages, headers, or footers. Thunder Blog tags that root internally so it can be selected in the iframe canvas.

Insert an element group into the block HTML using:

```text
{{elements:element-key}}
```

A non-repeatable element renders one item. A repeatable element can render multiple independently editable items.

## Repeatable controls

```json
{
  "repeatable": true,
  "min": 1,
  "max": 20,
  "controls": {
    "duplicate": true,
    "clone": true,
    "delete": true,
    "move": true
  }
}
```

Only enabled controls appear in the inspector. **Duplicate** copies the source values into an independent item with a new item ID. **Clone** creates a linked item: changing a field or palette colour on any member updates every item in the clone group. A linked item can be detached from the group to make it independent again. `min` and `max` are enforced in both the editor and server compiler.

## Element palette controls

An element can opt into semantic text and background colour controls:

```json
{
  "appearance": {
    "color": true,
    "background_color": true,
    "defaults": {
      "color": "",
      "background_color": "surface"
    }
  }
}
```

The inspector exposes only the named colours in the active post palette: `primary`, `primary-hover`, `secondary`, `accent`, `background`, `surface`, `text`, `muted`, `border`, `success`, `warning`, and `danger`. The document stores the palette name rather than a raw colour value, so changing the post palette updates every compatible element automatically.

## Supported field types

```text
text
textarea
richtext
url
image
number
range
color
select
html
shortcode
menu
```


## Saved menu sources

Use a block-wide field with type `menu` to let the editor choose one of the menus created under **Blog → Menus**. The block owns all menu markup and styling.

```json
{
  "settings": {
    "defaults": {"menu_id": "0"},
    "schema": [
      {"key": "menu_id", "label": "Saved menu", "type": "menu", "default": "0"}
    ]
  },
  "menu_sources": [
    {
      "setting": "menu_id",
      "token": "primary",
      "empty": "",
      "empty_preview": "<nav class=\"tb-example__menu tb-menu-empty\">Select a menu</nav>",
      "container": "<nav class=\"tb-example__menu\" aria-label=\"{{menu_name}}\">{{items}}</nav>",
      "item": "<a class=\"tb-example__link\" href=\"{{url}}\" target=\"{{target}}\">{{title}}</a>",
      "parent": "<div class=\"tb-example__parent\"><a href=\"{{url}}\">{{title}}</a><div class=\"tb-example__submenu\">{{children}}</div></div>"
    }
  ],
  "html": "<header class=\"tb-block tb-example\">{{menu:primary}}</header>"
}
```

Available menu-template tokens are `{{title}}`, `{{url}}`, `{{target}}`, `{{children}}`, `{{item_id}}`, `{{level}}`, `{{menu_name}}`, and `{{instance_id}}`. `parent` is used for items with children; `item` is used for leaf links. `empty_preview` is editor-only, while `empty` is what public compilation emits when no menu is selected.

## Direct canvas editing

Add a `selector` to an element field when the field should support direct interaction. The selector is relative to the element template root.

```json
{
  "key": "title",
  "label": "Title",
  "type": "text",
  "selector": ":scope"
}
```

Use `:scope` to target the template root itself. Text, textarea, and rich-text selectors become editable on double-click. Pasted content is inserted without source formatting. Image selectors open the plugin image browser on double-click. Fields without selectors are edited only through the inspector.

## Styling

Use semantic palette variables instead of hardcoded theme colours. Every selector should begin with a block-specific class such as `.tb-block--example` or `.tb-example__title`.

Other plugins can append block metadata through the `thunder_blog_block_index` filter, but their full manifest must still be resolvable by extending the block loading behavior or placing a matching manifest in this plugin's block directory.

## HTML and shortcode blocks

A trusted raw HTML block uses `"render_mode": "html"` and an element field named `code` with type `html`. A dynamic shortcode block uses `"render_mode": "shortcode"` and a field named `shortcode` with type `shortcode`. ThunderPHP shortcodes use dotted names, for example:

```text
[thunder-blog.posts layout="medium" order="latest" limit="6"]
```

Shortcode fields can also be placed inside normal page-layout element templates. They appear as editable text in the canvas and are replaced through `do_shortcode()` only when public content is rendered.
