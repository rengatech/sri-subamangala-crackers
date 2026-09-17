<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Price List</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid #000;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 20px;
            /* Moves logo to the left */
            top: 9px;
            width: 80px;
            /* Adjust as needed */
        }

        .mobile {
            position: absolute;
            top: 4px;
            right: 20px;
            /* Moves mobile number to the top-right corner */
            font-size: 16px;
            font-weight: bold;
            color: black;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            /* Adjust spacing */
            /* padding-left:30px;   */
        }

        .sub-heading {
            font-size: 18px;
            font-weight: bold;
            /* padding-left: 30px;  */
        }

        .address {
            font-size: 12px;
            /* text-align: left;  */
            width: 100%;
            /* padding-left: 30px;  */
        }

        .contact {
            font-size: 14px;
            /* text-align: right;  */
            width: 100%;
            padding-right: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #fff;
        }

        .category-heading {
            font-weight: bold;
            text-align: center;
            background-color: #f0f0f0;
        }

        .retail-heading {
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #000;
        }

        .date {
            text-align: right;
            font-weight: bold;
        }

        .logo-right {
            position: absolute;
            right: 20px;
            /* Align right */
            top: 50px;
            /* Adjust spacing from top */
            width: 80px;
            /* Same size as left logo */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if(!empty($settings->logo))
                <img src="{{ public_path('storage/' . $settings->logo) }}" alt="Logo" class="logo">
            @else
                <img src="{{ public_path('assets/img/sri-mangala-crackers/logo.jpeg') }}" alt="Logo" class="logo">
            @endif

            <!-- Mobile number on top-right -->
            <div class="mobile">Mob No : {{ $settings->mobile_number_1 }} </div>
            <div class="company-name">{{ strtoupper($settings->company_name) }}</div>
            <div class="sub-heading">Fireworks Wholesale & Retail Sales</div>
            <div class="address"> {{ $settings->company_address }}<br>
                PH: {{ $settings->mobile_number_1 }}<br>
                Email: {{ $settings->email_id }}<br>
                Website: {{ $settings->website }} </div>
        </div>

        <div style="padding: 8px 12px; text-align: right; font-size: 12px;">
            <span style="font-weight: bold;">Date : {{ date('d-m-Y') }}</span>
        </div>
        <div style="padding: 4px 12px; text-align: center; font-size: 11px; font-weight: bold; color: #cc0000;">
            Note: This price list is valid only for 3 days from the date of generation.
        </div>

        <!-- Product Table -->
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>PRODUCT</th>
                    <th>CONTENT</th>
                    <th>NET RATE</th>
                    <th>REQ QTY</th>
                </tr>
            </thead>
            <tbody>
                <!-- Category Headers -->
                @php
                    $slNo = 1;
                @endphp

                @foreach($categories as $category)
                    <tr>
                        <td colspan="5" class="category-heading">{{ strtoupper($category->category) }}</td>
                    </tr>

                    @foreach($category->products as $product)
                        <tr>
                            <td>{{ $slNo++ }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->unit ?? '' }}</td>
                            <td>{{ number_format($product->price, 2) }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>