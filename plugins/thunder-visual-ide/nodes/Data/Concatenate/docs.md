# Concatenate

Joins two or more connected values into one string. Increase **Number of inputs** to expose additional value ports.

The optional separator is inserted between values. Null becomes an empty string, scalar and Stringable values are cast to strings, and arrays or objects are JSON-encoded as a safe fallback.
