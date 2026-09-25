<html>

<head>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ $customer['name'] }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            font-size: 13px;
            color: #1f2937;
        }

        .container {
            width: 100%;
            background: #fff;
            border: 1px solid #d1d5db;
        }

        /* Header row */
        .header {
            width: 100%;
            border-bottom: 1px solid #d1d5db;
        }

        .header td {
            vertical-align: top;
            padding: 10px;
            border-right: 1px solid #d1d5db;
        }

        .header td:last-child {
            border-right: none;
        }

        .header img {
            max-width: 120px;
            height: auto;
        }

        /* Customer info */
        .customer-info {
            padding: 10px 12px;
        }

        .customer-info h3 {
            font-size: 15px;
            font-weight: 600;
            margin: 0 0 4px;
        }

        .customer-info p {
            margin: 2px 0;
            font-size: 13px;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #d1d5db;
        }

        .items-table th {
            background: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            padding: 6px 8px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
        }

        .items-table th:last-child {
            border-right: none;
        }

        .items-table td {
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            padding: 5px 8px;
            font-size: 12px;
            text-align: center;
        }

        .items-table td:last-child {
            border-right: none;
        }

        .items-table td.product-name {
            text-align: left;
        }

        /* Total row */
        .total-row td {
            font-size: 16px;
            font-weight: 700;
            border: 1px solid #d1d5db;
            padding: 8px;
            border-bottom: none;
        }

        /* Footer note */
        .footer {
            padding: 8px;
            text-align: center;
            font-size: 13px;
        }

        .label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <table class="header" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="width: 140px;">
                    <img src="{{ public_path('assets/img/sri-mangala-crackers/logo.jpeg') }}" alt="Logo">
                </td>
                <td style="width: 160px;">
                    <h2 style="font-size: 16px; margin: 0 0 4px;"><span class="label">Order Id :</span> {{ $id }}</h2>
                    <p style="margin: 0;"><span class="label">Date :</span> {{ date("d-m-Y", strtotime($created_at)) }}
                    </p>
                </td>
                <td>
                    <h3 style="font-size: 14px; font-weight: 700; text-decoration: underline; margin: 0 0 6px;">Company
                        Details</h3>
                    <p style="margin: 2px 0; font-size: 12px;">{{ $Company_name }}</p>
                    <p style="margin: 2px 0; font-size: 12px;">{{ $Company_address }}</p>
                    <p style="margin: 2px 0; font-size: 12px;">{{ $mobile_number_1 }}</p>
                </td>
            </tr>
        </table>

        <!-- Customer Info -->
        <div class="customer-info">
            <h3>From</h3>
            <p><span class="label">Name :</span> {{ $customer['name'] }}</p>
            <p><span class="label">Mobile Number :</span> {{ $customer['mobile_number'] }}</p>
            <p><span class="label">WhatsApp Number :</span> {{ $customer['whatsapp_number'] }}</p>
            <p><span class="label">City :</span> {{ $address['city_town'] }}</p>
            <p><span class="label">Address :</span> {{ $address['address'] }}</p>
        </div>

        <!-- Items Table -->
        <table class="items-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 40px;">S.No</th>
                    <th style="width: 200px;">Product Name</th>
                    <th style="width: 60px;">Qty</th>
                    <th style="width: 90px;">Net Rate</th>
                    <th style="width: 90px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $serialNumber = 1; @endphp
                @foreach($items as $item)
                    <tr>
                        <td>{{ $serialNumber }}</td>
                        <td class="product-name">{{ $item['product']['name'] ?? 'N/A' }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        @php
                            $effective_discount = (isset($item['product']['category']) && isset($item['product']['category']['has_discount']) && $item['product']['category']['has_discount']) ? $global_discount : 0;
                        @endphp
                        <td>{{ $item['product'] ? round($item['product']['price'] - round($item['product']['price'] * $effective_discount / 100)) : 0 }}
                        </td>
                        <td>{{ $item['total'] }}</td>
                    </tr>
                    @php $serialNumber++; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; border: 1px solid #d1d5db;">Total :</td>
                    <td style="border: 1px solid #d1d5db;">₹ {{ $net_total }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p><span class="label">Note:</span> This Estimate is valid for 3 days</p>
        </div>
    </div>
</body>

</html>