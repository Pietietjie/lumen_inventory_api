<?php

namespace App\Helpers;

class CurrencyHelper {

    public static function formatCurrency($amount): string
    {
        $formattedAsNumber = number_format($amount, 2, '.', ' ');

        return config('currency.symbol') . $formattedAsNumber;
    }

}
