<?php

/**
 * Interface CalendarInterface - each calendar implementation needs to provide
 * these methods.
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

interface CalendarInterface
{
    /**
     * Determine the number of days in a specified month, allowing for leap years, etc.
     */
    public function daysInMonth(int $year, int $month): int;

    /**
     * Determine the number of days in a week.
     */
    public function daysInWeek(): int;

    /**
     * The escape sequence used to indicate this calendar in GEDCOM files.
     */
    public function gedcomCalendarEscape(): string;

    /**
     * Determine whether or not a given year is a leap-year.
     */
    public function isLeapYear(int $year): bool;

    /**
     * What is the highest Julian day number that can be converted into this calendar.
     */
    public function jdEnd(): int;

    /**
     * What is the lowest Julian day number that can be converted into this calendar.
     */
    public function jdStart(): int;

    /**
     * Convert a Julian day number into a year/month/day.
     *
     * @return int[]
     */
    public function jdToYmd(int $julian_day): array;

    /**
     * Determine the number of months in a year (if given),
     * or the maximum number of months in any year.
     */
    public function monthsInYear(?int $year = null): int;

    /**
     * Convert a year/month/day to a Julian day number.
     */
    public function ymdToJd(int $year, int $month, int $day): int;
}
