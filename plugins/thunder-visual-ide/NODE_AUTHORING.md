# Thunder Visual IDE Node Authoring

Node behavior is no longer hardcoded in `editor.js`. Every discoverable node is a folder beneath `nodes/` containing:

```text
nodes/Group/NodeName/
├── node.json
├── Compiler.php
└── docs.md
```

The registry recursively scans for files named exactly `node.json`. Compiler source should follow `COMPILER_STYLE.md` so node packages remain easy to study and extend. Copy `node-package-template/` into `nodes/<your-group>/<your-node>/`, rename the two example files to `node.json` and `Compiler.php`, then reload the IDE.

## node.json

Important fields:

- `type`: globally unique stable node ID.
- `label`, `category`, `icon`, `color`, `description`: editor presentation.
- `kind`: `architecture`, `flow`, `view`, `expression`, or `visual`.
- `allowed_graphs`: one or more of `architecture`, `flow`, and `view`.
- `defaults`: data copied into every new instance.
- `properties`: generic inspector controls.
- `ports.inputs` and `ports.outputs`: typed sockets.
- `compiler.class` and `compiler.file`: PHP implementation.
- `nested_graph: true`: gives a node its own nested graph. Use `nested_graph_kind` as `flow` or `view` and optionally set `nested_graph_label`.
- `singleton: true`: permits only one instance per graph.

Supported inspector property types:

```text
text, number, textarea, code, checkbox, select, file,
model_ref, model_method, function_ref, class_ref, class_method,
model_methods, class_methods, breakpoints, permission_list, role_list,
permission_ref, role_ref, view_ref, reusable_view_ref, code_asset_ref,
modal_ref, form_ref
```

A port contains:

```json
{
  "id": "value",
  "label": "Value",
  "type": "mixed",
  "direction": "out",
  "multiple": false
}
```

Common port types are `exec`, `mixed`, `string`, `number`, `boolean`, `array`, `arguments`, `controller`, `view`, `asset`, and `view-child`.

## Compiler.php

Every compiler implements:

```php
ThunderVisualIde\Compiler\NodeCompilerInterface
```

The same compiler may be invoked in different modes through `$context->mode()`:

- `architecture`: contribute files, routes, hooks, metadata, functions, or looks.
- `expression`: return a PHP expression for a data output.
- `statement`: return ordered executable PHP for a flow graph.

### Expression result

```php
return [
    'expression' => '$request->post(\'email\')',
    'outputs' => ['value' => '$request->post(\'email\')'],
];
```

### Normal statement result

```php
return [
    'code' => "message('success', 'Saved');",
    'next_port' => 'exec',
];
```

### Terminal statement

```php
return [
    'code' => "redirect('/login');",
    'terminal' => true,
];
```

### Branch statement

```php
return [
    'control' => 'if',
    'before' => '$value = 1;',
    'condition' => '$value === 1',
    'true_port' => 'true',
    'false_port' => 'false',
    'false_before' => '// Optional code at start of false branch',
];
```

### Architecture contribution

```php
return [
    'routes' => [[
        'method' => 'GET',
        'pattern' => '/page',
        'name' => 'page.index',
    ]],
    'hooks' => [$hookCode],
    'files' => ['looks/main/frontend/page.php' => $html],
];
```

The project compiler understands these contribution keys:

```text
meta, routes, permissions, bootstrap, hooks, functions, looks, files
```

## Useful CompileContext methods

```php
$context->inputExpression($nodeId, 'input_port', 'null');
$context->inputArguments($nodeId);
$context->connectedNodes($nodeId, 'route', 'out', 'main');
$context->compileFlowGraph($graphId);
$context->requireService('request');
$context->requireService('session');
$context->modelVariable($modelNodeId);
$context->modelMethod($modelNodeId, $methodName);
$context->variableForNode($node, 'result');
$context->pluginNamespace();
$context->export($value);
$context->addError('...');
$context->addWarning('...');
```

Request and Session service requirements automatically prepend framework-native instances to the current controller/function flow. Model requirements create the selected model instance.

## Dynamic ports

The editor currently supports these reusable `dynamic_ports` strategies:

- `arguments`: creates `arg_1`, `arg_2`, etc. from `param_count`.
- `array`: creates `item_1`, `item_2`, etc. from `item_count` and `keys`.
- `model_method`: reads method parameters from the selected Model node's `methods_json`.
- `function_call`: reads parameters from the selected Function Definition.
- `class_constructor`: reads typed parameters from `__construct` on the selected Class Definition.
- `class_method`: reads typed parameters and return type from the selected custom class method.
- `image_method`, `request_method`, and `session_method`: read editable method metadata from the node definition.
- `form_schema_data`: exposes one output for every field in a selected visual form.
- `cast`: changes the Cast Value output socket type to match its selected conversion.

Additional strategies can be added in `getPorts()` inside `looks/main/assets/js/editor.js` when a genuinely new port-generation pattern is needed.


## Typed method metadata

Model and custom-class method managers store parameters in this form:

