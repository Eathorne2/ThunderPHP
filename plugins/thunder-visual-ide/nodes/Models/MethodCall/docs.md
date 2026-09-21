# Model Method Call

Calls a method defined by a **Model** node. Parameter ports follow the selected method signature.

Use **Browse Methods…** in the inspector, or double-click the node, to open the two-column Method Browser. The left column is searchable; the right column shows the signature, return type, parameters, Markdown help, and examples stored in the model method JSON.

Choosing **Use Selected Method** updates the node and regenerates its parameter and result ports.

## Building a method chain

Open **Browse Methods / Build Chain…** to arrange a linear sequence of model methods, configure parameters, and create the complete sequence as ordinary connected Model Method Call nodes. Methods marked chainable can continue to the next step; a terminal method must be last. The generated nodes remain individually editable in the normal graph.
