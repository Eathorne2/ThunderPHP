# Chart View

Renders a Chart.js canvas from a Chart Builder page-value key.

Leave **Load Chart.js source** disabled when the application or look already bundles Chart.js. Enable it and enter a script URL only when the current page does not already load Chart.js.

The node destroys an existing chart instance on the same canvas before rendering again, which makes it safer in AJAX-updated views and reusable components.

The chart keeps its required internal chart element. An additional outer wrapper is generated only when **Wrapper CSS classes** or **Wrapper inline styles** is provided. Responsive width settings apply only to that optional outer wrapper.
