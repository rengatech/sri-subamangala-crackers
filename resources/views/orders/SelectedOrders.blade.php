<html>

<head>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <h1>Selected Orders Report</h1>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
            </tr>
        </thead>
        @if ($selectedOrders->count() > 0)
        <tbody>
            @php
                $productQuantities = [];    

                foreach ($selectedOrders as $order) {
                    foreach ($order->items as $orderItem) {
                        $productName = $orderItem->product->name;
                        $quantity = $orderItem->quantity;

                        if (!isset($productQuantities[$productName])) {
                            $productQuantities[$productName] = 0;
                        }

                        $productQuantities[$productName] += $quantity;
                    }
                }
            @endphp

            @foreach ($productQuantities as $productName => $quantity)
                <tr>
                    <td>{{ $productName }}</td>
                    <td>{{ $quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    @else
        <tbody>
            <tr>
                <td colspan="2">No selected orders found.</td>
            </tr>
        </tbody>
    @endif

    </table>
</body>

</html>
