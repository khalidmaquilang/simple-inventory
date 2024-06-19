<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Invoice</title>

    <!-- Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased dark:bg-black dark:text-white/50">

<!-- Invoice -->
<div class="max-w-[85rem] px-4 sm:px-6 lg:px-8 mx-auto my-4 sm:my-10">
    <div class="sm:w-11/12 lg:w-3/4 mx-auto">
        <!-- Card -->
        <div class="flex flex-col p-4 sm:p-5 bg-white dark:bg-neutral-800">
            <!-- Grid -->
            <div class="flex justify-between">
                <div>
                    <img src="{{ $logo }}" alt="{{ $companyName }}" class="h-26 w-26"/>
                    <h1 class="mt-2 text-lg md:text-xl font-semibold text-blue-600 dark:text-white">{{ $companyName }}</h1>
                </div>
                <!-- Col -->

                <div class="text-end">
                    <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-neutral-200">Invoice #</h2>
                    <span class="mt-1 block text-gray-500 dark:text-neutral-500">{{ $invoiceNumber }}</span>

                    <address class="mt-4 not-italic text-gray-800 dark:text-neutral-200">
                        {{ $email }}<br>
                        {{ $phoneNumber }}<br>
                        {{ $address }}<br>
                    </address>
                </div>
                <!-- Col -->
            </div>
            <!-- End Grid -->

            <!-- Grid -->
            <div class="mt-8 grid sm:grid-cols-2 gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">Bill to:</h3>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">{{ $buyerName }}</h3>
                    <address class="mt-2 not-italic text-gray-500 dark:text-neutral-500">
                        {{ $buyerEmail }}<br>
                        {{ $buyerPhoneNumber }}<br>
                        {{ $buyerAddress }}<br>
                    </address>
                </div>
                <!-- Col -->

                <div class="sm:text-end space-y-2">
                    <!-- Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Invoice date:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $invoiceDate }}</dd>
                        </dl>
                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Due date:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $dueDate }}</dd>
                        </dl>
                    </div>
                    <!-- End Grid -->
                </div>
                <!-- Col -->
            </div>
            <!-- End Grid -->

            <!-- Table -->
            <div class="mt-6">
                <div class="border border-gray-200 p-4 rounded-lg space-y-4 dark:border-neutral-700">
                    <div class="hidden sm:grid sm:grid-cols-5">
                        <div class="sm:col-span-2 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                            Item
                        </div>
                        <div class="text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Qty
                        </div>
                        <div class="text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Price
                        </div>
                        <div class="text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Amount
                        </div>
                    </div>

                    <div class="hidden sm:block border-b border-gray-200 dark:border-neutral-700"></div>

                    @foreach($items as $item)
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                            <div class="col-span-full sm:col-span-2">
                                <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Item</h5>
                                <p class="font-medium text-gray-800 dark:text-neutral-200">{{ $item->name }}</p>
                            </div>
                            <div>
                                <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Qty</h5>
                                <p class="text-gray-800 dark:text-neutral-200">{{ $item->quantity }}</p>
                            </div>
                            <div>
                                <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Price</h5>
                                <p class="text-gray-800 dark:text-neutral-200">{{ $item->formatted_unit_cost }}</p>
                            </div>
                            <div>
                                <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Amount</h5>
                                <p class="sm:text-end text-gray-800 dark:text-neutral-200">{{ $item->formatted_total_cost }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- End Table -->

            <!-- Flex -->
            <div class="mt-8 flex sm:justify-end">
                <div class="w-full max-w-2xl sm:text-end space-y-2">
                    <!-- Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Subtotal:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $subTotal }}</dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Discount:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $discount }}</dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Shipping Fee:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $shippingFee }}</dd>
                        </dl>


                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">VAT:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $vat }}%</dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Total:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $total }}</dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Amount paid:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $paidAmount }}</dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3">
                            <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">Due balance:</dt>
                            <dd class="col-span-2 text-gray-500 dark:text-neutral-500">{{ $remainingAmount }}</dd>
                        </dl>
                    </div>
                    <!-- End Grid -->
                </div>
            </div>
            <!-- End Flex -->

            <div class="mt-8 sm:mt-12">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">Thank you!</h4>
                <p class="text-gray-500 dark:text-neutral-500">If you have any questions concerning this invoice, use
                    the following contact information:</p>
                <div class="mt-2">
                    <p class="block text-sm font-medium text-gray-800 dark:text-neutral-200">{{ config('app.url') }}</p>
                    <p class="block text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $superCompany->phone }}</p>
                </div>
            </div>

            <p class="mt-5 text-sm text-gray-500 dark:text-neutral-500">© 2024 {{ config('app.name') }}.</p>
        </div>
        <!-- End Card -->
    </div>
</div>
<!-- End Invoice -->
</body>
</html>
