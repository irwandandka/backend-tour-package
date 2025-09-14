@component('mail::message')
# Order Confirmation

Hi {{ $transaction->user->name }},

Thank you for your order **#{{ $transaction->code }}**.

**Product:** {{ $transaction->product->name }}  
**Quantity:** {{ $transaction->quantity }}  
**Total:** Rp {{ number_format($transaction->amount, 0, ',', '.') }}

We've attached the **invoice** for your order.

@component('mail::button', ['url' => url('/transactions/'.$transaction->id)])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
