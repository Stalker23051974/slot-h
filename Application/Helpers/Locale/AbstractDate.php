<?php
// Норвегия, Финляндия, Эстония, Латвия, Литва, Польша, Беларусь, Украина, Грузия, Азербайджан, Казахстан, Китай, Монголия, Северная Корея, Япония, США, Армения, Кыргызстан, Узбекистан, Таджикистан

namespace Application\Helpers\Locale;

/**
 * Abstract base class for locale-specific date formatting.
 *
 * This class provides a foundation for country-specific date localization.
 * Each country's locale class extends this and overrides methods as needed
 * to match local date format conventions.
 *
 * Supported countries:
 * Norway, Finland, Estonia, Latvia, Lithuania, Poland, Belarus, Ukraine,
 * Georgia, Azerbaijan, Kazakhstan, China, Mongolia, North Korea, Japan,
 * United States, Armenia, Kyrgyzstan, Uzbekistan, Tajikistan
 *
 * @package   Application\Helpers\Locale
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class AbstractDate
{
    /**
     * Get the localized month name.
     *
     * By default, returns the month name as-is. Override in child classes
     * for language-specific month names.
     *
     * @param string $month Month name
     *
     * @return null|string|string[] Localized month name
     */
    public static function getRealMonth($month)
    {
        return $month;
    }

    /**
     * Format a date according to local conventions.
     *
     * Default format: "Month Day, Year" (US-style).
     * Override in child classes for country-specific formats.
     *
     * Examples:
     * - US: "January 15, 2024"
     * - UK: "15 January 2024"
     * - JP: "2024年1月15日"
     * - RU: "15 января 2024"
     *
     * @param int|string $day     Day of the month
     * @param string     $month   Month name (localized)
     * @param int|string $year    Year
     * @param bool       $addYear Whether to include the year in output
     *
     * @return string Formatted date string
     */
    public static function getDate($day, $month, $year, $addYear = true)
    {
        return $month . ' ' . $day . ', ' . $year;
    }
}