# Set Current Permissions

Creates a top-level `user_permissions` filter in `plugin.php`.

For a fixed list, enter literal permission slugs in the inspector. For database-backed permissions, double-click the node to open its Permission Loader flow. The callback begins with the existing `$permissions` array in scope. Use a Variable node named `permissions`, combine it with the loaded values, and finish with a Return node.
