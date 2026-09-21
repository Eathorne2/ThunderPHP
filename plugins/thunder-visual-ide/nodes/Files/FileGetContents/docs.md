# File Get Contents

Reads a local file or supported PHP stream URL with `file_get_contents()`.

The operation executes once when flow reaches the node. The native function result is preserved:

- **Contents** is a string on success and `false` on failure.
- **Success** uses the strict check `result !== false`, so an empty file is still considered successful.

Optional inputs map to the PHP function's stream context, byte offset, and length parameters. Leave **Length** disconnected to read through the end of the stream.

Enable **Suppress PHP warnings** when failure is expected and will be handled through the Success output. Do not suppress warnings merely to hide invalid or unsafe paths.

Example:

```text
Start → File Get Contents → If(Success)
              │                 ├─ true: use Contents
              │                 └─ false: log or return an error
String Path ──┘
```

Validate user-controlled paths before connecting them. Reading arbitrary paths or remote stream wrappers can expose sensitive data.
