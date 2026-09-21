# Controller Hook

Creates either an `add_action()` or `add_filter()` callback and owns an ordered nested execution graph.

- Choose **action** for controller/lifecycle work that does not need to return the supplied value. The Hook name field is an editable dropdown: choose a standard ThunderPHP action hook or type a custom name.
- Choose **filter** when the callback must return the value passed into it. The editable dropdown switches to the standard ThunderPHP filter hooks.
- Connect it to one or more Route nodes to add the route-name argument.
- Leave it unconnected to generate a global/custom hook with no route argument.
- Use **Callback data variable** to rename `$data` to a clearer variable such as `$menu`, `$roles`, or `$payload`.

Action example:

```php
add_action('controller', function ($data = []) {
    // Compiled flow
}, 10);
```

Filter example:

```php
add_filter('menu_items', function ($items = []) {
    // Compiled flow
    return $items;
}, 10);
```

Filter graphs contain a protected Start node and a protected **Return Filter Data** node. The compiler also adds a final fallback return, ensuring the original callback argument is always returned when a reachable branch does not terminate explicitly.

Use **Hook Data Argument** inside the nested flow to access the current callback argument without manually typing its variable name.
