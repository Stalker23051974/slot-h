<?php

namespace Application\Helpers\Locale\Locale_2;

/**
 * English date localization class.
 *
 * Formats dates according to English language conventions:
 * - Date format: "January 15, 2024" or "15 January 2024"
 * - Month names in nominative case
 *
 * @package   Application\Helpers\Locale\Locale_2
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Date extends \Application\Helpers\Locale\AbstractDate
{
    /**
     * Format date in English style.
     *
     * @param int|string $day     Day of the month
     * @param string     $month   Month name
     * @param int|string $year    Year
     * @param bool       $addYear Whether to include the year
     *
     * @return string Formatted date string
     */
    public static function getDate($day, $month, $year, $addYear = true)
    {
        return $month . ' ' . $day . (true === $addYear ? ', ' . $year : '');
    }
}