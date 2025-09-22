<h2>Payment Confirmation</h2>
<p>Hi {{ $order->customer_name }},</p>
<p>We have received your payment for Order <strong>{{ $order->order_id }}</strong>.</p>
<p>Amount: Rp{{ number_format($order->amount, 0, ',', '.') }}</p>
<p>Status: {{ ucfirst($order->payment_status) }}</p>
<p>Thank you for your purchase 🎉</p>