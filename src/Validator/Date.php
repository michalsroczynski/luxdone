<?php
/**
 * @author Convert Team
 * @copyright Copyright (c) Convert (https://www.convert.no/)
 */

declare(strict_types=1);

namespace Task\Validator;

use DateTime;
use Exception;

class Date
{
    const DATE_FORMAT = 'Ymd';

    /**
     * Validate date
     *
     * @param $date
     * @return void
     * @throws Exception
     */
    public function validate($date): void
    {
        $dateTime = DateTime::createFromFormat(self::DATE_FORMAT, $date);
        if (!$dateTime || $dateTime->format(self::DATE_FORMAT) !== $date) {
            throw new Exception('Wrong date: ' . $date);
        }
    }
}