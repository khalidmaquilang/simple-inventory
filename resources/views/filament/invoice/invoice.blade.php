<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="{{ public_path('app-invoice/assets/css/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ public_path('app-invoice/assets/fonts/font-awesome/css/font-awesome.min.css') }}">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ public_path('app-invoice/assets/css/style.css') }}">
</head>
<body>

<!-- Invoice 5 start -->
<div class="invoice-6 invoice-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner clearfix">
                    <div class="invoice-info clearfix" id="invoice_wrapper">
                        <div class="invoice-headar">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="invoice-logo">
                                        <!-- logo started -->
                                        @if ($logo)
                                            <div class="logo">
                                                <img src="{{ $logo }}" alt="{{ $companyName }}"/>
                                            </div>
                                        @endif
                                        <!-- logo ended -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-contant">
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h1 class="invoice-name">Invoice</h1>
                                    </div>
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number-inner">
                                            <h2 class="name">Invoice No:</h2>
                                            <p class="mb-0">#{{ $invoiceNumber }}</p>
                                            <p class="mb-0">Invoice Date: <span>{{ $invoiceDate }}</span></p>
                                            <p class="mb-0">Due Date: <span>{{ $dueDate }}</span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number">
                                            <h4 class="inv-title-1">Invoice To</h4>
                                            <h2 class="name mb-10">{{ $buyerName }}</h2>
                                            <p class="invo-addr-1 mb-0">
                                                @if ($buyerEmail) {{ $buyerEmail }}<br /> @endif
                                                @if ($buyerPhoneNumber){{ $buyerPhoneNumber }}<br /> @endif
                                                @if ($buyerAddress){{ $buyerAddress }}<br /> @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number">
                                            <div class="invoice-number-inner">
                                                <h4 class="inv-title-1">Invoice From</h4>
                                                <h2 class="name mb-10">{{ $companyName }}</h2>
                                                <p class="invo-addr-1 mb-0">
                                                    @if ($email) {{ $email }}<br /> @endif
                                                    @if ($phoneNumber) {{ $phoneNumber }}<br /> @endif
                                                    @if ($address) {{ $address }}<br /> @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-center">
                                <div class="order-summary">
                                    <div class="table-outer">
                                        <table class="default-table invoice-table">
                                            <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @foreach($items as $item)
                                                <tr>
                                                    <td>{{ $item->name }}</td>
                                                    <td class="right-align">{{ $item->quantity }}</td>
                                                    <td class="right-align">{{ $item->formatted_unit_cost }}</td>
                                                    <td class="right-align">{{ $item->formatted_total_cost }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="3">Sub Total</td>
                                                <td class="right-align">{{ $subTotal }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Discount</td>
                                                <td class="right-align">{{ $discount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Shipping Fee</td>
                                                <td class="right-align">{{ $shippingFee }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">VAT ({{ $vatPercent }}%)</td>
                                                <td class="right-align">{{ $vat }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Total</td>
                                                <td class="right-align"><strong>{{ $total }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Amount Paid</td>
                                                <td class="right-align">{{ $paidAmount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Due Balance</td>
                                                <td class="right-align"><strong>{{ $remainingAmount }}</strong></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-bottom">
                                <div class="row">
                                    <div class="col-lg-7 col-md-7 col-sm-7">
                                        <div class="terms-conditions mb-30">
                                            <h3 class="inv-title-1 mb-10">Terms & Conditions</h3>
                                            All sales are final. For any queries, please contact {{ $companyName }}.
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                        <div class="payment-method mb-30">
                                            <h3 class="inv-title-1 mb-10">Payment Method</h3>
                                            <ul class="payment-method-list-1 text-14">
                                                <li><strong>{{ $paymentMethod }}</strong></li>
                                                @if ($referenceNumber)<li><strong>Reference Number:</strong> {{ $referenceNumber }}</li> @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invoice 5 end -->

</body>
</html>
