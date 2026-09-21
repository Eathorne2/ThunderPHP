# Render Reusable View

Includes the partial produced by a **Reusable View** architecture node.

The include runs in the current View scope, so normal page values, extracted variables and template helpers remain available. Use the same connected Look for the reusable partial and the page that renders it.

When both wrapper fields are empty, the compiler emits only the `require current_look(...)` statement. Adding wrapper classes or inline styles creates one outer `div`, and the responsive width settings apply to that wrapper.
