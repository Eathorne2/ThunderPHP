# JavaScript Flows

JavaScript Flow is the browser-behavior graph owned by a normal View or Reusable View. It is deliberately separate from both the server Flow graph and the visual View Builder graph:

- **Flow graph** generates server-side PHP.
- **View Builder** generates HTML and PHP presentation markup.
- **JavaScript Flow** generates browser-side JavaScript.

Open a View or Reusable View in the architecture graph and choose **Open JavaScript Flow** from its inspector or context menu. Each owner receives its own nested JavaScript graph and generated Look asset.

## Generated files

When a JavaScript Flow contains executable behavior, the owning View generates an asset similar to:

```text
looks/main/assets/js/thunder-view-account-view.js
```

The View loads that file with `defer`. Empty JavaScript graphs do not generate or load an asset.

Every rendered View Builder node receives an internal `data-tvi-view-node` marker. JavaScript DOM and Event Listener nodes use that marker when a target View component is selected, so generated IDs do not have to be entered manually.

## Execution entries

A JavaScript graph can contain multiple independent browser entry points:

- **JS Start** runs once when the generated deferred asset executes.
- **Event Listener** starts a branch when an event occurs on a selected View component or CSS selector.
- **AJAX Request** can act as a form-submit entry when **Intercept form submit automatically** is enabled and it has no execution input.

Execution connections use the purple `js-exec` socket family. JavaScript values use the cyan browser-value socket family. They cannot connect to normal PHP execution or data sockets.

## AJAX Request

AJAX Request uses `XMLHttpRequest`, allowing upload progress as well as normal response handling. It supports:

- GET, POST, PUT, PATCH and DELETE
- connected, JSON, FormData or empty request bodies
- connected headers or fallback headers JSON
- text, JSON, Blob, ArrayBuffer and Document responses
- optional text-response JSON parsing
- timeout and credential settings
- automatic form-submit interception

Its execution branches are:

```text
Before Send
Upload Progress
Download Progress
Success
HTTP Error
Network Error
Timeout
Aborted
Complete
```

The request also exposes browser-side values for Response, XHR, Status, Progress and Error. Use those values inside the corresponding callback branch so they remain in the correct JavaScript scope. The **XHR variable name** inspector field defaults to `xhr` and makes that identifier available to JavaScript Code nodes inside every request callback. Each request is compiled in its own block scope, so multiple requests may reuse the same name safely.

`load` is separated into Success and HTTP Error according to the status code. Network Error, Timeout and Aborted are transport-level branches. Complete is emitted from `loadend` and runs after any outcome.

## Progress bars

Place **AJAX Progress Bar** in the owning View Builder graph. It uses semantic form-theme colors, supports optional percentage text and may start hidden.

In JavaScript Flow:

1. Connect AJAX Request **Upload Progress** or **Download Progress** to **Update AJAX Progress**.
2. Connect the AJAX Request **Progress** data output to the update node.
3. Select the intended View progress component in the update node inspector.

When the browser cannot calculate a total, the component switches to an indeterminate animation instead of displaying a false percentage. Multiple progress components can coexist because each update node targets a specific View node marker.

## Event Listener

Event Listener can target:

- a selected component from the owner View graph, or
- a manually entered CSS selector.

An optional descendant selector narrows the target inside the selected component. The event name is free text, allowing standard and custom DOM events. It also supports prevent-default, stop-propagation, capture, once and passive options.

The node exposes the browser Event object, matched element and element value to its callback branch. **Event variable name** controls the callback identifier and defaults to `e`, so custom JavaScript in that branch may use `e`, `event`, or another valid identifier selected by the user.


## Assigning JavaScript variables

**Set JS Variable** can declare a `let`, `const`, or `var`, or assign to an existing variable/property path. Connect the value input, choose the assignment mode, and use its output in later browser-side expressions. Declarations require a simple identifier; assignment mode also accepts paths such as `app.user.name`.

## PHP values in JavaScript

**PHP Value to JS** is the explicit server-to-browser bridge. Enter a PHP expression and a JavaScript variable name. The owning View serializes the PHP value with `json_encode()` into a small bootstrap block before the deferred JavaScript Flow asset loads. PHP tags and trailing semicolons are not required.

## Browser-side values

The initial JavaScript value nodes include:

- JS String, Number, Boolean and Null
- JS Variable and JS Expression
- JS Array and JS Object with dynamic ports
- JS Concatenate
- Form Data
- Query Element

These are browser expressions, not PHP values. Use a JS Expression only for trusted hand-authored JavaScript.

## DOM behavior

The initial DOM statement nodes include:

- Set Content: text, HTML or form-control value
- Class List: add, remove or toggle
- Set Attribute or remove attribute
- Visibility: show, hide or toggle
- Update AJAX Progress

DOM nodes can target a selected View component, a manual selector, or a connected Query Element result where the node supports an element input.

## Custom JavaScript

**JavaScript Code** runs arbitrary browser JavaScript without `<script>` tags. It can expose up to twenty connected input values as named local constants before the custom source.

Keep custom code scoped to the values and elements supplied by the graph. The generated asset already uses strict mode and an ownership guard to prevent the same View flow from registering twice.
