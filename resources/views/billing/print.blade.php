<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bill #{{ $billing->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            text-align: center;
        }

        /* Center text generically */
        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-block: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .brand-logo img {
            max-width: 180px;
            height: auto;
        }

        .company-details {
            text-align: right;
        }

        .company-details h3 {
            margin: 0;
            padding: 0;
            font-size: 24px;
            color: #b11f24;
        }

        h1,
        p {
            margin: 5px 0;
        }

        h1.invoice-title {
            font-size: 32px;
            text-transform: uppercase;
            margin: 0;
            padding-bottom: 15px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <div style="display: flex; justify-content: flex-end;">
            <button class="no-print" onclick="window.print()" style="padding: 10px 20px; cursor:pointer;">Print
                Bill</button>
        </div>

        <!-- INVOICE TEXT CENTER -->
        <div style="text-align: center; margin-top: 10px;">
            <h1 class="invoice-title">Invoice</h1>
        </div>

        <!-- BRAND LOGO LEFT, COMPANY NAME/MOBILE/ADDRESS RIGHT -->
        <div class="header-section">
            <div class="brand-logo">
                <img src="{{ asset('assets/img/sri-mangala-crackers/logo.jpeg') }}" alt="Brand Logo">
            </div>
            <div class="company-details">
                <h3>{{ $settings->company_name ?? 'Sri Subamangala Crackers' }}</h3>
                <p><strong>Mobile:</strong> {{ $settings->mobile_number_1 }}</p>
                @if($settings->company_address)
                    <p style="white-space: pre-wrap; font-size: 14px; max-width: 250px; margin-left: auto;">
                        {{ $settings->company_address }}</p>
                @endif
            </div>
        </div>

        <div style="margin-top: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <p><strong>Bill ID:</strong> #{{ $billing->id }}</p>
            <p><strong>Customer:</strong> {{ $billing->customer_name }}</p>
            @if($billing->mobile_number)
                <p><strong>Mobile:</strong> {{ $billing->mobile_number }}</p>
            @endif
            @if($billing->address)
                <p><strong>Address:</strong> {{ $billing->address }}</p>
            @endif
            <p><strong>Date:</strong> {{ $billing->created_at->format('d-m-Y h:i A') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="width: 80px;">Quantity</th>
                    <th style="width: 120px;">Price</th>
                    <th style="width: 120px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billing->items as $item)
                    <tr>
                        <td>{{ $item->product_name ?? 'Item' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Sub Total</th>
                    <th class="text-right">₹{{ number_format($billing->sub_total, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Discount</th>
                    <th class="text-right">₹{{ number_format($billing->discount, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right" style="font-size: 18px;">Net Amount</th>
                    <th class="text-right" style="font-size: 18px; color: #b11f24;">
                        ₹{{ number_format($billing->net_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>