```json
{
  "name": "findByEmail",
  "params": [
    {"name": "email", "type": "string"},
    {"name": "active", "type": "bool"}
  ],
  "return_type": "?object"
}
```

Parameter and return types are optional. Model methods additionally store a `body`. Custom class methods store `visibility`, `static`, and `graph_id`; their bodies are compiled from the referenced execution graph.

Architecture compilers may return a `bootstrap` array for PHP statements that must run before hook registration, such as loading custom class files:

```php
return [
    'bootstrap' => ["require_once plugin_path('classes/Service.php');"],
    'files' => ['classes/Service.php' => $php],
];
```

## Visual View compilers

View Builder nodes use `allowed_graphs: ["view"]` and are compiled with mode `view`. A visual node normally returns:

```php
return ['html' => '<div>...</div>'];
```

Utility nodes that do not use the generated View Builder layout classes may return
`'uses_base_css' => false` so they do not load `thunder-view-components.css` by themselves.

Reusable root nodes may also return:

```php
return [
    'html' => $html,
    'component_name' => 'signup-form',
];
```

Useful context methods are:

```php
$context->compileViewGraph($graphId);
$context->compileViewNode($nodeId, $graphId);
$context->compileViewChildren($nodeId, 'children', $graphId);
```

Parent nodes expose `view-child` output ports. Child nodes expose a matching input. Children are emitted from top to bottom, then left to right, based on their canvas positions.

`ViewNodeSupport` contains responsive width classes, safe static HTML attributes, component-name normalization, option-line parsing, and the generated base CSS.


## Form-schema-aware nodes

The editor supports `view_ref`, `reusable_view_ref`, `code_asset_ref`, `modal_ref` and `form_ref` inspector property types. A node definition can set `"dynamic_ports": "form_schema_data"` to expose one output per field in a selected visual Form. Server-side compilers can call `$context->formSchema($viewNodeId, $formName)` to retrieve the same normalized field and validation metadata.

## Migration graph nodes

A node definition may use `allowed_graphs: ["migration"]`. Migration operation compilers run in `migration` mode and return:

```php
return [
    'up' => '$this->addColumnToTable(...);',
    'down' => '$this->dropColumn(...);',
];
```

Migration graphs begin with `migration.start`. Operations connect with normal `exec` ports. The IDE emits `up()` operations in graph order and `down()` operations in reverse order.

## Custom inspector property types added by the IDE

`dependency_list` displays the Plugin dependency manager. It currently stores a keyed JSON object in the node property.

`validation_rules` displays the visual `Core\Validate` rules manager. It stores the normal field-keyed rules object as formatted JSON, so compilers can continue to decode the property directly. The built-in catalogue is loaded from `validation-rules/rules.json`; unknown/custom rule names remain supported.

For uploaded library folders, prefer the built-in `libraries.package` node. Its archive is stored server-side and its compiler adds extracted files plus optional bootstrap code.

## Optional named result variables

Flow nodes that must store a returned value may expose an optional text property named `result_variable`:

```json
{
  "defaults": {
    "result_variable": ""
  },
  "properties": [
    {
      "name": "result_variable",
      "label": "Result variable",
      "type": "text"
    }
  ]
}
```

In the compiler, use:

```php
$variable = $context->variableForNode($node, 'result');
```

When the property is blank, Thunder Visual IDE retains the automatic node-specific variable. When a user enters `saved_user` or `$saved_user`, the compiler uses `$saved_user`.

Nodes with more than one internal variable may use explicit property names:

```php
$validator = $context->variableForNode(
    $node,
    'validate',
    'validator_variable'
);
```

## Chainable method metadata

Method definitions used by Model Method Call, Class Method Call, and framework method nodes may include a boolean `chainable` field:

```json
{
  "name": "scopeActive",
  "return_type": "self",
  "chainable": true,
  "help": "Adds the active-record condition and returns the current model."
}
```

Use `chainable: true` only when the method returns the same usable service/model/class instance (normally `self`, `static`, or the current class). Chainable methods receive a visual badge and may be followed by another compatible method in the Method Browser Chain Builder. Terminal methods return normal data or a final operation result and should leave `chainable` false.

The Chain Builder is only a construction interface. Clicking **Create Chain in Graph** creates ordinary nodes and edges; projects do not store a separate chain representation.

## JavaScript Flow node packages

JavaScript Flow definitions use `"allowed_graphs": ["javascript"]`. Execution ports use `js-exec`; browser data types begin with `js-`, for example `js-string`, `js-number`, `js-object`, `js-element`, `js-event`, `js-xhr` and `js-progress`. `js-value` is the general browser-value socket.

Expression compilers return an `expression` and may provide an `outputs` map. Statement compilers return `code` and normally `next_port`. Control nodes such as conditions, events and AJAX return a `control` descriptor that `CompileContext` expands into correctly scoped JavaScript branches.

JavaScript nodes must never emit `<script>` tags. The owning View compiler writes the complete nested graph to a dedicated Look JavaScript asset.
