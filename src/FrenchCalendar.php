<?php

/**
 * Class FrenchCalendar - calculations for the French Republican calendar.
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

/**
 * Class FrenchCalendar - calculations for the French Republican calendar.
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
class FrenchCalendar implements CalendarInterface
{
    /**
     * Determine the number of days in a specified month, allowing for leap years, etc.
     */
    public function daysInMonth(int $year, int $month): int
    {
        if ($year <= 0) {
            throw new InvalidArgumentException('Year ' . $year . ' is invalid for this calendar');
        }

        if ($month < 1 || $month > 13) {
            throw new InvalidArgumentException('Month ' . $month . ' is invalid for this calendar');
        }

        if ($month !== 13) {
            return 30;
        }

        if ($this->isLeapYear($year)) {
            return 6;
        }

        return 5;
    }

    /**
     * Determine the number of days in a week.
     */
    public function daysInWeek(): int
    {
        return 10;
    }

    /**
     * The escape sequence used to indicate this calendar in GEDCOM files.
     */
    public function gedcomCalendarEscape(): string
    {
        return '@#DFRENCH R@';
    }

    /**
     * Determine whether or not a given year is a leap-year.
     *
     * Leap years were based on astronomical observations.  Only years 3, 7 and 11
     * were ever observed.  Moves to a gregorian-like (fixed) system were proposed
     * but never implemented.
     */
    public function isLeapYear(int $year): bool
    {
        return $year % 4 == 3;
    }

    /**
     * What is the highest Julian day number that can be converted into this calendar.
     */
    public function jdEnd(): int
    {
        return 2380687; // 31 DEC 1805 = 10 NIVO 0014
    }

    /**
     * What is the lowest Julian day number that can be converted into this calendar.
     */
    public function jdStart(): int
    {
        return 2375840; // 22 SEP 1792 = 01 VEND 0001
    }

    /**
     * Convert a Julian day number into a year/month/day.
     *
     * @return int[]
     */
    public function jdToYmd(int $julian_day): array
    {
        $year  = (int) (($julian_day - 2375109) * 4 / 1461) - 1;
        $month = (int) (($julian_day - 2375475 - $year * 365 - (int) ($year / 4)) / 30) + 1;
        $day   = $julian_day - 2375444 - $month * 30 - $year * 365 - (int) ($year / 4);

        return array($year, $month, $day);
    }

    /**
     * Determine the number of months in a year (if given),
     * or the maximum number of months in any year.
     */
    public function monthsInYear(?int $year = null): int
    {
        return 13;
    }

    /**
     * Convert a year/month/day to a Julian day number.
     */
    public function ymdToJd(int $year, int $month, int $day): int
    {
        if ($month < 1 || $month > $this->monthsInYear()) {
            throw new InvalidArgumentException('Month ' . $month . ' is invalid for this calendar');
        }

        return 2375444 + $day + $month * 30 + $year * 365 + (int) ($year / 4);
    }
}
