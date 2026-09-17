<html>

<head>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Bulk download</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 10px;
            background: #e5e7eb;
            font-size: 12px;
            color: #1f2937;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d1d5db;
        }

        .text-red { color: #ff0000; }
        .label { font-weight: 600; }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items th {
            background: #000;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 6px;
            text-align: center;
            border-right: 1px solid #333;
        }

        table.items th:last-child { border-right: none; }

        table.items td {
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            padding: 4px 6px;
            font-size: 11px;
            text-align: center;
        }

        table.items td:last-child { border-right: none; }
        table.items td.left { text-align: left; }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            border-top: 1px solid #d1d5db;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .footer-note {
            padding: 8px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            margin-top: 40px;
        }
    </style>
</head>

<body>

@foreach ($orderData as $index => $order)
    @if ($index > 0)
        <div style="page-break-before: always;"></div>
    @endif

    <div class="container">
        {{-- Greeting --}}
        <div style="border-bottom: 1px solid #d1d5db; padding: 6px 8px; text-align: center;">
            <p style="margin: 0; font-weight: 700; font-size: 10px;">Dear Customer,</p>
            <p style="margin: 2px 0; font-size: 9px; font-weight: 600;">Thank you for choosing SriMadhuCrackers Crackers. Your order has been placed successfully. Kindly make the payment in mentioned Bank Accounts in our</p>
            <p style="margin: 0; font-weight: 700; font-size: 10px;">website www.SriMadhuCrackers.com</p>
        </div>

        {{-- Address & Order Number --}}
        <table cellpadding="0" cellspacing="0" width="100%" style="border-bottom: 1px solid #d1d5db;">
            <tr>
                <td style="width: 55%; padding: 8px; border-right: 1px solid #d1d5db; vertical-align: top;">
                    <p style="margin: 0 0 2px; font-weight: 700; font-size: 12px;">To Address :</p>
                    <p style="margin: 1px 0; font-size: 11px;">{{ $order['customer']['name'] }}</p>
                    @if(isset($order['customer']['address']))
                        <p style="margin: 1px 0; font-size: 11px;">{{ $order['customer']['address']['address'] }}</p>
                        <p style="margin: 1px 0; font-size: 11px;">{{ $order['customer']['address']['city_town'] }}</p>
                    @endif
                    <p style="margin: 1px 0; font-size: 11px;">{{ $order['customer']['mobile_number'] }}</p>
                </td>
                <td style="padding: 8px; text-align: center; vertical-align: middle;">
                    <p style="margin: 0; font-weight: 700; font-size: 13px; color: #ff0000;">Order Number : {{ $order['id'] }}</p>
                </td>
            </tr>
        </table>

        {{-- Date & Net Amount --}}
        <table cellpadding="0" cellspacing="0" width="100%" style="border-bottom: 1px solid #d1d5db;">
            <tr>
                <td style="width: 55%; padding: 6px 8px; border-right: 1px solid #d1d5db;">
                    <p style="margin: 0; font-weight: 700; font-size: 12px; color: #ff0000;">Order Date: {{ date('F d, Y', strtotime($order['created_at'])) }}</p>
                </td>
                <td style="padding: 6px 8px; text-align: center;">
                    <p style="margin: 0; font-weight: 700; font-size: 12px; color: #ff0000;">Net Amount : ₹{{ $order['net_total'] }}</p>
                </td>
            </tr>
        </table>

        {{-- Items Table --}}
        <table class="items" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 35px;">S.No</th>
                    <th>Product Name</th>
                    <th style="width: 45px;">Qty</th>
                    <th style="width: 60px;">Rate</th>
                    <th style="width: 55px;">Discount</th>
                    <th style="width: 65px;">Offer Rate</th>
                    <th style="width: 70px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order['items'] as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="left">{{ $item['product']['name'] ?? '' }}</td>
                    <td>{{ $item['quantity'] ?? '' }}</td>
                    <td>₹{{ isset($item['product']['price']) && isset($global_discount)
                        ? round($item['product']['price'] / (1 - $global_discount * (1 / 100)))
                        : '' }}</td>
                    <td>₹{{ isset($item['product']['price']) && isset($global_discount)
                        ? round($item['product']['price'] / (1 - $global_discount * (1 / 100)) - $item['product']['price'])
                        : '' }}</td>
                    <td>₹{{ isset($item['product']['price']) ? round($item['product']['price']) : '' }}</td>
                    <td>₹{{ $item['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Order Summary --}}
        <div style="padding: 4px 0; text-align: center; font-weight: 700; font-size: 12px;">Order Estimation Summary</div>

        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align: right; width: 85%;">No. of. Products</td>
                <td style="text-align: center;">{{ $order['product_count'] }}</td>
            </tr>
            <tr>
                <td style="text-align: right;">No. of. Items</td>
                <td style="text-align: center;">{{ $order['total_items'] }}</td>
            </tr>
            <tr>
                <td style="text-align: right;">Total Amount</td>
                <td style="text-align: center;">{{ $order['sub_total'] }}</td>
            </tr>
            <tr>
                <td style="text-align: right;">Discount</td>
                <td style="text-align: center;">{{ $order['discount_total'] }}</td>
            </tr>
            <tr>
                <td style="text-align: right;">Net Total</td>
                <td style="text-align: center;">{{ $order['net_total'] }}</td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        <p style="margin: 0;">Please note that there may be delays pertaining to shipping batches. You will be informed before we ship your order.</p>
        <p style="margin: 2px 0 0;">Kindly await our confirmation on processing and shipping status. For further queries, kindly reach out to our customer care</p>
    </div>
@endforeach
</body>

</html>
