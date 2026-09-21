# Display Data

Compiles `dd($data);` using the value connected to **Data**.

The node keeps a **Next** execution output because ThunderPHP installations may implement `dd()` as a display helper rather than a terminating helper. If your implementation terminates execution, downstream nodes will naturally not run.
