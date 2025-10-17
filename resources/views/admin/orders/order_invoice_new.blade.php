<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Invoice</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                padding: 0;
                background-color: #f8f9fa;
                text-align: center;
            }
            .invoice-container {
                max-width: 800px;
                margin: auto;
                background: #fff;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }
            .header {
                text-align: center;
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            .header img {
                max-width: 150px;
            }
            .header h2 {
                color: #007bff;
                margin: 10px 0 0;
            }
            .info-section {
                display: flex;
                justify-content: space-between;
                padding: 15px 0;
                border-bottom: 1px solid #ddd;
            }
            .info-section div {
                text-align: left;
            }
            .info-section p {
                margin: 5px 0;
            }
            .table-container {
                width: 100%;
                margin-top: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
            th {
                background-color: #007bff;
                color: #fff;
            }
            .totals {
                margin-top: 20px;
                text-align: right;
            }
            .totals p {
                font-size: 18px;
                font-weight: bold;
            }
            .footer {
                text-align: center;
                font-size: 12px;
                color: #777;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <div class="header">
                <img src="{{ asset('front/images/logo.png') }}" alt="Company Logo">
                <h2>Order Invoice</h2>
            </div>
            
            <div class="info-section">
                <div>
                    <p><strong>Deliver To:</strong></p>
                    <p>{{$orderDetails['order_address']['shipping_name'] }}</p>
                    <p>{{$orderDetails['order_address']['shipping_address'] }}</p>
                    <p>{{$orderDetails['order_address']['shipping_city'] }}, {{$orderDetails['order_address']['shipping_state'] }}, {{$orderDetails['order_address']['shipping_postcode'] }}</p>
                    <p><strong>Mobile:</strong> {{$orderDetails['order_address']['shipping_mobile'] }}</p>
                </div>
                <div>
                    <p><strong>Order ID:</strong> {{$orderDetails['id']}}</p>
                    <p><strong>Date:</strong> {{date('d F Y h:ia', strtotime($orderDetails['created_at']))}}</p>
                    <p><strong>Payment Method:</strong> {{ ($orderDetails['payment_method'] == "COD") ? "Cash on Delivery" : 'Prepaid' }}</p>
                </div>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>MRP (₹)</th>
                            <th>Discount (₹)</th>
                            <th>Price (₹)</th>
                            <th>Quantity</th>
                            <th>Subtotal (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderDetails['order_products'] as $product)
                        <tr>
                            <td>{{$product['product_name']}} ({{$product['product_code']}})</td>
                            <td>{{$product['product_size']}}</td>
                            <td>{{round($product['mrp'],2)}}</td>
                            <td>{{round($product['discount'],2)}}</td>
                            <td>{{round($product['product_price'],2)}}</td>
                            <td>{{$product['product_qty']}}</td>
                            <td>{{round($product['sub_total'],2)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="totals">
                <p>Coupon Discount: ₹{{round($orderDetails['coupon_discount'],2)}}</p>
                <p>Shipping Charges: ₹{{round($orderDetails['shipping_charges'],2)}}</p>
                <p>Taxes: ₹{{round($orderDetails['taxes'],2)}}</p>
                <p><strong>Grand Total: ₹{{round($orderDetails['grand_total'],2)}}</strong></p>
            </div>
            
            <div class="footer">
                <p>This is a computer-generated invoice and does not require a signature.</p>
                <p>If you receive an open or tampered package at the time of delivery, please do not accept it.</p>
            </div>
        </div>
    </body>
</html>