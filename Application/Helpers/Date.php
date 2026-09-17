<?php

/**
 * Date and time manipulation helper.
 *
 * This class provides comprehensive date/time utilities including:
 * - Localized weekdays and months (multi-language support)
 * - Date formatting with localization
 * - Timestamp conversion and manipulation
 * - Date arithmetic (add/subtract days, months, years, hours)
 * - Human-readable time intervals
 * - Database-ready date/time formatting
 * - Time difference calculation
 * - RTL language support
 *
 * All date strings are localized using the Translate system,
 * making this helper fully multilingual.
 *
 * @package   Application\Helpers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers;

use \Config\CC as C;

class Date
{
    /**
     * Get full weekday names in current language.
     *
     * @return array Array of weekday names (Monday to Sunday)
     */
    public static function weekDays()
    {
        return [
            C::locale('Понедельник'),
            C::locale('Вторник'),
            C::locale('Среда'),
            C::locale('Четверг'),
            C::locale('Пятница'),
            C::locale('Суббота'),
            C::locale('Воскресенье')
        ];
    }

    /**
     * Get short weekday names (2 letters) in current language.
     *
     * @return array Array of short weekday names
     */
    public static function shortWeekDays2()
    {
        return [
            C::locale('Пн'),
            C::locale('Вт'),
            C::locale('Ср'),
            C::locale('Чт'),
            C::locale('Пт'),
            C::locale('Сб'),
            C::locale('Вс')
        ];
    }

    /**
     * Get short weekday names (3 letters) in current language.
     *
     * @return array Array of short weekday names
     */
    public static function shortWeekDays3()
    {
        return [
            C::locale('Пон'),
            C::locale('Втр'),
            C::locale('Срд'),
            C::locale('Чтв'),
            C::locale('Птн'),
            C::locale('Суб'),
            C::locale('Вск')
        ];
    }

    /**
     * Get full month names in current language.
     *
     * Index 0 is empty for 1-based month numbering.
     *
     * @return array Array of month names (index 1-12)
     */
    public static function monthTitleReal()
    {
        return [
            '',
            C::locale('January'),
            C::locale('February'),
            C::locale('March'),
            C::locale('April'),
            C::locale('May'),
            C::locale('June'),
            C::locale('Jule'),
            C::locale('August'),
            C::locale('September'),
            C::locale('October'),
            C::locale('November'),
            C::locale('December')
        ];
    }

    /**
     * Get short month names in current language.
     *
     * @return array Array of short month names (index 1-12)
     */
    public static function shortMonthTitleReal()
    {
        return [
            '',
            C::locale('Jan'),
            C::locale('Feb'),
            C::locale('Mar'),
            C::locale('Apr'),
            C::locale('May'),
            C::locale('Jun'),
            C::locale('Jul'),
            C::locale('Aug'),
            C::locale('Sept'),
            C::locale('Oct'),
            C::locale('Nov'),
            C::locale('Dec')
        ];
    }

    /**
     * Get number of days in each month.
     *
     * @param bool|int $year Year for leap year calculation
     *
     * @return array Days per month (index 0-11)
     */
    public static function getMonthDuration($year = false)
    {
        $res = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if (false !== $year && 0 === ($year % 4)) {
            $res[1]++; // February has 29 days in leap year
        }
        return $res;
    }

    /**
     * Format a date for human-readable display.
     *
     * Supports date ranges with localization.
     *
     * @param bool|int|array $date    Timestamp or array [start, end]
     * @param bool           $addYear Whether to include the year
     * @param bool           $addTime Whether to include the time
     *
     * @return string Human-readable date string
     */
    public static function getHumanDate($date = false, $addYear = true, $addTime = false)
    {
        if (false === $date) {
            $date = CURRENT_TIME;
        }

        $locale = '\Application\Helpers\Locale\Locale_' . \Application\Translate::getLanguage() . '\Date';
        /** @var \Application\Helpers\Locale\AbstractDate $locale */

        $date = !is_array($date)
            ? [$date => static::dbDate($date), '' => false]
            : [$date[0] => static::dbDate($date[0]), $date[1] => (isset($date[1]) && false !== $date[1]) ? static::dbDate($date[1]) : false];

        foreach ($date as $k => &$v) {
            if (false !== $v) {
                $i = dfExplode('-', $v);
                $v = $locale::getDate($i[2], static::monthTitleReal()[(int)$i[1]], $i[0], $addYear)
                    . (true === $addTime ? ', ' . date('H:i:s', $k) : '');
            }
        }

        $i = array_search(false, $date);
        if (false !== $i) {
            unset($date[$i]);
        }

        return implode(' - ', $date);
    }

    /**
     * Format a date for database storage (Y-m-d).
     *
     * @param bool|int|string $date   Timestamp or date string
     * @param string          $format Output format (default: Y-m-d)
     *
     * @return false|string Formatted date
     */
    public static function dbDate($date = false, $format = 'Y-m-d')
    {
        if (false === $date) {
            $date = CURRENT_TIME;
        }
        return date($format, is_numeric($date) ? $date : self::getStrToTime($date));
    }

    /**
     * Get Unix timestamp for the start of the day.
     *
     * @param bool|int $date Timestamp
     * @param bool     $his  Whether to return full datetime
     *
     * @return false|int|null|string|string[] Unix timestamp
     */
    public static function unixTimeOfDay($date = false, $his = false)
    {
        if (false === $date) {
            $date = CURRENT_TIME;
        }
        return false === $his
            ? self::getStrToTime(self::dbDate($date) . ' 00:00:00')
            : self::getStrToTime(self::dbDate($date));
    }

    /**
     * Convert a date string to Unix timestamp.
     *
     * @param string|int $date Date string or timestamp
     *
     * @return false|int|null|string|string[] Unix timestamp
     */
    public static function getStrToTime($date)
    {
        if (!is_string($date) || !preg_match("/[^\d]{1,}/", $date)) {
            return $date;
        }
        $date = preg_replace("/\//", '-', $date);
        $result = strtotime($date);
        return $result < 0 ? 0 : $result;
    }

    /**
     * Format datetime for database storage (Y-m-d H:i:s).
     *
     * @param bool|int|string $date Timestamp or date string
     *
     * @return bool|string Formatted datetime
     */
    public static function dbDateTime($date = false)
    {
        if (false === $date) {
            $date = CURRENT_TIME;
        }
        return date('Y-m-d H:i:s', is_numeric($date) ? $date : self::getStrToTime($date));
    }

    /**
     * Format date for display (dd.mm.YYYY HH:MM).
     *
     * @param bool|int|string $date Timestamp or date string
     *
     * @return false|string Formatted date
     */
    public static function getShowerTime($date = false)
    {
        if (false === $date) {
            $date = CURRENT_TIME;
        }
        return date('d.m.Y H:i', is_numeric($date) ? $date : self::getStrToTime($date));
    }

    /**
     * Format datetime for Russian display (dd-mm-YYYY HH:MM:SS).
     *
     * @param bool|int|string $date Timestamp or date string
     *
     * @return bool|string Formatted datetime
     */
    public static function rusDateTime($date = false)
    {
        return date('d-m-Y H:i:s', false === $date ? CURRENT_TIME : (is_numeric($date) ? $date : self::getStrToTime($date)));
    }

    /**
     * Extract time only from a timestamp.
     *
     * @param bool|int|string $date    Timestamp or date string
     * @param bool            $seconds Whether to include seconds
     *
     * @return false|string Time string (HH:MM or HH:MM:SS)
     */
    public static function onlyTime($date = false, $seconds = false)
    {
        return date('H:i' . (false === $seconds ? '' : ':s'), false === $date ? CURRENT_TIME : (is_numeric($date) ? $date : self::getStrToTime($date)));
    }

    /**
     * Get full text representation of a date.
     *
     * Returns: "Monday, 15 January 2024 года"
     *
     * @param bool|int|string $date Timestamp or date string
     *
     * @return string Full text date
     */
    public static function getTextDate($date = false)
    {
        $locale = '\Application\Helpers\Locale\Locale_' . \Application\Translate::getLanguage() . '\Date';
        /** @var \Application\Helpers\Locale\AbstractDate $locale */

        $current = false === $date ? CURRENT_TIME : (is_numeric($date) ? $date : self::getStrToTime($date));

        return Date::weekDays()[(int)date('N', $current) - 1]
            . ', ' . preg_replace("/^0/", '', date('d', $current))
            . ' ' . $locale::getRealMonth(Date::monthTitleReal()[(int)date('m', $current)])
            . ' ' . date('Y', $current)
            . ' ' . C::locale('года');
    }

    /**
     * Calculate a date offset by a period string.
     *
     * @param string     $period Period string (e.g., '-1 DAY', '+2 MONTHS')
     * @param bool|int   $date   Base timestamp
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getPreviosDate($period, $date = false, $string = true, $format = 'Y-m-d H:i:s')
    {
        $response = self::getStrToTime((false === $date ? date($format) : $date) . ' ' . $period);
        return true === $string ? static::dbDate($response, $format) : $response;
    }

    /**
     * Subtract hours from a date.
     *
     * @param bool|int   $date   Base timestamp
     * @param int        $hour   Number of hours to subtract
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getHourLeft($date = false, $hour = 1, $string = true, $format = 'Y-m-d H:i:s')
    {
        return static::getPreviosDate('-' . $hour . ' HOURS', $date, $string, $format);
    }

    /**
     * Subtract days from a date.
     *
     * @param bool|int   $date   Base timestamp
     * @param int        $day    Number of days to subtract
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getDayLeft($date = false, $day = 1, $string = true, $format = 'Y-m-d H:i:s')
    {
        return static::getPreviosDate('-' . $day . ' DAY', $date, $string, $format);
    }

    /**
     * Add days to a date.
     *
     * @param bool|int   $date   Base timestamp
     * @param int        $day    Number of days to add
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getDayRight($date = false, $day = 1, $string = true, $format = 'Y-m-d H:i:s')
    {
        return static::getPreviosDate('+' . $day . ' DAY', $date, $string, $format);
    }

    /**
     * Subtract months from a date.
     *
     * @param bool|int   $date   Base timestamp
     * @param int        $month  Number of months to subtract
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getMonthLeft($date = false, $month = 1, $string = true, $format = 'Y-m-d H:i:s')
    {
        return static::getPreviosDate('-' . $month . ' MONTH', $date, $string, $format);
    }

    /**
     * Add months to a date.
     *
     * @param bool|int   $date   Base timestamp
     * @param int        $month  Number of months to add
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getMonthRight($date = false, $month = 1, $string = true, $format = 'Y-m-d H:i:s')
    {
        return static::getPreviosDate('+' . $month . ' MONTH', $date, $string, $format);
    }

    /**
     * Subtract years from a date.
     *
     * @param bool|int   $date   Base timestamp
     * @param int        $year   Number of years to subtract
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getYearLeft($date = false, $year = 1, $string = true, $format = 'Y-m-d H:i:s')
    {
        return static::getPreviosDate('-' . $year . ' YEAR', $date, $string, $format);
    }

    /**
     * Add years to a date.
     *
     * @param bool|int   $date   Base timestamp
     * @param int        $year   Number of years to add
     * @param bool       $string Return as string or timestamp
     * @param string     $format Output format
     *
     * @return false|int|null|string|string[] Calculated date
     */
    public static function getYearRight($date = false, $year = 1, $string = true, $format = 'Y-m-d H:i:s')
    {
        return static::getPreviosDate('+' . $year . ' YEAR', $date, $string, $format);
    }

    /**
     * Get ISO 8601 full timestamp.
     *
     * @return string ISO 8601 timestamp (YYYY-MM-DDTHH:MM:SS+00:00)
     */
    public static function getFull()
    {
        return date('Y-m-d', CURRENT_TIME) . 'T' . date('H:i:s', CURRENT_TIME) . '+00:00';
    }

    /**
     * Convert seconds to human-readable time interval.
     *
     * Supports pluralization with Russian/English language rules.
     *
     * @param int $v Time in seconds
     *
     * @return string Human-readable interval (e.g., "2 days 3 hours 15 minutes")
     */
    public static function adaptTimestamp($v)
    {
        $result = [];

        foreach ([
                     31536000 => function ($v1) {
                         return $v1 == 1 ? C::locale('year') : ($v1 < 5 ? C::locale(' year') : C::locale('years'));
                     },
                     2592000 => function ($v1) {
                         return $v1 == 1 ? C::locale('month') : ($v1 < 5 ? C::locale('months') : C::locale(' months'));
                     },
                     604800 => function ($v1) {
                         return $v1 == 1 ? C::locale('week') : C::locale('weeks');
                     },
                     86400 => function ($v1) {
                         return $v1 == 1 ? C::locale('day') : C::locale('days');
                     },
                     3600 => function ($v1) {
                         return ($v1 == 1 || $v1 == 21) ? C::locale('hour') : (($v1 < 5 || $v1 > 21) ? C::locale('hours') : ($v1 < 21 ? C::locale(' hours') : C::locale(' hours')));
                     },
                     60 => function ($v1) {
                         return dfInArray($v1, [1, 21, 31, 41, 51]) ? C::locale('minute') : (dfInArray($v1, [2, 3, 4, 21, 22, 23, 24, 31, 32, 33, 34, 41, 42, 43, 44, 51, 52, 53, 54]) ? C::locale('minutes') : C::locale('minutes'));
                     }
                 ] as $k0 => $v0) {
            if ($v > $k0) {
                $v1 = (int)($v / $k0);
                $result[] = $v1 . ' ' . $v0($v1);
                $v -= $v1 * $k0;
            }
        }

        if ($v > 0) {
            $v1 = $v;
            /** @var int $v1 */
            $result[] = $v . ' ' . (dfInArray($v1, [1, 21, 31, 41, 51])
                    ? C::locale('second')
                    : (dfInArray($v, [2, 3, 4, 22, 23, 24, 32, 33, 34, 42, 43, 44, 52, 53, 54])
                        ? C::locale('seconds')
                        : C::locale('seconds')));
        }

        return implode(' ', $result);
    }

    /**
     * Fix and parse ISO date string.
     *
     * @param string $v ISO date string (YYYY-MM-DDTHH:MM:SS.mmm)
     *
     * @return false|int|null|string|string[] Unix timestamp
     */
    public static function fixDate($v)
    {
        return self::getStrToTime(preg_replace("/^([\d]{4})\-([\d]{2})\-([\d]{2})T(.*?)\.[\d]{1,}/", "$1-$2-$3 $4", $v));
    }

    /**
     * Convert seconds to time format (HH:MM:SS).
     *
     * @param int  $time        Time in seconds
     * @param bool $showHours   Whether to show hours
     * @param bool $showMinutes Whether to show minutes
     * @param bool $showSeconds Whether to show seconds
     *
     * @return string Formatted time (e.g., "01:30:45")
     */
    public static function convertInTime($time, $showHours = true, $showMinutes = true, $showSeconds = true)
    {
        $hours = (int)($time / 3600);
        $minutes = (int)(($time - ($hours * 3600)) / 60);
        $seconds = $time - ($hours * 3600) - $minutes * 60;

        $result = [];
        if (true === $showHours) {
            $result[] = ($hours < 10 ? '0' : '') . $hours;
        }
        if (true === $showMinutes) {
            $result[] = ($minutes < 10 ? '0' : '') . $minutes;
        }
        if (true === $showSeconds) {
            $result[] = ($seconds < 10 ? '0' : '') . $seconds;
        }

        return implode(':', $result);
    }

    /**
     * Convert time string (HH:MM:SS) to seconds.
     *
     * @param string $time Time string (e.g., "01:30:45")
     *
     * @return int Total seconds
     */
    public static function convertFromTime($time)
    {
        $v = dfExplode(':', $time);
        return $v[0] * 3600 + $v[1] * 60 + $v[2];
    }
}