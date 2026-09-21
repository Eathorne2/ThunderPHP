# Query Builder Method

Calls methods from ThunderPHP's `QueryBuilder` trait through a `\Core\Database` instance. The method list is derived from the supplied QueryBuilder implementation and reference guide.

The Query Builder input is optional. When unconnected, the compiler uses the graph-level `$db = new \Core\Database();` service.

- Connect an Array node to **Columns** for `select()` and `groupBy()`; values are unpacked into variadic arguments.
- `where()` and `orWhere()` support both `(column, value)` and `(column, operator, value)` forms.
- `whereNested()`, `union()` and `unionAll()` accept callable or builder expressions.
- `delete()` and `update()` without conditions affect the entire table.

Use **Browse Methods…** in the inspector, or double-click the node, to search all QueryBuilder methods and read their exact signatures, return types, descriptions and examples before selecting one.
