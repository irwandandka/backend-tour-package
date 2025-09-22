@component('mail::message')
# Payment Successful 🎉

Hi {{ $transaction->user->name }},

We have received your payment for order **#{{ $transaction->code }}**.

Your **E-Ticket** is attached to this email.
Please present it at the venue.

<!-- @component('mail::button', ['url' => url('/transactions/'.$transaction->id)])
View Details
@endcomponent -->

Thanks,<br>
{{ config('app.name') }}
@endcomponent