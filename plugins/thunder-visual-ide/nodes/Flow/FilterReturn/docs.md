# Return Filter Data

This protected terminal node is created automatically when a Controller Hook is changed to **filter**.

It returns the exact callback argument configured by the owning controller:

```php
return $data;
```

or, when the callback variable is named `items`:

```php
return $items;
```

The node and the filter graph's Start node cannot be deleted. Switching the Controller Hook back to **action** removes this filter-only return node.
