<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>E-Ticket #{{ $transaction->code }}</title>
    <link rel="stylesheet" href="{{ asset('css/tailwind.min.css') }}">
</head>

<body>
    <div class="my-12 mx-auto max-w-4xl p-5">
        {{-- Header --}}
        <div
            id="header"
            class="flex justify-between items-start my-9">
            <div class="">
                <img
                    class="h-32 w-auto"
                    src="https://cdn.irwandandka.my.id/irwandev/company/f95554b0-91f1-403c-84f2-a0f29f386262.jpeg"
                    alt="Company Logo">
            </div>
            <div class="flex flex-col items-end text-right">
                <h1 class="text-gray-800 text-3xl font-normal mb-3">
                    E-TICKET
                </h1>

                <h2 class="text-gray-700 text-2xl font-bold">
                    {{ $transaction->product->name }}
                </h2>

                <p class="text-gray-600 mt-4">
                    ACE Tours & Travel Pte Ltd
                </p>
                <p class="text-gray-600">
                    133 New Bridge Road, #19-03/04/05 Chinatown Point, Singapore 059413
                </p>
            </div>
        </div>

        {{-- Informasi Booking & Customer --}}
        <div
            id="booking-information"
            class="flex flex-row justify-between mb-8 border-t border-b border-gray-200 py-4">
            <div class="">
                <h2 class="text-gray-800 text-xl font-normal mb-2">
                    Customer Details
                </h2>

                <p class="text-gray-600">
                    <strong>Name:</strong> {{ $transaction->customer_name }}
                </p>

                <p class="text-gray-600">
                    <strong>Email:</strong> {{ $transaction->customer_email }}
                </p>

                <p class="text-gray-600">
                    <strong>Phone:</strong> {{ $transaction->customer_phone }}
                </p>
            </div>
            <div class="text-right">
                <h2 class="text-gray-800 text-xl font-normal mb-2">
                    Booking Details
                </h2>
                <div class="flex flex-row justify-between gap-10">
                    <span class="text-gray-600 font-normal">Booking Code: </span>
                    <span class="text-gray-800 font-bold">{{ $transaction->code }}</span>
                </div>
                <div class="flex flex-row justify-between">
                    <span class="text-gray-600 font-normal">Date From: </span>
                    <span class="text-gray-800 font-normal">{{ \Carbon\Carbon::parse($transaction->date_from)->format('d F Y') }}</span>
                </div>
                <div class="flex flex-row justify-between">
                    <span class="text-gray-600 font-normal">Date To: </span>
                    <span class="text-gray-800 font-normal">{{ \Carbon\Carbon::parse($transaction->date_to)->format('d F Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Detail Akomodasi/Kamar --}}
        <div class="mb-8">
            <h2 class="text-gray-800 text-xl font-normal mb-2">Accommodation Details</h2>
            <table class="table w-full border-collapse border border-gray-300">
                <thead class="bg-gray-400">
                    <tr>
                        <th class="text-left px-4 py-2 border border-gray-300">Room Type</th>
                        <th class="text-center px-4 py-2 border border-gray-300">Guests</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->transactionDetails as $detail)
                    <tr>
                        <td class="text-left px-4 py-2">{{ $detail->productDetail->name_en }}</td>
                        <td class="text-center px-4 py-2">
                            @if ($detail->quantity_adult > 0)
                            {{ $detail->quantity_adult }} Adult(s)
                            @endif
                            @if ($detail->quantity_child > 0)
                            , {{ $detail->quantity_child }} Child(ren)
                            @endif
                            @if ($detail->quantity_infant > 0)
                            , {{ $detail->quantity_infant }} Infant(s)
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Detail Penumpang --}}
        <div class="mb-6">
            <h2 class="text-gray-800 text-xl font-normal mb-2">Passenger Details</h2>
            <table class="table w-full border-collapse border border-gray-300">
                <thead class="bg-gray-400">
                    <tr>
                        <th class="text-left px-4 py-2 border border-gray-300 w-1/12">No.</th>
                        <th class="text-left px-4 py-2 border border-gray-300">Title</th>
                        <th class="text-left px-4 py-2 border border-gray-300">First Name</th>
                        <th class="text-left px-4 py-2 border border-gray-300">Last Name</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaction->passengers as $passenger)
                    <tr>
                        <td class="text-left px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="text-left px-4 py-2">{{ $passenger->title }}</td>
                        <td class="text-left px-4 py-2">{{ $passenger->first_name }}</td>
                        <td class="text-left px-4 py-2">{{ $passenger->last_name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center px-4 py-2">No passenger data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer/Notes --}}
        <div class="mt-12 text-center text-gray-600 text-sm">
            <p>Thank you for choosing ACE Tours & Travel. Please present this E-Ticket upon arrival.</p>
            <p>For inquiries, please contact us at (65) 6438 2811 or book@acetours.com.sg.</p>
        </div>
    </div>
</body>

</html>