# All Permissions

Defines every permission exposed by the generated plugin.

The compiler writes these entries to the `permissions` array in `config.json`. ThunderPHP reads permissions from plugin configuration, so this node does not generate a runtime filter hook.

Duplicate slugs are ignored. Use **Has Permission** in execution flows to call `user_can()` with one of the configured slugs.
