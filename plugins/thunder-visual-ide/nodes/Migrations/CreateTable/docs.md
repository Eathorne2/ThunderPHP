# Create Table

Creates a table through ThunderPHP migration helpers. Use the structured Columns manager for names, SQL types, lengths, nullability, defaults, unsigned values, and auto increment.

The optional **Extra SQL** field is intended for uncommon modifiers that are not yet represented by the manager.

## ENUM columns

Choose `ENUM` as the column type and enter comma-separated values in **Enum values**, for example `draft,published,archived`. The compiler quotes and escapes each value in the generated column definition.
