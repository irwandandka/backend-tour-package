<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket #{{ $transaction->code }}</title>
</head>
<body>
    <h1>E-Ticket</h1>
    <p>Order Code: {{ $transaction->code }}</p>
    <p>Customer: {{ $transaction->user->name }}</p>
    <p>Product: {{ $transaction->product->name }}</p>
    <p>Valid Date: {{ $transaction->date_from }} - {{ $transaction->date_to }}</p>
</body>
</html>
