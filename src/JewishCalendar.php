<?php declare(strict_types=1);

/**
 * Class JewishCalendar - calculations for the Jewish calendar.
 *
 * Hebrew characters in the code have either ISO-8859-8 or UTF_8 encoding.
 * Hebrew characters in the comments have UTF-8 encoding.
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

class JewishCalendar implements CalendarInterface
{
    /** Place this symbol before the final letter of a sequence of numerals */
    public const GERSHAYIM_ISO8859 = '"';
    public const GERSHAYIM         = "\xd7\xb4";

    /** Place this symbol after a single numeral */
    public const GERESH_ISO8859 = '\'';
    public const GERESH         = "\xd7\xb3";

    /** The Hebrew word for thousand */
    public const ALAFIM_ISO8859 = "\xe0\xec\xf4\xe9\xed";
    public const ALAFIM         = "\xd7\x90\xd7\x9c\xd7\xa4\xd7\x99\xd7\x9d";

    /** A year that is one day shorter than normal. */
    public const DEFECTIVE_YEAR = -1;

    /** A year that has the normal number of days. */
    public const REGULAR_YEAR = 0;

    /** A year that is one day longer than normal. */
    public const COMPLETE_YEAR = 1;

    /** @var string[] Hebrew numbers are represented by letters, similar to roman numerals. */
    private static $HEBREW_NUMERALS_ISO8859_8 = [
        400 => "\xfa",
        300 => "\xf9",
        200 => "\xf8",
        100 => "\xf7",
        90  => "\xf6",
        80  => "\xf4",
        70  => "\xf2",
        60  => "\xf1",
        50  => "\xf0",
        40  => "\xee",
        30  => "\xec",
        20  => "\xeb",
        19  => "\xe9\xe8",
        18  => "\xe9\xe7",
        17  => "\xe9\xe6",
        16  => "\xe8\xe6",
        15  => "\xe8\xe5",
        10  => "\xe9",
        9   => "\xe8",
        8   => "\xe7",
        7   => "\xe6",
        6   => "\xe5",
        5   => "\xe4",
        4   => "\xe3",
        3   => "\xe2",
        2   => "\xe1",
        1   => "\xe0",
    ];

    /** @var string[] Hebrew numbers are represented by letters, similar to roman numerals. */
    private static $HEBREW_NUMERALS_UTF8 = [
        400 => "\xd7\xaa",
        300 => "\xd7\xa9",
        200 => "\xd7\xa8",
        100 => "\xd7\xa7",
        90  => "\xd7\xa6",
        80  => "\xd7\xa4",
        70  => "\xd7\xa2",
        60  => "\xd7\xa1",
        50  => "\xd7\xa0",
        40  => "\xd7\x9e",
        30  => "\xd7\x9c",
        20  => "\xd7\x9b",
        19  => "\xd7\x99\xd7\x98",
        18  => "\xd7\x99\xd7\x97",
        17  => "\xd7\x99\xd7\x96",
        16  => "\xd7\x98\xd7\x96",
        15  => "\xd7\x98\xd7\x95",
        10  => "\xd7\x99",
        9   => "\xd7\x98",
        8   => "\xd7\x97",
        7   => "\xd7\x96",
        6   => "\xd7\x95",
        5   => "\xd7\x94",
        4   => "\xd7\x93",
        3   => "\xd7\x92",
        2   => "\xd7\x91",
        1   => "\xd7\x90",
    ];

    /** @var string[] Some letters have a different final form */
    private static $FINAL_FORMS_UTF8 = [
        "\xd7\x9b" => "\xd7\x9a",
        "\xd7\x9e" => "\xd7\x9d",
        "\xd7\xa0" => "\xd7\x9f",
        "\xd7\xa4" => "\xd7\xa3",
        "\xd7\xa6" => "\xd7\xa5",
    ];

    /** @var int[] These months have fixed lengths.  Others are variable. */
    private static $FIXED_MONTH_LENGTHS = [
        1 => 30, 4 => 29, 5 => 30, 7 => 29, 8 => 30, 9 => 29, 10 => 30, 11 => 29, 12 => 30, 13 => 29,
    ];

    /**
     * Cumulative number of days for each month in each type of year.
     * First index is false/true (non-leap year, leap year)
     * Second index is year type (-1, 0, 1)
     * Third index is month number (1 ... 13).
     *
     * @var int[][][]
     */
    private static $CUMULATIVE_DAYS = [
        0 => [ // Non-leap years
            self::DEFECTIVE_YEAR => [
                1 => 0, 30, 59, 88, 117, 147, 147, 176, 206, 235, 265, 294, 324,
            ],
            self::REGULAR_YEAR  => [ // Regular years
                1 => 0, 30, 59, 89, 118, 148, 148, 177, 207, 236, 266, 295, 325,
            ],
            self::COMPLETE_YEAR  => [ // Complete years
                1 => 0, 30, 60, 90, 119, 149, 149, 178, 208, 237, 267, 296, 326,
            ],
        ],
        1 => [ // Leap years
            self::DEFECTIVE_YEAR => [ // Deficient years
                1 => 0, 30, 59, 88, 117, 147, 177, 206, 236, 265, 295, 324, 354,
            ],
            self::REGULAR_YEAR  => [ // Regular years
                1 => 0, 30, 59, 89, 118, 148, 178, 207, 237, 266, 296, 325, 355,
            ],
            self::COMPLETE_YEAR  => [ // Complete years
                1 => 0, 30, 60, 90, 119, 149, 179, 208, 238, 267, 297, 326, 356,
            ],
        ],
    ];

    /** @var int[] Rosh Hashanah cannot fall on a Sunday, Wednesday or Friday.  Move the year start accordingly. */
    private static $ROSH_HASHANAH = [347998, 347997, 347997, 347998, 347997, 347998, 347997];

    /**
     * Determine the number of days in a specified month, allowing for leap years, etc.
     */
    public function daysInMonth(int $year, int $month): int
    {
        if ($year < 1) {
            throw new InvalidArgumentException('Year ' . $year . ' is invalid for this calendar');
        }

        if ($month < 1 || $month > 13) {
            throw new InvalidArgumentException('Month ' . $month . ' is invalid for this calendar');
        }

        if ($month == 2) {
            return $this->daysInMonthHeshvan($year);
        }

        if ($month == 3) {
            return $this->daysInMonthKislev($year);
        }

        if ($month == 6) {
            return $this->daysInMonthAdarI($year);
        }

        return self::$FIXED_MONTH_LENGTHS[$month];
    }

    /**
     * Determine the number of days in a week.
     */
    public function daysInWeek(): int
    {
        return 7;
    }

    /**
     * The escape sequence used to indicate this calendar in GEDCOM files.
     */
    public function gedcomCalendarEscape(): string
    {
        return '@#DHEBREW@';
    }

    /**
     * Determine whether or not a given year is a leap-year.
     */
    public function isLeapYear(int $year): bool
    {
        return (7 * $year + 1) % 19 < 7;
    }

    /**
     * What is the highest Julian day number that can be converted into this calendar.
     */
    public function jdEnd(): int
    {
        return \PHP_INT_MAX;
    }

    /**
     * What is the lowest Julian day number that can be converted into this calendar.
     */
    public function jdStart(): int
    {
        return 347998; // 1 Tishri 0001 AM
    }

    /**
     * Convert a Julian day number into a year.
     */
    protected function jdToY(int $julian_day): int
    {
        // Estimate the year, and underestimate it, it will be refined after
        $year = max((int) ((($julian_day - 347998) * 98496) / 35975351) - 1, 1);

        // Adjust by adding years;
        while ($julian_day >= $this->yToJd($year + 1)) {
            $year++;
        }

        return $year;
    }

    /**
     * Convert a Julian day number into a year/month/day.
     *
     * @return int[]
     */
    public function jdToYmd(int $julian_day): array
    {
        // Find the year, by adding one month at a time to use up the remaining days.
        $year  = $this->jdToY($julian_day);
        $month = 1;
        $day   = $julian_day - $this->yToJd($year) + 1;

        while ($day > $this->daysInMonth($year, $month)) {
            $day -= $this->daysInMonth($year, $month);
            $month++;
        }

        return [$year, $month, $day];
    }

    /**
     * Determine the number of months in a year (if given),
     * or the maximum number of months in any year.
     */
    public function monthsInYear(?int $year = null): int
    {
        if ($year !== null && !$this->isLeapYear($year)) {
            return 12;
        }

        return 13;
    }

    /**
     * Calculate the Julian Day number of the first day in a year.
     */
    protected function yToJd(int $year): int
    {
        $div19 = (int) (($year - 1) / 19);
        $mod19 = ($year - 1) % 19;

        $months      = 235 * $div19 + 12 * $mod19 + (int) ((7 * $mod19 + 1) / 19);
        $parts       = 204 + 793 * ($months % 1080);
        $hours       = 5 + 12 * $months + 793 * (int) ($months / 1080) + (int) ($parts / 1080);
        $conjunction = 1080 * ($hours % 24) + ($parts % 1080);
        $julian_day  = 1 + 29 * $months + (int) ($hours / 24);

        if (
            $conjunction >= 19440
            || $julian_day % 7 === 2 && $conjunction >= 9924 && !$this->isLeapYear($year)
            || $julian_day % 7 === 1 && $conjunction >= 16789 && $this->isLeapYear($year - 1)
        ) {
            $julian_day++;
        }

        // The actual year start depends on the day of the week
        return $julian_day + self::$ROSH_HASHANAH[$julian_day % 7];
    }

    /**
     * Convert a year/month/day to a Julian day number.
     */
    public function ymdToJd(int $year, int $month, int $day): int
    {
        return
            $this->yToJd($year)
            + self::$CUMULATIVE_DAYS[$this->isLeapYear($year)][$this->yearType($year)][$month]
            + $day - 1;
    }

    /**
     * Determine whether a year is normal, defective or complete.
     *
     * @return int defective (-1), normal (0) or complete (1)
     */
    private function yearType(int $year): int
    {
        $year_length = $this->yToJd($year + 1) - $this->yToJd($year);

        if ($year_length === 353 || $year_length === 383) {
            return self::DEFECTIVE_YEAR;
        }

        if ($year_length === 355 || $year_length === 385) {
            return self::COMPLETE_YEAR;
        }

        return self::REGULAR_YEAR;
    }

    /**
     * Calculate the number of days in Heshvan.
     */
    private function daysInMonthHeshvan(int $year): int
    {
        if ($this->yearType($year) === self::COMPLETE_YEAR) {
            return 30;
        }

        return 29;
    }

    /**
     * Calculate the number of days in Kislev.
     */
    private function daysInMonthKislev(int $year): int
    {
        if ($this->yearType($year) === self::DEFECTIVE_YEAR) {
            return 29;
        }

        return 30;
    }

    /**
     * Calculate the number of days in Adar I.
     */
    private function daysInMonthAdarI(int $year): int
    {
        if ($this->isLeapYear($year)) {
            return 30;
        }

        return 0;
    }

    /**
     * Hebrew month names.
     *
     * @link https://bugs.php.net/bug.php?id=54254
     *
     * @return string[]
     */
    protected function hebrewMonthNames(int $year): array
    {
        $leap_year = $this->isLeapYear($year);

        return [
            1 => "\xfa\xf9\xf8\xe9", // Tishri - תשרי
            "\xe7\xf9\xe5\xef", // Heshvan - חשון
            "\xeb\xf1\xec\xe5", // Kislev - כסלו
            "\xe8\xe1\xfa", // Tevet - טבת
            "\xf9\xe1\xe8", // Shevat - שבט
            $leap_year ? "\xe0\xe3\xf8 \xe0'" : "\xe0\xe3\xf8", // Adar I - אדר א׳ - אדר
            $leap_year ? "\xe0\xe3\xf8 \xe1'" : "\xe0\xe3\xf8", // Adar II - אדר ב׳ - אדר
            "\xf0\xe9\xf1\xef", // Nisan - ניסן
            "\xe0\xe9\xe9\xf8", // Iyar - אייר
            "\xf1\xe9\xe5\xef", // Sivan - סיון
            "\xfa\xee\xe5\xe6", // Tammuz - תמוז
            "\xe0\xe1", // Av - אב
            "\xe0\xec\xe5\xec", // Elul - אלול
        ];
    }

    /**
     * The Hebrew name of a given month.
     */
    protected function hebrewMonthName(int $year, int $month): string
    {
        $months = $this->hebrewMonthNames($year);

        return $months[$month];
    }

    /**
     * Add geresh (׳) and gershayim (״) punctuation to numeric values.
     *
     * Gereshayim is a contraction of “geresh” and “gershayim”.
     */
    protected function addGereshayim(string $hebrew): string
    {
        switch (\strlen($hebrew)) {
            case 0:
                // Zero, e.g. the zeros from the year 5,000
                return $hebrew;
            case 1:
                // Single digit - append a geresh
                return $hebrew . self::GERESH_ISO8859;
            default:
                // Multiple digits - insert a gershayim
                return substr_replace($hebrew, self::GERSHAYIM_ISO8859, -1, 0);
        }
    }

    /**
     * Convert a number into a string, in the style of roman numerals.
     *
     * @param string[] $numerals
     */
    private function numberToNumerals(int $number, array $numerals): string
    {
        $string = '';

        while ($number > 0) {
            foreach ($numerals as $n => $t) {
                if ($number >= $n) {
                    $string .= $t;
                    $number -= $n;

                    break;
                }
            }
        }

        return $string;
    }

    /**
     * Convert a number into Hebrew numerals using UTF8.
     */
    public function numberToHebrewNumerals(int $number, bool $show_thousands): string
    {
        // Years (e.g. "5782") may be written without the thousands (e.g. just "782"),
        // but since there is no zero, the number 5000 must be written as "5 thousand"
        if ($show_thousands || $number % 1000 === 0) {
            $thousands = (int) ($number / 1000);
        } else {
            $thousands = 0;
        }
        $number = $number % 1000;

        $hebrew = $this->numberToNumerals($number, self::$HEBREW_NUMERALS_UTF8);

        // Two bytes per UTF8 character
        if (\strlen($hebrew) === 2) {
            // Append a geresh after single-digit
            $hebrew .= self::GERESH;
        } elseif (\strlen($hebrew) > 2) {
            // Some letters have a "final" form, when used at the end of a word.
            $hebrew = substr($hebrew, 0, -2) . strtr(substr($hebrew, -2), self::$FINAL_FORMS_UTF8);
            // Insert a gershayim before the final letter
            $hebrew = substr_replace($hebrew, self::GERSHAYIM, -2, 0);
        }

        if ($thousands) {
            if ($hebrew) {
                $hebrew = $this->numberToHebrewNumerals($thousands, $show_thousands) . $hebrew;
            } else {
                $hebrew = $this->numberToHebrewNumerals($thousands, $show_thousands) . ' ' . self::ALAFIM;
            }
        }

        return $hebrew;
    }

    /**
     * Convert a number into Hebrew numerals using ISO8859-8.
     */
    protected function numberToHebrewNumeralsIso8859(int $number, bool $gereshayim): string
    {
        $hebrew = $this->numberToNumerals($number, self::$HEBREW_NUMERALS_ISO8859_8);

        // Hebrew numerals are letters.  Add punctuation to prevent confusion with actual words.
        if ($gereshayim) {
            return $this->addGereshayim($hebrew);
        }

        return $hebrew;
    }

    /**
     * Format a year using Hebrew numerals.
     */
    protected function yearToHebrewNumerals(int $year, bool $alafim_geresh, bool $alafim, bool $gereshayim): string
    {
        if ($year < 1000) {
            return $this->numberToHebrewNumeralsIso8859($year, $gereshayim);
        }

        $thousands = $this->numberToHebrewNumeralsIso8859((int) ($year / 1000), false);
        if ($alafim_geresh) {
            $thousands .= self::GERESH_ISO8859;
        }
        if ($alafim) {
            $thousands .= ' ' . self::ALAFIM_ISO8859 . ' ';
        }

        return $thousands . $this->numberToHebrewNumeralsIso8859($year % 1000, $gereshayim);
    }

    /**
     * Convert a Julian Day number into a Hebrew date.
     */
    public function jdToHebrew(int $julian_day, bool $alafim_garesh, bool $alafim, bool $gereshayim): string
    {
        [$year, $month, $day] = $this->jdToYmd($julian_day);

        return
            $this->numberToHebrewNumeralsIso8859($day, $gereshayim) . ' '
            . $this->hebrewMonthName($year, $month) . ' '
            . $this->yearToHebrewNumerals($year, $alafim_garesh, $alafim, $gereshayim);
    }
}
