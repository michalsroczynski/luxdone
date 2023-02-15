<?php

declare(strict_types=1);

namespace Task\Validator;

use Exception;

class Currency
{
    const SUPPOERTED_CURRENCIES = [
        'usd',
        'eur',
        'chf',
        'gbp'
    ];

    /**
     * @param string $currency
     * @return void
     */
    public function validate(string $currency): void
    {
        if (!in_array($currency, self::SUPPOERTED_CURRENCIES)) {
            throw new Exception('Not supported currency: ' . $currency);
        }
    }
}