# All Roles

Defines every role exposed by the plugin.

The compiler creates a top-level `roles` filter in `plugin.php`. The callback receives the existing associative role array, adds entries keyed by role slug, and returns the array.

`Has Role` dropdowns read from this catalog.
