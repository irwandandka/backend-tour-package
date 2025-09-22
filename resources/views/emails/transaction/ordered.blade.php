@component('mail::message')
# Order Confirmation

Hi {{ $transaction->user->name }},

Thank you for your order **{{ $transaction->code }}**.

Here are your order details:
**Booking Date:** {{ $transaction->booking_date }} <br>
**Product:** {{ $transaction->product->name }} <br>
**Quantity:** {{ $transaction->quantity }} <br>
**Total:** {{ $transaction->currency->symbol }} {{ number_format($transaction->total_amount, 0, ',', '.') }}

We've attached the **invoice** for your order.

<!-- @component('mail::button', ['url' => url('/transactions/'.$transaction->id)])
View Order
@endcomponent -->

Thanks,<br>
{{ config('app.name') }}
@endcomponent