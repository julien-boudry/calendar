<?php declare(strict_types=1);

/**
 * class Shim - PHP implementations of functions from the PHP calendar extension.
 *
 * @link      http://php.net/manual/en/book.calendar.php
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2014-2021 Greg Roach
 * @license   This program is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU General Public License as published by
 *            the Free Software Foundation, either version 3 of the License, or
 *            (at your option) any later version.
 *
 *            This program is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU General Public License for more details.
 *
 *            You should have received a copy of the GNU General Public License
 *            along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fisharebest\ExtCalendar;

use InvalidArgumentException;
use ValueError;

class Shim
{
    private static FrenchCalendar $french_calendar;

    private static GregorianCalendar $gregorian_calendar;

    private static JewishCalendar $jewish_calendar;

    private static JulianCalendar $julian_calendar;

    /**
     * English names for the days of the week.
     *
     * @var string[]
     */
    private static $DAY_NAMES = [
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
    ];

    /**
     * Abbreviated English names for the days of the week.
     *
     * @var string[]
     */
    private static $DAY_NAMES_SHORT = [
        'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat',
    ];

    /** @var string[] Names of the months of the Gregorian/Julian calendars */
    private static $MONTH_NAMES = [
        '', 'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /** @var string[] Abbreviated names of the months of the Gregorian/Julian calendars */
    private static $MONTH_NAMES_SHORT = [
        '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
    ];

    /** @var string[] Name of the months of the French calendar */
    private static $MONTH_NAMES_FRENCH = [
        '', 'Vendemiaire', 'Brumaire', 'Frimaire', 'Nivose', 'Pluviose', 'Ventose',
        'Germinal', 'Floreal', 'Prairial', 'Messidor', 'Thermidor', 'Fructidor', 'Extra',
    ];

    /** @var string[] Names of the months of the Jewish calendar in a non-leap year */
    private static $MONTH_NAMES_JEWISH = [
        '', 'Tishri', 'Heshvan', 'Kislev', 'Tevet', 'Shevat', 'Adar',
        'Adar', 'Nisan', 'Iyyar', 'Sivan', 'Tammuz', 'Av', 'Elul',
    ];

    /** @var string[] Names of the months of the Jewish calendar in a leap year */
    private static $MONTH_NAMES_JEWISH_LEAP_YEAR = [
        '', 'Tishri', 'Heshvan', 'Kislev', 'Tevet', 'Shevat', 'Adar I',
        'Adar II', 'Nisan', 'Iyyar', 'Sivan', 'Tammuz', 'Av', 'Elul',
    ];

    /**
     * Create the necessary shims to emulate the ext/calendar package.
     */
    public static function create(): void
    {
        self::$french_calendar    = new FrenchCalendar;
        self::$gregorian_calendar = new GregorianCalendar;
        self::$jewish_calendar    = new JewishCalendar;
        self::$julian_calendar    = new JulianCalendar;
    }

    /**
     * Return the number of days in a month for a given year and calendar.
     *
     * Shim implementation of cal_days_in_month()
     *
     * @link https://php.net/cal_days_in_month
     */
    public static function calDaysInMonth(int $calendar_id, int $month, int $year): int
    {
        switch ($calendar_id) {
            case \CAL_FRENCH:
                return self::calDaysInMonthFrench($year, $month);

            case \CAL_GREGORIAN:
                return self::calDaysInMonthCalendar(self::$gregorian_calendar, $year, $month);

            case \CAL_JEWISH:
                return self::calDaysInMonthCalendar(self::$jewish_calendar, $year, $month);

            case \CAL_JULIAN:
                return self::calDaysInMonthCalendar(self::$julian_calendar, $year, $month);

            default:
                throw new ValueError('cal_days_in_month(): Argument #1 ($calendar) must be a valid calendar ID');
        }
    }

    /**
     * Calculate the number of days in a month in a specified (Gregorian or Julian) calendar.
     */
    private static function calDaysInMonthCalendar(CalendarInterface $calendar, int $year, int $month): int
    {
        try {
            return $calendar->daysInMonth($year, $month);
        } catch (InvalidArgumentException $ex) {
            throw new ValueError('Invalid date');
        }
    }

    /**
     * Calculate the number of days in a month in the French calendar.
     *
     * Mimic PHP’s validation of the parameters
     */
    private static function calDaysInMonthFrench(int $year, int $month): int
    {
        if ($year > 14) {
            throw new ValueError('Invalid date');
        }

        return self::calDaysInMonthCalendar(self::$french_calendar, $year, $month);
    }

    /**
     * Converts from Julian Day Count to a supported calendar.
     *
     * Shim implementation of cal_from_jd()
     *
     * @link https://php.net/cal_from_jd
     *
     * @return array<string, int|string|null>
     */
    public static function calFromJd(int $julian_day, int $calendar_id): array
    {
        switch ($calendar_id) {
            case \CAL_FRENCH:
                return self::calFromJdCalendar($julian_day, self::jdToFrench($julian_day), self::$MONTH_NAMES_FRENCH, self::$MONTH_NAMES_FRENCH);

            case \CAL_GREGORIAN:
                return self::calFromJdCalendar($julian_day, self::jdToGregorian($julian_day), self::$MONTH_NAMES, self::$MONTH_NAMES_SHORT);

            case \CAL_JEWISH:
                $months = self::jdMonthNameJewishMonths($julian_day);

                $cal = self::calFromJdCalendar($julian_day, self::jdToCalendar(self::$jewish_calendar, $julian_day, 347998, 324542846), $months, $months);

                if ($julian_day < 347998 || $julian_day > 324542846) {
                    $cal['dow'] = null;
                    $cal['dayname'] = '';
                    $cal['abbrevdayname'] = '';
                }

                return $cal;

            case \CAL_JULIAN:
                return self::calFromJdCalendar($julian_day, self::jdToJulian($julian_day), self::$MONTH_NAMES, self::$MONTH_NAMES_SHORT);

            default:
                throw new ValueError('cal_from_jd(): Argument #2 ($calendar) must be a valid calendar ID');
        }
    }

    /**
     * Convert a Julian day number to a calendar and provide details.
     *
     * @param string[] $months
     * @param string[] $months_short
     *
     * @return array<string, int|string|null>
     */
    private static function calFromJdCalendar(int $julian_day, string $mdy, array $months, array $months_short): array
    {
        [$month, $day, $year] = explode('/', $mdy);

        return [
            'date'          => $month . '/' . $day . '/' . $year,
            'month'         => (int) $month,
            'day'           => (int) $day,
            'year'          => (int) $year,
            'dow'           => self::jdDayOfWeek($julian_day, 0),
            'abbrevdayname' => self::jdDayOfWeek($julian_day, 2),
            'dayname'       => self::jdDayOfWeek($julian_day, 1),
            'abbrevmonth'   => $months_short[$month],
            'monthname'     => $months[$month],
        ];
    }

    /**
     * Returns information about a particular calendar.
     *
     * Shim implementation of cal_info()
     *
     * @link https://php.net/cal_info
     *
     * @return array<string|int, mixed>
     */
    public static function calInfo(int $calendar_id): array
    {
        switch ($calendar_id) {
            case \CAL_FRENCH:
                return self::calInfoCalendar(self::$MONTH_NAMES_FRENCH, self::$MONTH_NAMES_FRENCH, 30, 'French', 'CAL_FRENCH');

            case \CAL_GREGORIAN:
                return self::calInfoCalendar(self::$MONTH_NAMES, self::$MONTH_NAMES_SHORT, 31, 'Gregorian', 'CAL_GREGORIAN');

            case \CAL_JEWISH:
                return self::calInfoCalendar(self::$MONTH_NAMES_JEWISH_LEAP_YEAR, self::$MONTH_NAMES_JEWISH_LEAP_YEAR, 30, 'Jewish', 'CAL_JEWISH');

            case \CAL_JULIAN:
                return self::calInfoCalendar(self::$MONTH_NAMES, self::$MONTH_NAMES_SHORT, 31, 'Julian', 'CAL_JULIAN');

            case -1:
                return [
                    \CAL_GREGORIAN => self::calInfo(\CAL_GREGORIAN),
                    \CAL_JULIAN    => self::calInfo(\CAL_JULIAN),
                    \CAL_JEWISH    => self::calInfo(\CAL_JEWISH),
                    \CAL_FRENCH    => self::calInfo(\CAL_FRENCH),
                ];

            default:
                throw new ValueError('cal_info(): Argument #1 ($calendar) must be a valid calendar ID');
        }
    }

    /**
     * Returns information about the French calendar.
     *
     * @param string[] $month_names
     * @param string[] $month_names_short
     *
     * @return array<string, mixed>
     */
    private static function calInfoCalendar(array $month_names, array $month_names_short, int $max_days_in_month, string $calendar_name, string $calendar_symbol): array
    {
        return [
            'months'         => \array_slice($month_names, 1, null, true),
            'abbrevmonths'   => \array_slice($month_names_short, 1, null, true),
            'maxdaysinmonth' => $max_days_in_month,
            'calname'        => $calendar_name,
            'calsymbol'      => $calendar_symbol,
        ];
    }

    /**
     *  Converts from a supported calendar to Julian Day Count.
     *
     * Shim implementation of cal_to_jd()
     *
     * @link https://php.net/cal_to_jd
     */
    public static function calToJd(int $calendar_id, int $month, int $day, int $year): int
    {
        switch ($calendar_id) {
            case \CAL_FRENCH:
                return self::frenchToJd($month, $day, $year);

            case \CAL_GREGORIAN:
                return self::gregorianToJd($month, $day, $year);

            case \CAL_JEWISH:
                return self::jewishToJd($month, $day, $year);

            case \CAL_JULIAN:
                return self::julianToJd($month, $day, $year);

            default:
                throw new ValueError('cal_to_jd(): Argument #1 ($calendar) must be a valid calendar ID');
        }
    }

    /**
     * Get Unix timestamp for midnight on Easter of a given year.
     *
     * Shim implementation of easter_date()
     *
     * @link https://php.net/easter_date
     */
    public static function easterDate(int $year): int
    {
        if ($year < 1970) {
            throw new ValueError('easter_date(): Argument #1 ($year) must be a year after 1970 (inclusive)');
        }

        $days = self::$gregorian_calendar->easterDays($year);

        if ($days < 11) {
            return (int) mktime(0, 0, 0, 3, $days + 21, $year);
        }

        return (int) mktime(0, 0, 0, 4, $days - 10, $year);
    }

    /**
     * Get number of days after March 21 on which Easter falls for a given year.
     *
     * Shim implementation of easter_days()
     *
     * @link https://php.net/easter_days
     */
    public static function easterDays(int $year, int $method): int
    {
        if ($year < 1) {
            throw new ValueError('easter_days(): Argument #1 ($year) must be between 1 and ' . \PHP_INT_MAX);
        }

        if (
            $method === \CAL_EASTER_ALWAYS_JULIAN
            || $method === \CAL_EASTER_ROMAN && $year <= 1582
            || $year <= 1752 && $method !== \CAL_EASTER_ROMAN && $method !== \CAL_EASTER_ALWAYS_GREGORIAN
        ) {
            return self::$julian_calendar->easterDays($year);
        }

        return self::$gregorian_calendar->easterDays($year);
    }

    /**
     * Converts a date from the French Republican Calendar to a Julian Day Count.
     *
     * Shim implementation of FrenchToJD()
     *
     * @link https://php.net/FrenchToJD
     */
    public static function frenchToJd(int $month, int $day, int $year): int
    {
        if ($year <= 0) {
            return 0;
        }

        return self::$french_calendar->ymdToJd($year, $month, $day);
    }

    /**
     * Converts a Gregorian date to Julian Day Count.
     *
     * Shim implementation of GregorianToJD()
     *
     * @link https://php.net/GregorianToJD
     */
    public static function gregorianToJd(int $month, int $day, int $year): int
    {
        if ($year === 0) {
            return 0;
        }

        return self::$gregorian_calendar->ymdToJd($year, $month, $day);
    }

    /**
     * Returns the day of the week.
     *
     * Shim implementation of JDDayOfWeek()
     *
     * @link https://php.net/JDDayOfWeek
     */
    public static function jdDayOfWeek(int $julian_day, int $mode): int|string
    {
        $day_of_week = ($julian_day + 1) % 7;
        if ($day_of_week < 0) {
            $day_of_week += 7;
        }

        switch ($mode) {
            case 1:
                return self::$DAY_NAMES[$day_of_week];

            case 2:
                return self::$DAY_NAMES_SHORT[$day_of_week];

            default: // CAL_DOW_DAYNO or anything else
                return $day_of_week;
        }
    }

    /**
     * Returns a month name.
     *
     * Shim implementation of JDMonthName()
     *
     * @link https://php.net/JDMonthName
     */
    public static function jdMonthName(int $julian_day, int $mode): string
    {
        switch ($mode) {
            case \CAL_MONTH_GREGORIAN_LONG:
                return self::jdMonthNameCalendar(self::$gregorian_calendar, $julian_day, self::$MONTH_NAMES);

            case \CAL_MONTH_JULIAN_LONG:
                return self::jdMonthNameCalendar(self::$julian_calendar, $julian_day, self::$MONTH_NAMES);

            case \CAL_MONTH_JULIAN_SHORT:
                return self::jdMonthNameCalendar(self::$julian_calendar, $julian_day, self::$MONTH_NAMES_SHORT);

            case \CAL_MONTH_JEWISH:
                return self::jdMonthNameCalendar(self::$jewish_calendar, $julian_day, self::jdMonthNameJewishMonths($julian_day));

            case \CAL_MONTH_FRENCH:
                return self::jdMonthNameCalendar(self::$french_calendar, $julian_day, self::$MONTH_NAMES_FRENCH);

            case \CAL_MONTH_GREGORIAN_SHORT:
            default:
                return self::jdMonthNameCalendar(self::$gregorian_calendar, $julian_day, self::$MONTH_NAMES_SHORT);
        }
    }

    /**
     * Calculate the month-name for a given julian day, in a given calendar,
     * with given set of month names.
     *
     * @param string[] $months
     */
    private static function jdMonthNameCalendar(CalendarInterface $calendar, int $julian_day, array $months): string
    {
        [, $month] = $calendar->jdToYmd($julian_day);

        return $months[$month];
    }

    /**
     * Determine which month names to use for the Jewish calendar.
     *
     * @return string[]
     */
    private static function jdMonthNameJewishMonths(int $julian_day): array
    {
        [, , $year] = explode('/', self::jdToCalendar(self::$jewish_calendar, $julian_day, 347998, 324542846));

        if (self::$jewish_calendar->isLeapYear((int) $year)) {
            return self::$MONTH_NAMES_JEWISH_LEAP_YEAR;
        }

        return self::$MONTH_NAMES_JEWISH;
    }

    /**
     * Convert a Julian day in a specific calendar to a day/month/year.
     *
     * Julian days outside the specified range are returned as “0/0/0”.
     */
    private static function jdToCalendar(CalendarInterface $calendar, int $julian_day, int $min_jd, int $max_jd): string
    {
        if ($julian_day >= $min_jd && $julian_day <= $max_jd) {
            [$year, $month, $day] = $calendar->jdToYmd($julian_day);

            return $month . '/' . $day . '/' . $year;
        }

        return '0/0/0';
    }

    /**
     * Converts a Julian Day Count to the French Republican Calendar.
     *
     * Shim implementation of JDToFrench()
     *
     * @link https://php.net/JDToFrench
     */
    public static function jdToFrench(int $julian_day): string
    {
        // JDToFrench() converts years 1 to 14 inclusive, even though the calendar
        // officially ended on 10 Nivôse 14 (JD 2380687, 31st December 1805 Gregorian).
        return self::jdToCalendar(self::$french_calendar, $julian_day, 2375840, 2380952);
    }

    /**
     * Converts Julian Day Count to Gregorian date.
     *
     * Shim implementation of JDToGregorian()
     *
     * @link https://php.net/JDToGregorian
     */
    public static function jdToGregorian(int $julian_day): string
    {
        return self::jdToCalendar(self::$gregorian_calendar, $julian_day, 1, 784350656097);
    }

    /**
     * Converts a Julian day count to a Jewish calendar date.
     *
     * Shim implementation of JdtoJewish()
     *
     * @link https://php.net/JdtoJewish
     */
    public static function jdToJewish(int $julian_day, bool $hebrew, int $fl): string
    {
        if ($hebrew) {
            if ($julian_day < 347998 || $julian_day > 4000075) {
                throw new ValueError('Year out of range (0-9999)');
            }

            return self::$jewish_calendar->jdToHebrew(
                $julian_day,
                (bool) ($fl & \CAL_JEWISH_ADD_ALAFIM_GERESH),
                (bool) ($fl & \CAL_JEWISH_ADD_ALAFIM),
                (bool) ($fl & \CAL_JEWISH_ADD_GERESHAYIM)
            );
        }

        // The upper limit prevents numeric overflow.
        return self::jdToCalendar(self::$jewish_calendar, $julian_day, 347998, 324542846);
    }

    /**
     * Converts a Julian Day Count to a Julian Calendar Date.
     *
     * Shim implementation of JDToJulian()
     *
     * @link https://php.net/JDToJulian
     */
    public static function jdToJulian(int $julian_day): string
    {
        return self::jdToCalendar(self::$julian_calendar, $julian_day, 1, 784368370349);
    }

    /**
     * Convert Julian Day to Unix timestamp.
     *
     * Shim implementation of jdtounix()
     *
     * @link https://php.net/jdtounix
     */
    public static function jdToUnix(int $julian_day): int
    {
        if ($julian_day >= 2440588 && $julian_day <= 106751993607888) {
            return ($julian_day - 2440588) * 86400;
        }

        throw new ValueError('jday must be between 2440588 and 106751993607888');
    }

    /**
     * What is the maximum value acceptable to jdtounix().
     *
     * @internal
     */
    public static function jdToUnixUpperLimit(): int
    {
        return 106751993607888;
    }

    /**
     * Converts a date in the Jewish Calendar to Julian Day Count.
     *
     * Shim implementation of JewishToJD()
     *
     * @link https://php.net/JewishToJD
     */
    public static function jewishToJd(int $month, int $day, int $year): int
    {
        if ($year <= 0 || $year >= 6000) {
            return 0;
        }

        return self::$jewish_calendar->ymdToJd($year, $month, $day);
    }

    /**
     * Converts a Julian Calendar date to Julian Day Count.
     *
     * Shim implementation of JulianToJD()
     *
     * @link https://php.net/JulianToJD
     */
    public static function julianToJd(int $month, int $day, int $year): int
    {
        if ($year === 0) {
            return 0;
        }

        return self::$julian_calendar->ymdToJd($year, $month, $day);
    }

    /**
     * Convert Unix timestamp to Julian Day.
     *
     * Shim implementation of unixtojd()
     *
     * @link https://php.net/unixtojd
     */
    public static function unixToJd(int $timestamp): int
    {
        if ($timestamp < 0) {
            throw new ValueError('unixtojd(): Argument #1 ($timestamp) must be greater than or equal to 0');
        }

        return self::GregorianToJd((int) date('n', $timestamp), (int) date('j', $timestamp), (int) date('Y', $timestamp));
    }
}
