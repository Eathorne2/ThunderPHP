# PHP Value to JS

Serializes one PHP value into the generated View before its JavaScript asset loads, then exposes it as a browser-side `const`.

For example, these settings:

- PHP expression: `$user ?? null`
- JavaScript variable: `currentUser`

produce a safely JSON-encoded page value and make `currentUser` available throughout the owning JavaScript Flow.

The PHP expression must not include `<?php`, `<?=`, `?>`, or a trailing semicolon. Values that cannot be encoded are emitted as `null`.
