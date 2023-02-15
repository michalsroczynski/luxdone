<?php

declare(strict_types=1);

namespace Task\Controller\Api;

use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Task\Validator\Currency;
use Task\Validator\Date;
use Task\Repository\Nbp as NbpRepository;

class Nbp
{
    const availableRequestMethod = 'GET';
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @var Date
     */
    private $dateValidator;

    /**
     * @var NbpRepository
     */
    private $nbpRepository;

    /**
     * @var Currency
     */
    private $currencyValidator;

    public function __construct(
        Date $dateValidator,
        Currency $currencyValidator,
        NbpRepository $nbpRepository
    ) {
        $this->dateValidator = $dateValidator;
        $this->currencyValidator = $currencyValidator;
        $this->nbpRepository = $nbpRepository;
    }

    /**
     * @param string $requestMethod
     * @param array $uri
     * @return array
     * @throws Exception
     * @throws GuzzleException
     */
    public function process(string $requestMethod, array $uri): array
    {
        switch ($requestMethod) {
            case self::availableRequestMethod:
                return $this->processGetMethod($uri);
                break;
            default:
                throw new Exception('Not available request method: ' . $requestMethod);
        }
    }

    /**
     * @param array $uri
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    private function processGetMethod(array $uri): array
    {
        if (count($uri) !== 3) {
            throw new Exception('Wrong GET request');
        }

        $this->currencyValidator->validate($uri[0]);
        $this->dateValidator->validate($uri[1]);
        $this->dateValidator->validate($uri[2]);

        $startDate = DateTime::createFromFormat(Date::DATE_FORMAT, $uri[1])->format(self::DATE_FORMAT);
        $endDate = DateTime::createFromFormat(Date::DATE_FORMAT, $uri[2])->format(self::DATE_FORMAT);

        $rates = $this->nbpRepository->getRatesByCurrencyBetweenDates($uri[0], $startDate, $endDate);

        return ['average_price' => $this->calculateAverageRate($rates)];
    }

    /**
     * @param array $rates
     * @return float
     */
    private function calculateAverageRate(array $rates): float
    {
        $avgRate = 0;

        foreach ($rates as $rate) {
            $avgRate += $rate['mid'];
        }

        return (float)number_format((float)($avgRate / count($rates)), 4, '.', '');
    }

}