# PHP Input

Reads the raw request body from `php://input` exactly once during the flow.

The node exposes:

- **Raw Body** — the original request text, or an empty string if PHP could not read the stream.
- **JSON** — the decoded JSON value, or `null` when the body is not valid JSON.
- **JSON Valid** — `true` when `json_decode()` accepted the body. A valid JSON `null` value therefore produces `JSON = null` and `JSON Valid = true`.

Use **Decode JSON as associative array** to choose between PHP arrays and `stdClass` objects. **Maximum bytes** can limit the request body; `0` reads the complete stream.

Typical JSON API flow:

```text
Start → PHP Input → If(JSON Valid)
                  └─ JSON → Array Get / Assign Variable
```

This node is intended for raw JSON, XML, text, webhook payloads, and other bodies that are not available through normal form input handling.
