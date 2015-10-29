<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml;

final class Helper
{
    const TIME_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @param string $duration
     */
    public static function validateDurationString($duration)
    {
        if ($duration) {
            try {
                new \DateInterval((string) $duration);
            } catch (\Exception $ex) {
                throw new \InvalidArgumentException(sprintf("Invalid duration '%s' format", $duration), 0, $ex);
            }
        }
    }

    /**
     * @param int $time
     *
     * @return string
     */
    public static function time2string($time)
    {
        return gmdate('Y-m-d\TH:i:s\Z', $time);
    }

    /**
     * @param int|string|\DateTime $value
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public static function getTimestampFromValue($value)
    {
        if (is_string($value)) {
            return self::parseSAMLTime($value);
        } elseif ($value instanceof \DateTime) {
            return $value->getTimestamp();
        } elseif (is_int($value)) {
            return $value;
        } else {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param string $time
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public static function parseSAMLTime($time)
    {
        $matches = array();
        if (preg_match(
            '/^(\\d\\d\\d\\d)-(\\d\\d)-(\\d\\d)T(\\d\\d):(\\d\\d):(\\d\\d)(?:\\.\\d+)?Z$/D',
            $time,
            $matches
        ) == 0) {
            throw new \InvalidArgumentException('Invalid SAML2 timestamp: '.$time);
        }

        $year = intval($matches[1]);
        $month = intval($matches[2]);
        $day = intval($matches[3]);
        $hour = intval($matches[4]);
        $minute = intval($matches[5]);
        $second = intval($matches[6]);

        // Use gmmktime because the timestamp will always be given in UTC.
        $ts = gmmktime($hour, $minute, $second, $month, $day, $year);

        return $ts;
    }

    /**
     * @param int $length
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function generateRandomBytes($length)
    {
        $length = intval($length);
        if ($length <= 0) {
            throw new \InvalidArgumentException();
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        }

        $data = '';
        for ($i = 0; $i < $length; ++$i) {
            $data .= chr(mt_rand(0, 255));
        }

        return $data;
    }

    /**
     * @param string $bytes
     *
     * @return string
     */
    public static function stringToHex($bytes)
    {
        $result = '';
        $len = strlen($bytes);
        for ($i = 0; $i < $len; ++$i) {
            $result .= sprintf('%02x', ord($bytes[$i]));
        }

        return $result;
    }

    /**
     * @return string
     */
    public static function generateID()
    {
        return '_'.self::stringToHex(self::generateRandomBytes(21));
    }

    /**
     * Is ID element at least 128 bits in length (SAML2.0 standard section 1.3.4).
     *
     * @param string $id
     *
     * @return bool
     */
    public static function validateIdString($id)
    {
        return is_string($id) && strlen(trim($id)) >= 16;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function validateRequiredString($value)
    {
        return is_string($value) && strlen(trim($value)) > 0;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function validateOptionalString($value)
    {
        return $value === null || self::validateRequiredString($value);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function validateWellFormedUriString($value)
    {
        $value = trim($value);
        if ($value == '' || strlen($value) > 65520) {
            return false;
        }

        if (preg_match('|\s|', $value)) {
            return false;
        }

        $parts = parse_url($value);
        if (isset($parts['scheme'])) {
            if ($parts['scheme'] != rawurlencode($parts['scheme'])) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param int $notBefore
     * @param int $now
     * @param int $allowedSecondsSkew
     *
     * @return bool
     */
    public static function validateNotBefore($notBefore, $now, $allowedSecondsSkew)
    {
        return $notBefore == null || (($notBefore - $allowedSecondsSkew) < $now);
    }

    /**
     * @param int $notOnOrAfter
     * @param int $now
     * @param int $allowedSecondsSkew
     *
     * @return bool
     */
    public static function validateNotOnOrAfter($notOnOrAfter, $now, $allowedSecondsSkew)
    {
        return $notOnOrAfter == null || ($now < ($notOnOrAfter + $allowedSecondsSkew));
    }
}
