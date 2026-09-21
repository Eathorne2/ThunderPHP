# Code Asset

Creates a plain-text CSS or JavaScript asset in a connected Look.

Use the View inspector's **Insert Asset Link** buttons to add a compile-time marker such as:

```html
<tvi-asset name="main-styles" />
```

The View compiler replaces the marker with a normal `current_look_http()` stylesheet or script tag.
