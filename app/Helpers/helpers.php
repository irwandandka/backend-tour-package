<?php

if (! function_exists('formatCurrency')) {
    function formatCurrency(float $amount, string $currencyCode = 'IDR'): string
    {
        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($amount, $currencyCode);
    }
}

if (! function_exists('generateTransactionCode')) {
    function generateTransactionCode(): string
    {
        $prefix = 'ACE';
        $date = date('ym');
        $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        return "{$prefix}-{$date}{$random}";
    }
}
