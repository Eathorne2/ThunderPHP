# Chart.js Nodes

Thunder Visual IDE separates chart data preparation from View rendering so database logic stays in the flow graph and HTML/JavaScript stays in the View graph.

## Nodes

### Array Column

Extracts one property from each query result row and returns a plain array. It supports arrays, objects, `Traversable` results, and dot-notation paths such as `summary.total`.

Typical uses:

- Extract `month` into chart labels.
- Extract `total` into dataset values.
- Extract nested values from reporting queries.

### Chart Dataset

Creates one Chart.js dataset array. Connect the values from Array Column or another array-producing node to **Data**.

The node exposes common options such as label, colors, border width, fill, tension, hidden state, point radius, stack, Y-axis ID, and an optional dataset-specific chart type. Use **Advanced Dataset Options** for settings not exposed directly.

### Chart Builder

Combines labels and one or more Chart Dataset outputs into a complete configuration. Use the inspector's **Add Dataset** and **Remove Dataset** controls to create dynamic dataset ports.

The builder stores the generated array with:

```php
set_value('chart_key', $chart_config);
```

The **Page value key** must match the Chart View node's **Chart data key**.

### Chart View

Reads the Chart Builder value with `get_value()`, renders a canvas, and initializes Chart.js. The IDE bundles the standalone Chart.js 4.5.0 UMD build. When **Custom Chart.js path** is empty, the compiler copies that exact library into the generated plugin as `assets/js/chart.min.js` and loads it with `plugin_http_path()`.

Set **Custom Chart.js path** only when the generated page should use another local or remote build. Repeated rendering safely destroys an existing chart attached to the same canvas before creating the replacement.

## Example workflow

Flow graph:

```text
Query result
  ├─ Array Column: month ────────────────┐
  └─ Array Column: total → Chart Dataset ├─ Chart Builder
                                          └─ set_value('monthly_sales')
```

View graph:

```text
Chart View
  Chart data key: monthly_sales
```

## Multiple datasets

Create one Chart Dataset node per series, then connect each to a separate dynamic Dataset input on Chart Builder. All datasets share the builder's labels unless a Chart.js chart type accepts point objects directly, such as scatter or bubble data.

## Advanced options

The common controls are intended for routine charts. Array nodes can supply additional Chart.js configuration through:

- **Advanced Dataset Options** on Chart Dataset.
- **Advanced Chart Options** on Chart Builder.

Advanced arrays are merged after the node's common defaults, so advanced values take precedence.

## Chart.js library loading

`Chart View` has an optional **Custom Chart.js path** field.

- Leave it empty to use the IDE-bundled Chart.js file. The compiler copies it to `assets/js/chart.min.js` in the generated plugin and loads it below the chart before initialization.
- Enter a local or remote path to use that source instead. In that case the compiler does not add the bundled Chart.js file solely for that Chart View.
- Multiple Chart View nodes share one loader promise, so the library is not initialized repeatedly.
