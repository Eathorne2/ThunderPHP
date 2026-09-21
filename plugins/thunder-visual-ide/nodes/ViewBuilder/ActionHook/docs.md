# Run Action Hook (View)

Runs a ThunderPHP action hook exactly where the node appears in the generated View.
Callbacks registered with `add_action()` may echo HTML at that position.

```php
do_action('account.sidebar', $data);
```

## Data

Connect any View-compatible expression to **Data**. When it is not connected, the hook receives an empty array.

## Reusable component

Leave **Reusable component name** blank to render the hook at the node's normal position.
To embed the hook inside hand-written View HTML, keep the node as a root component, give it a component name, and insert:

```html
<tvi-component name="account-sidebar-hook" />
```

When the named marker is used anywhere in the final View document, including inside an HTML Design Component connected to an HTML Boilerplate, the hook renders only at that marker. If no marker uses the name, the node falls back to its normal View Graph position.
