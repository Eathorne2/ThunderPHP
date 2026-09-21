# Session Method

Calls a documented method on a connected `\Core\Session` object. Selecting a method changes the parameter ports and Result output type.

The included methods cover:

- setting, retrieving, checking, removing and popping session values
- dot-notation and array push/prepend workflows
- retrieving or clearing all application session data
- authentication, logout, user access and login/admin checks
- session ID regeneration and complete invalidation
- flash messages and old form input
- numeric counters
- raw data and current-user utilities
- the Session message helper

Optional inputs use the documented ThunderPHP defaults when disconnected. The **Session** output passes the same shared Session object to another Session Method node.

For `set()` with several values, connect an Array node to **Key or Data Array**. For `auth()`, connect the single user-row object returned by the relevant Model Method or query node. The docs recommend `contains_role('admin')` instead of `is_admin()` for role-based applications, but the class method remains available here.

Open **Browse Methods…** in the inspector or double-click the node to search every Session method with examples and lifecycle notes. For `message(string $type, ?string $msg = '', bool $erase = false)`, check with `erase = false` and only use `erase = true` at the point where the message is rendered.
