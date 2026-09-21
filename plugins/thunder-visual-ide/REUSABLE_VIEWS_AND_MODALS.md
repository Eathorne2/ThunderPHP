# Reusable Views and Modals

## Reusable View workflow

A **Reusable View** is an architecture node with its own nested View Builder graph. It compiles a normal PHP partial inside the connected Look, but it does not register a route or action hook.

1. Add **Reusable View** to the architecture graph.
2. Connect it to the appropriate Look.
3. Choose a partial path such as `frontend/includes/account-card.php`.
4. Open its nested graph and build the partial with normal View Builder nodes.
5. In another View graph, add **Render Reusable View** and select the reusable view from the dropdown.

The renderer emits:

```php
<?php require current_look('frontend/includes/account-card.php'); ?>
```

The partial runs in the current View scope, so page values and extracted variables remain available. The reusable view and the page that renders it should use the same Look.

## Modal workflow

A **Modal** is a View container. Attach its dialog contents through **Modal Content** exactly as you would attach children to a normal Container.

A **Modal Trigger** contains a small HTML fragment. Its first element receives the action attributes. Connect **Modal Target** to the Modal's **Triggers** input. Any number of triggers can target the same Modal.

The trigger action can be:

- `open`
- `close`
- `toggle`

Triggers may be normal View children, root items, or named components placed with a `<tvi-component>` marker. A close trigger may therefore be placed inside the modal content while still targeting its containing Modal through the separate behavior connection.

Modal options include backdrop click closing, Escape closing, body-scroll locking, fixed or absolute positioning, dialog alignment, z-index, backdrop color, width and maximum height.

The generated runtime also exposes:

```js
ThunderModal.open('account-modal');
ThunderModal.close('account-modal');
ThunderModal.toggle('account-modal');
ThunderModal.refresh();
```

The runtime uses delegated click handling, restores focus to the opener, traps keyboard focus inside the active modal, updates `aria-expanded`, and supports multiple modals on one page.


## Dynamic initially-open state

The Modal checkbox remains the fallback state. Connecting a value to **Initially Open** overrides it for the current request. Use **Page Value Get** when a controller flow has stored a request-lifetime flag with `set_value()`, or **Variable** when the View already receives an extracted variable.
