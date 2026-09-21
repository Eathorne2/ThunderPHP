# Set Current Roles

Creates a top-level `user_roles` filter in `plugin.php`.

For a fixed list, enter literal role slugs in the inspector. For database-backed roles, double-click the node to open its Role Loader flow. The callback begins with the existing `$roles` array in scope. Use a Variable node named `roles`, combine it with the loaded values, and finish with a Return node.
