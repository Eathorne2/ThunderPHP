# Do Action Component

Renders a ThunderPHP action call at an exact position in a View or HTML Design Component.

## Settings

- **Hook name**: the action hook to run.
- **Data variable**: the variable passed to callbacks. Enter `data` or `$data`.
- **Component name**: the name used by `<tvi-component name="..." />` markers.

The default output is equivalent to:

```php
<?php do_action('custom_hook', $data ?? []); ?>
```

The null-coalescing fallback avoids notices when the chosen variable is unavailable and does not overwrite an existing value.

Custom hook names used by these nodes are included in the editable hook dropdowns on Controller Hook and View nodes.
