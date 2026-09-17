<?php

namespace Application\Helpers\Locale\Locale_1;

/**
 * Russian date localization class.
 *
 * Formats dates according to Russian language conventions:
 * - Date format: "15 января 2024 года"
 * - Month names in genitive case (родительный падеж)
 *
 * @package   Application\Helpers\Locale\Locale_1
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Date extends \Application\Helpers\Locale\AbstractDate
{
    /**
     * Convert month name to genitive case (родительный падеж).
     *
     * Russian months in nominative: январь, февраль, март, апрель, май, июнь,
     * июль, август, сентябрь, октябрь, ноябрь, декабрь
     *
     * Genitive (for "15 января"): января, февраля, марта, апреля, мая, июня,
     * июля, августа, сентября, октября, ноября, декабря
     *
     * @param string $month Month name in nominative case
     *
     * @return null|string|string[] Month name in genitive case
     */
    public static function getRealMonth($month)
    {
        return preg_replace(["/ь$/", "/й$/", "/т$/"], ['я', 'я', 'та'], $month);
    }

    /**
     * Format date in Russian style.
     *
     * @param int|string $day     Day of the month
     * @param string     $month   Month name (will be converted to genitive)
     * @param int|string $year    Year
     * @param bool       $addYear Whether to include the year and "года"
     *
     * @return string Formatted date string
     */
    public static function getDate($day, $month, $year, $addYear = true)
    {
        return $day . ' ' . self::getRealMonth($month) . (true === $addYear ? ' ' . $year . ' года' : '');
    }
}