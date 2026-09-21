# Run Filter Hook (View)

Runs a ThunderPHP filter and prints its returned value exactly where the node appears in the generated View.

```php
echo do_filter('article.body', $content);
```

## Data

Connect the initial value or context through **Data**. When it is not connected, the filter receives an empty string so an unmodified filter renders nothing.

## Output safety

- Leave **Escape filtered output** disabled when registered callbacks intentionally return trusted HTML.
- Enable it when the filter should produce plain text.

As with any raw View output, only trusted callbacks should return HTML.

## Reusable component

Leave **Reusable component name** blank to render the result at the node's position.
To embed it inside hand-written View HTML, keep the node as a root component, set a component name, and insert:

```html
<tvi-component name="article-body-filter" />
```

When the named marker is used anywhere in the final View document, including inside an HTML Design Component connected to an HTML Boilerplate, the hook renders only at that marker. If no marker uses the name, the node falls back to its normal View Graph position.
