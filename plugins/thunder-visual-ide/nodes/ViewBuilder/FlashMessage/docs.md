# Flash Message

Reads a ThunderPHP flash message using `message()` and displays it.

## Erase after display

Enable **Erase after display** to remove the session message only when it is actually rendered. The generated view first checks the message with `erase = false`, then retrieves it for output with `erase = true`.

This avoids erasing the message during the condition check before the output expression can display it.
