# Request Service

Provides the shared `\Core\Request` object for the current execution graph.

The compiler uses Thunder Visual IDE's normal Request service prelude, so the generated graph creates only one `$request` instance even when several Request nodes or existing Request-aware nodes are used.

Connect the **Request** output to one or more **Request Method** nodes. A Request Method can also be used without this node; it will request the same shared service automatically.
