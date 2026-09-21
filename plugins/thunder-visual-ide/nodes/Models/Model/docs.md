# Model

Defines a ThunderPHP model. Custom methods become available to **Model Method Call** nodes.

Model methods may use a PHP body or a nested execution graph. Select the implementation in the visual Methods manager. Method parameters are ordinary PHP variables inside the flow.

Each method also accepts **Help information** written in Markdown. Add its purpose, important behavior, return details, and a fenced PHP example. Model Method Call nodes display this material in a searchable two-column Method Browser.

Method parameters may define an optional raw PHP default value such as `null`, `[]`, `false`, `10`, or `'draft'`. Call nodes omit trailing unconnected optional parameters so PHP uses the declared defaults.

The Query Builder starter `createRecord` and `updateById` methods pass `$data` through `filterInsertData()` and `filterUpdateData()` before writing to the database.
