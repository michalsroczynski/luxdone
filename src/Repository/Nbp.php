<?php

declare(strict_types=1);

namespace Task\Repository;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Nbp
{
    const RATES_BY_CURRENCY_BETWEEN_DATES_URL = 'http://api.nbp.pl/api/exchangerates/rates/a/';
    const RESPONSE_FORMAT = 'json';

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $currency
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function getRatesByCurrencyBetweenDates(
        string $currency,
        string $startDate,
        string $endDate
    ): array {
        $url = $this->getRatesByCurrencyBetweenDatesUrl($currency, $startDate, $endDate);

        $response = $this->client
            ->get($url)
            ->getBody()
            ->getContents();

        $rates = json_decode($response, true);

        if (!$response || !isset($rates['rates'])) {
            throw new Exception('Wrong response from NBP');
        }

        return $rates['rates'];
    }

    /**
     * @param string $currency
     * @param string $startDate
     * @param string $endDate
     * @return string
     */
    private function getRatesByCurrencyBetweenDatesUrl(
        string $currency,
        string $startDate,
        string $endDate
    ): string {
        return self::RATES_BY_CURRENCY_BETWEEN_DATES_URL
            . $currency . '/'
            . $startDate . '/'
            . $endDate
            . '?format=' . self::RESPONSE_FORMAT;
    }
}