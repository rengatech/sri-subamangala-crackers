<html>

<head>
  <META http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title inertia>{{ $order->customer->name }}</title>
</head>

<body>
  <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width:100%;background:#e9e9e9;padding:50px 0px">
    <tr>
      <td>
        <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;background:#ffffff;padding:0px 25px">
          <tbody>
            <tr>
              <td style="margin:0;padding:0">
                <table border="0" cellpadding="20" cellspacing="0" width="100%" style="background:#ffffff;color:#1a1a1a;line-height:150%;text-align:center;border-bottom:1px solid #e9e9e9;font-family:300 14px &#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif">
                  <tbody>
                    <tr>

                      <td valign="top" align="left" width="100" style="background-color:#ffffff">
                      <span style="text-decoration:underline;">Company Details</span>
                      <p>Company Name :</p>
                      <p>Company Address :</p>
                      <p>Mobile Number :</p>

                      
                      </td>
                      <td valign="top" align="right" width="100" style="background-color:#ffffff">
                      <span style="text-decoration:underline;">Order #{{ $order->id }}</span>
                      <p>{{ $order->created_at->format('d-m-Y') }}</p>

                      
                      </td>
                    </tr>
                  </tbody>
                </table>

                <br>

                <table border="0" cellpadding="" cellspacing="0" width="100%" style="background:#ffffff;color:#000000;line-height:150%;text-align:center;font:300 16px &#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif">
                  <tbody>
                    <tr>
                      <td valign="top" width="100">
                        <h3 style="text-align:left;">Customer Details</h3>
                        <p style="text-align:left;">Name  : <span style="font-size:18px;">{{ $order->customer->name }} </span></p>
                        <p style="text-align:left;">Email : <span style="font-size:18px;">{{ $order->customer->email }}</span></p>
                        <p style="text-align:left;">Mobile Number : <span style="font-size:18px;">{{ $order->customer->mobile_number }}</span></p>
                        <p style="text-align:left;">whatsapp Number: <span style="font-size:18px;">{{ $order->customer->whatsapp_number }}</span></p>
                      </td>
                      <td valign="top" width="100">
                        <h3 style="text-align:center;text-transform:uppercase">Shipping Addres</h3>
                        <p>Address    : <span style="font-size:18px;">{{ $order->address->address }} </span></p>
                        <p>City town  : <span style="font-size:18px;">{{ $order->address->city_town }}</span></p>
                        <p>District   : <span style="font-size:18px;">{{ $order->address->district  }}</span></p>
                        <p>Pincode    : <span style="font-size:18px;">{{ $order->address->pin_code  }}</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <br>
          
                <table align="center" cellspacing="0" cellpadding="6" width="95%" style="border:0;color:#000000;line-height:150%;text-align:left;font:300 14px/30px &#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif;" border=".5px">
                  <thead>
                    <tr style="background:#efefef">
                      <th scope="col" width="30%" style="text-align:left;border:1px solid #eee">Product</th>
                      <th scope="col" width="15%" style="text-align:right;border:1px solid #eee">Quantity</th>
                      <th scope="col" width="20%" style="text-align:right;border:1px solid #eee">Price</th>
                    </tr>
                  </thead>
                  <tbody>
                      
                  @foreach($order->items as $item)
                    <tr width="100%">
                      <td width="30%" style="text-align:left;vertical-align:middle;border-left:1px solid #eee;border-bottom:1px solid #eee;border-right:0;border-top:0;word-wrap:break-word">
                      {{ $item->product->name }}
                      </td>
                      <td width="15%" style="text-align:right;vertical-align:middle;border-left:1px solid #eee;border-bottom:1px solid #eee;border-right:0;border-top:0">
                      {{ $item->quantity }}
                      </td>
                      <td width="20%" style="text-align:right;vertical-align:middle;border-left:1px solid #eee;border-bottom:1px solid #eee;border-right:1px solid #eee;border-top:0"><span>{{ $item->total }}</span></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <br>
                <br>
                <table border="0" cellpadding="20" cellspacing="0" width="100%" style="color:#000000;line-height:150%;text-align:left;font:300 14px &#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif">
                  <tbody>
                    <tr>
                      <td valign="top">
                        <h4 style="font-size:24px;margin:0;padding:0;margin-bottom:10px; text-align:right;">Summary</h4>
                        <p style="margin:0;margin-bottom:10px;padding:0; text-align:right;"><strong>Net Total :</strong> {{ $order['net_total'] }}</p>
                        <p style="margin:0;margin-bottom:10px;padding:0; text-align:right;"><strong>Discount Total :</strong> {{ $order['discount_total'] }}</p>
                        <p style="margin:0;margin-bottom:10px;padding:0; text-align:right;"><strong>Sub Total :</strong>{{ $order['sub_total'] }}</p>
                      </td>
                     
                    </tr>
                  </tbody>
                </table>
               
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>