# Query Builder Service

Provides one shared `\Core\Database` instance for the current execution graph. ThunderPHP's `Database` class uses the `QueryBuilder` trait, so the same object can run fluent SELECT, INSERT, UPDATE, DELETE, pagination, UNION, UPSERT and raw SQL operations.

```php
$db = new \Core\Database();
```

Connect **Query Builder** to Query Builder Method nodes. A method node can also use the shared `$db` instance automatically when its object input is unconnected.

Only one database instance is emitted per execution graph, even when several Query Builder Service or Query Builder Method nodes are used.
