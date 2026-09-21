# Chart Builder

Combines labels and one or more Chart Dataset nodes into a complete Chart.js configuration.

The generated configuration is assigned to a PHP variable and also stored with `set_value()` under **Page value key**. A Chart View node retrieves that key later in the same request.

Use **Advanced Chart Options** for Chart.js options that are not exposed directly. They are merged after the common settings.
