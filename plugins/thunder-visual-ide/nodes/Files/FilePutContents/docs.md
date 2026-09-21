# File Put Contents

Writes data to a local file or supported PHP stream URL with `file_put_contents()`.

The operation executes once when flow reaches the node:

- **Bytes Written** contains the integer returned by PHP, including `0` for a successful zero-byte write, or `false` on failure.
- **Success** uses `bytesWritten !== false` and therefore distinguishes a zero-byte success from failure.

Options map to PHP's standard flags:

- **Append instead of replace** adds `FILE_APPEND`.
- **Use exclusive lock** adds `LOCK_EX`.
- **Search PHP include path** adds `FILE_USE_INCLUDE_PATH`.
- **Suppress PHP warnings** adds the `@` operator while leaving failure visible through the outputs.

Example:

```text
Start → File Put Contents → If(Success)
String Path ─────┐
String / JSON ───┴─ Data
```

Validate user-controlled paths and do not permit unrestricted writes into plugin, configuration, or public executable directories. `LOCK_EX` is useful for local files but is not a replacement for application-level concurrency rules.
