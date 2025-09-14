<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->code }}</title>
    <link rel="stylesheet" href="{{ asset('css/tailwind.min.css') }}">
</head>

<body>
    <div class="my-12">
        <div
            id="header"
            class="flex justify-between items-center my-9 mx-5">
            <div class="">
                <img
                    class="h-32 w-auto"
                    src="https://cdn.irwandandka.my.id/irwandev/company/f95554b0-91f1-403c-84f2-a0f29f386262.jpeg"
                    alt="Company Logo">
            </div>
            <div class="flex flex-col items-end text-right">
                <h1 class="text-gray-800 text-3xl font-normal mb-5">
                    INVOICE
                </h1>

                <h4 class="text-gray-700 text-xl font-normal">ACE Tours & Travel Pte Ltd</h4>

                <p class="text-gray-600">
                    133 New Bridge Road, #19-03/04/05 Chinatown Point, Singapore 059413
                </p>

                <p class="text-gray-600">
                    License Number: TA01284
                </p>

                <p class="text-gray-600">
                    UEN: 200305848E
                </p>

                <p class="text-gray-600">
                    (65) 6438 2811
                </p>

                <p class="text-gray-600">
                    book@acetours.com.sg
                </p>
            </div>
        </div>

        <div
            id="customer-information"
            class="flex flex-row justify-between mx-5 mb-6">
            <div class="">
                <h2 class="text-gray-800 text-xl font-normal">
                    Bill To
                </h2>

                <p class="text-gray-600">
                    {{ $transaction->customer_name}}
                </p>

                <p class="text-gray-600">
                    {{ $transaction->customer_email }}
                </p>

                <p class="text-gray-600">
                    {{ $transaction->customer_phone }}
                </p>

                <p class="text-gray-600">
                    {{ $transaction->address }}
                </p>
            </div>
            <div>
                <div class="flex flex-row justify-between gap-24">
                    <span class="text-gray-600 font-normal">Invoice: </span>
                    <span class="text-gray-800 font-normal">{{ $transaction->code }}</span>
                </div>
                <div class="flex flex-row justify-between">
                    <span class="text-gray-600 font-normal">Date: </span>
                    <span class="text-gray-800 font-normal">{{ $transaction->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex flex-row justify-between">
                    <span class="text-gray-600 font-normal">Due Date: </span>
                    <span class="text-gray-800 font-normal">{{ $transaction->created_at->addDays(1)->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <div class="mb-6 mx-5">
            <table class="table w-full border-collapse border border-gray-300">
                <thead class="bg-gray-400">
                    <tr>
                        <th class="text-left px-4 py-2 border border-gray-300">Description</th>
                        <th class="text-right px-4 py-2 border border-gray-300">Unit Price</th>
                        <th class="text-right px-4 py-2 border border-gray-300">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left font-bold px-4 py-2">
                            {{ $transaction->transactionDetails[0]->product->name }}
                        </td>
                        <td class="></td>
                        <td class="></td>
                    </tr>
                    @foreach ($transaction->transactionDetails as $transactionDetail)
                    <tr>
                        <td class="text-left px-4 py-2">
                            {{ $transactionDetail->productDetail->name_en }}
                        </td>
                        <td class="text-right px-4 py-2">
                            @if ($transactionDetail->quantity_adult > 0)
                            <label>
                                {{ $transaction->currency->symbol }}
                                {{ number_format($transactionDetail->sales_adult, 2) }} (Adult x {{ $transactionDetail->quantity_adult }})
                            </label>
                            @endif
                            @if ($transactionDetail->quantity_child > 0)
                            <label class="block mt-2">
                                {{ $transaction->currency->symbol }}
                                {{ number_format($transactionDetail->sales_child, 2) }} (Child x {{ $transactionDetail->quantity_child }})
                            </label>
                            @endif
                            @if ($transactionDetail->quantity_infant > 0)
                            <label class="block mt-2">
                                {{ $transaction->currency->symbol }}
                                {{ number_format($transactionDetail->sales_infant, 2) }} (Infant x {{ $transactionDetail->quantity_infant }})
                            </label>
                            @endif
                        </td>
                        <td class="text-right px-4 py-2">
                            {{ $transaction->currency->symbol }}
                            {{ number_format($transactionDetail->sales_total, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            <div class="flex flex-row justify-end mx-5">
                <table class="table w-1/3">
                    <tbody>
                        <tr class="">
                            <td class="text-left px-4 py-2 font-bold">Subtotal</td>
                            <td class="text-right px-4 py-2">
                                {{ $transaction->currency->symbol }} {{ number_format($transaction->total_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left px-4 py-2 font-bold">Tax ({{ $transaction->tax_percentage }}%)</td>
                            <td class="text-right px-4 py-2">
                                {{ $transaction->currency->symbol }} {{ number_format(20, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left px-4 py-2 font-bold border-b-2 border-gray-800">Total ({{ $transaction->currency->symbol }})</td>
                            <td class="text-right px-4 py-2 font-bold border-b-2 border-gray-800">
                                {{ $transaction->currency->symbol }} {{ number_format($transaction->total_amount + 20, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left px-4 py-2 font-bold">Total Due</td>
                            <td class="text-right px-4 py-2">
                                {{ $transaction->currency->symbol }} {{ number_format($transaction->total_amount + 20, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>