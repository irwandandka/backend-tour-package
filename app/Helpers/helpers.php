<?php

if (!function_exists('formatCurrency')) {
    function formatCurrency(float $amount, string $currencyCode = 'IDR'): string
    {
        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currencyCode);
    }
}
