# HTML Boilerplate

Creates the outer HTML5 document for a View Builder graph.

## Default body

The default document contains a centered main element and a direct content marker:

```html
<body>
    <main style="margin-left:auto;margin-right:auto;max-width:1200px">
        <tvi-content />
    </main>
</body>
```

The default boilerplate does not register or run any action hooks. Add a **Do Action Component** at the required position when the page should expose a custom ThunderPHP action.

## Direct content marker

`<tvi-content />` inserts:

1. Source code from the owning View node.
2. Visual nodes connected to the boilerplate's **Page content** output.

`<tvi-children />` is accepted as an alias. If the marker is removed, direct content is inserted before `</body>` unless the document explicitly uses an `html_content` action hook.

The compiler also places generated View Builder stylesheets at this insertion point before the first visual component, including components connected directly to **Page content**.

## Social metadata

The default document includes canonical, robots, Open Graph and X/Twitter metadata. It uses optional View variables such as `$page_title`, `$page_description`, `$canonical_url`, `$site_name`, `$social_title`, `$social_description`, `$social_image`, and `$social_image_alt`, with safe fallbacks.
