# Session Service

Provides the shared `\Core\Session` object for the current execution graph.

The compiler uses Thunder Visual IDE's normal Session service prelude, so generated code creates only one `$session` instance even when several Session nodes, Session Method nodes, or the existing Session Get/Set nodes are used.

Connect the **Session** output to one or more **Session Method** nodes. A Session Method can also be used without this node; it requests the same shared service automatically.
