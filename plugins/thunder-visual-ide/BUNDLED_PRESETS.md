# Bundled Node Presets

Copy any node bundle exported by Thunder Visual IDE into this folder, or into a subfolder used as its category.

Accepted files:

- `*.thunder-nodes.json`
- `*.json` containing a node bundle

Example:

```text
presets/
├── Authentication/
│   ├── login-controller.thunder-nodes.json
│   └── registration-form.thunder-nodes.json
└── Database/
    └── users-table.thunder-nodes.json
```

Reload the IDE after copying a file. Bundled presets appear in the **Bundled presets** section and cannot be deleted from the browser. User-created presets remain stored separately in browser storage.
