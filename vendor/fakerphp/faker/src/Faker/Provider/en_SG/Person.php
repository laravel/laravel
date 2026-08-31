<?php

namespace Faker\Provider\en_SG;

use Faker\Provider\DateTime;

class Person extends \Faker\Provider\Person
{
    /**
     * National Registration Identity Card number
     *
     * @param \DateTime|null $birthDate birth date
     *
     * @return string in format S1234567D or T1234567J
     */
    public static function nric(?\DateTime $birthDate = null): string
    {
        return self::singaporeId($birthDate, false);
    }

    /**
     * Foreign Identification Number
     *
     * @param \DateTime|null $issueDate issue date
     *
     * @return string in format F1234567N or G1234567X
     */
    public static function fin(?\DateTime $issueDate = null): string
    {
        return self::singaporeId($issueDate, true);
    }

    /**
     * Singapore NRIC (citizens) or FIN (foreigners) number
     *
     * @param \DateTime|null $issueDate birth/issue date
     * @param bool           $foreigner whether a person is foreigner or citizen
     *
     * @return string in format S1234567D, T1234567J, F1234567N or G1234567X
     */
    public static function singaporeId(?\DateTime $issueDate = null, bool $foreigner = false): string
    {
        if ($issueDate === null) {
            $issueDate = DateTime::dateTimeThisCentury();
        }

        $weights = [2, 7, 6, 5, 4, 3, 2];
        $result = '';

        if ($foreigner) {
            $prefix = ($issueDate < new \DateTime('2000-01-01')) ? 'F' : 'G';
            $checksumArr = ['X', 'W', 'U', 'T', 'R', 'Q', 'P', 'N', 'M', 'L', 'K'];
        } else {
            $prefix = ($issueDate < new \DateTime('2000-01-01')) ? 'S' : 'T';
            // NRICs before 1968 did not contain YOB
            $result .= ($issueDate < new \DateTime('1968-01-01')) ? static::randomElement(['00', '01']) : $issueDate->format('y');
            $checksumArr = ['J', 'Z', 'I', 'H', 'G', 'F', 'E', 'D', 'C', 'B', 'A'];
        }

        $length = count($weights);

        for ($i = strlen($result); $i < $length; ++$i) {
            $result .= static::randomDigit();
        }

        $checksum = in_array($prefix, ['G', 'T'], true) ? 4 : 0;

        for ($i = 0; $i < $length; ++$i) {
            $checksum += (int) $result[$i] * $weights[$i];
        }

        return $prefix . $result . $checksumArr[$checksum % 11];
    }
}
