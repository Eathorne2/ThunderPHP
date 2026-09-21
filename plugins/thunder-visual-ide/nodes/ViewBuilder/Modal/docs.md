# Modal

Creates a hidden modal overlay. Attach normal View Builder nodes to **Modal Content**. Connect any number of **Modal Trigger** nodes to the **Triggers** input.

Options control backdrop closing, Escape closing, page-scroll locking, overlay positioning, dialog alignment, z-index, backdrop color and dialog sizing.

The generated page also exposes:

```js
ThunderModal.open('modal-id');
ThunderModal.close('modal-id');
ThunderModal.toggle('modal-id');
```


## Dynamic initial state

The **Initially Open** checkbox is the fallback. Connect a Boolean, Variable, or Page Value Get node to the **Initially Open** input to decide the initial state at request time.

A common refresh workflow is:

1. In the controller flow, call `set_value('open_modal', true)` only after the action that should reopen the dialog.
2. In the View graph, add **Page Value Get** with key `open_modal`.
3. Connect its output to the Modal's **Initially Open** port.

On requests where that value is missing or false, the modal starts closed.
