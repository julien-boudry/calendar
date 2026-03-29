<?php declare(strict_types=1);

/**
 * Test harness for the class Shim.
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

use PHPUnit\Framework\TestCase;

class ShimTest extends TestCase
{
    // Use this many random dates to test date conversion functions.
    public const ITERATIONS = 512;
    /**
     * To iterate over large ranges of test data, use a prime-number interval to
     * avoid any synchronisation problems.
     */
    public const LARGE_PRIME = 235741;

    public static function setUpBeforeClass(): void
    {
        // Sync the C library timezone with PHP's timezone so that native
        // ext/calendar functions (which use the C library's mktime/localtime)
        // match PHP's date/mktime functions.
        putenv('TZ=' . date_default_timezone_get());
    }

    /**
     * Test that the shim defines all the necessary constants.
     *
     * @coversNothing
     *
     * @return void
     */
    public function testConstantsExist(): void
    {
        $this->assertTrue(\defined('CAL_GREGORIAN'));
        $this->assertTrue(\defined('CAL_JULIAN'));
        $this->assertTrue(\defined('CAL_JEWISH'));
        $this->assertTrue(\defined('CAL_FRENCH'));
        $this->assertTrue(\defined('CAL_NUM_CALS'));
        $this->assertTrue(\defined('CAL_DOW_DAYNO'));
        $this->assertTrue(\defined('CAL_DOW_SHORT'));
        $this->assertTrue(\defined('CAL_DOW_LONG'));
        $this->assertTrue(\defined('CAL_MONTH_GREGORIAN_SHORT'));
        $this->assertTrue(\defined('CAL_MONTH_GREGORIAN_LONG'));
        $this->assertTrue(\defined('CAL_MONTH_JULIAN_SHORT'));
        $this->assertTrue(\defined('CAL_MONTH_JULIAN_LONG'));
        $this->assertTrue(\defined('CAL_MONTH_JEWISH'));
        $this->assertTrue(\defined('CAL_MONTH_FRENCH'));
        $this->assertTrue(\defined('CAL_EASTER_DEFAULT'));
        $this->assertTrue(\defined('CAL_EASTER_ROMAN'));
        $this->assertTrue(\defined('CAL_EASTER_ALWAYS_GREGORIAN'));
        $this->assertTrue(\defined('CAL_EASTER_ALWAYS_JULIAN'));
        $this->assertTrue(\defined('CAL_JEWISH_ADD_ALAFIM_GERESH'));
        $this->assertTrue(\defined('CAL_JEWISH_ADD_ALAFIM'));
        $this->assertTrue(\defined('CAL_JEWISH_ADD_GERESHAYIM'));

        $this->assertSame(0, \CAL_GREGORIAN);
        $this->assertSame(1, \CAL_JULIAN);
        $this->assertSame(2, \CAL_JEWISH);
        $this->assertSame(3, \CAL_FRENCH);
        $this->assertSame(4, \CAL_NUM_CALS);
        $this->assertSame(0, \CAL_DOW_DAYNO);
        $this->assertSame(2, \CAL_DOW_SHORT);
        $this->assertSame(1, \CAL_DOW_LONG);
        $this->assertSame(0, \CAL_MONTH_GREGORIAN_SHORT);
        $this->assertSame(1, \CAL_MONTH_GREGORIAN_LONG);
        $this->assertSame(2, \CAL_MONTH_JULIAN_SHORT);
        $this->assertSame(3, \CAL_MONTH_JULIAN_LONG);
        $this->assertSame(4, \CAL_MONTH_JEWISH);
        $this->assertSame(5, \CAL_MONTH_FRENCH);
        $this->assertSame(0, \CAL_EASTER_DEFAULT);
        $this->assertSame(1, \CAL_EASTER_ROMAN);
        $this->assertSame(2, \CAL_EASTER_ALWAYS_GREGORIAN);
        $this->assertSame(3, \CAL_EASTER_ALWAYS_JULIAN);
        $this->assertSame(2, \CAL_JEWISH_ADD_ALAFIM_GERESH);
        $this->assertSame(4, \CAL_JEWISH_ADD_ALAFIM);
        $this->assertSame(8, \CAL_JEWISH_ADD_GERESHAYIM);
    }

    /**
     * Test that the shim defines all the necessary functions.
     *
     * @coversNothing
     *
     * @return void
     */
    public function testFunctionsExist(): void
    {
        $this->assertTrue(\function_exists('cal_days_in_month'));
        $this->assertTrue(\function_exists('cal_from_jd'));
        $this->assertTrue(\function_exists('cal_info'));
        $this->assertTrue(\function_exists('easter_date'));
        $this->assertTrue(\function_exists('easter_days'));
        $this->assertTrue(\function_exists('FrenchToJD'));
        $this->assertTrue(\function_exists('GregorianToJD'));
        $this->assertTrue(\function_exists('JDDayOfWeek'));
        $this->assertTrue(\function_exists('JDMonthName'));
        $this->assertTrue(\function_exists('JDToFrench'));
        $this->assertTrue(\function_exists('JDToGregorian'));
        $this->assertTrue(\function_exists('jdtojewish'));
        $this->assertTrue(\function_exists('JDToJulian'));
        $this->assertTrue(\function_exists('jdtounix'));
        $this->assertTrue(\function_exists('JewishToJD'));
        $this->assertTrue(\function_exists('JulianToJD'));
        $this->assertTrue(\function_exists('unixtojd'));
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthFrench(): void
    {
        foreach ([3, 4] as $year) {
            foreach ([1, 12, 13] as $month) {
                $this->assertSame(Shim::calDaysInMonth(\CAL_FRENCH, $month, $year), cal_days_in_month(\CAL_FRENCH, $month, $year));
            }
        }
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchBug67976(): void
    {
        $this->assertSame(Shim::calDaysInMonth(\CAL_FRENCH, 13, 14), cal_days_in_month(\CAL_FRENCH, 13, 14));
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchInvalidMonth1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_FRENCH, 14, 10);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchInvalidMonth2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_FRENCH, 14, 10);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchZeroYear1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_FRENCH, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchZeroYear2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_FRENCH, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchNegativeYear1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_FRENCH, 1, -1);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchNegativeYear2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_FRENCH, 1, -1);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchHighYear1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_FRENCH, 1, 15);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthFrenchHighYear2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_FRENCH, 1, 15);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @large
     *
     * @return void
     */
    public function testCalDaysInMonthGregorian(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $year = mt_rand(-4713, 9999);
            $month = mt_rand(1, 12);
            if ($year != 0) {
                $this->assertSame(Shim::calDaysInMonth(\CAL_GREGORIAN, $month, $year), cal_days_in_month(\CAL_GREGORIAN, $month, $year));
            }
        }
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthGregorianInvalidMonth1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_GREGORIAN, 13, 2014);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthGregorianInvalidMonth2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_GREGORIAN, 13, 2014);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthGregorianInvalidYear1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_GREGORIAN, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthGregorianInvalidYear2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_GREGORIAN, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @large
     *
     * @return void
     */
    public function testCalDaysInMonthJulian(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $year = mt_rand(-4713, 9999);
            $month = mt_rand(1, 12);
            if ($year != 0) {
                $this->assertSame(Shim::calDaysInMonth(\CAL_GREGORIAN, $month, $year), cal_days_in_month(\CAL_GREGORIAN, $month, $year));
            }
        }
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @large
     *
     * @return void
     */
    public function testCalDaysInMonthJewish(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $year = mt_rand(1, 5999);
            $month = mt_rand(1, 13);
            $this->assertSame(Shim::calDaysInMonth(\CAL_JEWISH, $month, $year), cal_days_in_month(\CAL_JEWISH, $month, $year));
        }
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthJewishInvalidMonth1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_JEWISH, 14, 2014);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthJewishInvalidMonth2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_JEWISH, 14, 2014);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthJewishInvalidYear1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_JEWISH, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthJewishInvalidYear2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_JEWISH, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthJulianInvalidMonth1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_JULIAN, 13, 2014);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthJulianInvalidMonth2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_JULIAN, 13, 2014);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthJulianInvalidYear1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        Shim::calDaysInMonth(\CAL_JULIAN, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthJulianInvalidYear2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Invalid date');

        cal_days_in_month(\CAL_JULIAN, 1, 0);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calDaysInMonth
     *
     * @return void
     */
    public function testCalDaysInMonthInvalidCalendar1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_days_in_month(): Argument #1 ($calendar) must be a valid calendar ID');

        Shim::calDaysInMonth(999, 1, 1);
    }

    /**
     * Test the implementation of Shim::calDaysInMonth() against cal_days_in_month().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalDaysInMonthInvalidCalendar2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_days_in_month(): Argument #1 ($calendar) must be a valid calendar ID');

        cal_days_in_month(999, 1, 1);
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     *
     * @return void
     */
    public function testCalFromJdFrench(): void
    {
        // 0/0/0
        $this->assertSame(cal_from_jd(2375839, \CAL_FRENCH), Shim::calFromJd(2375839, \CAL_FRENCH));
        // 1/1/1
        $this->assertSame(cal_from_jd(2375840, \CAL_FRENCH), Shim::calFromJd(2375840, \CAL_FRENCH));
        // 13/5/14
        $this->assertSame(cal_from_jd(2380952, \CAL_FRENCH), Shim::calFromJd(2380952, \CAL_FRENCH));
        // 0/0/0
        $this->assertSame(cal_from_jd(2380953, \CAL_FRENCH), Shim::calFromJd(2380953, \CAL_FRENCH));
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     *
     * @return void
     */
    public function testCalFromJdGregorian(): void
    {
        // 0/0/0
        $this->assertSame(cal_from_jd(0, \CAL_GREGORIAN), Shim::calFromJd(0, \CAL_GREGORIAN));
        // 11/25/-4714
        $this->assertSame(cal_from_jd(1, \CAL_GREGORIAN), Shim::calFromJd(1, \CAL_GREGORIAN));
        // 12/31/-1
        $this->assertSame(cal_from_jd(1721425, \CAL_GREGORIAN), Shim::calFromJd(1721425, \CAL_GREGORIAN));
        // 1/1/1
        $this->assertSame(cal_from_jd(1721426, \CAL_GREGORIAN), Shim::calFromJd(1721426, \CAL_GREGORIAN));
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     *
     * @return void
     */
    public function testCalFromJdJewish(): void
    {
        // 0/0/0
        $this->assertSame(cal_from_jd(347997, \CAL_JEWISH), Shim::calFromJd(347997, \CAL_JEWISH));
        // 1/1/1
        $this->assertSame(cal_from_jd(347998, \CAL_JEWISH), Shim::calFromJd(347998, \CAL_JEWISH));
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     *
     * @return void
     */
    public function testCalFromJdJulian(): void
    {
        // 0/0/0
        $this->assertSame(cal_from_jd(0, \CAL_JULIAN), Shim::calFromJd(0, \CAL_JULIAN));
        // 1/2/-4713
        $this->assertSame(cal_from_jd(1, \CAL_JULIAN), Shim::calFromJd(1, \CAL_JULIAN));
        // 12/31/-1
        $this->assertSame(cal_from_jd(1721423, \CAL_JULIAN), Shim::calFromJd(1721423, \CAL_JULIAN));
        // 1/1/1
        $this->assertSame(cal_from_jd(1721424, \CAL_JULIAN), Shim::calFromJd(1721424, \CAL_JULIAN));
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     *
     * @return void
     */
    public function testCalFromJdInvalidCalendar1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_from_jd(): Argument #2 ($calendar) must be a valid calendar ID');

        Shim::calFromJd(2345678, 999);
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalFromJdInvalidCalendar2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_from_jd(): Argument #2 ($calendar) must be a valid calendar ID');

        cal_from_jd(2345678, 999);
    }

    /**
     * Test the implementation of Shim::calInfo() against cal_info().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calInfo
     *
     * @return void
     */
    public function testCalInfo(): void
    {
        $this->assertSame(Shim::calInfo(\CAL_FRENCH), cal_info(\CAL_FRENCH));
        $this->assertSame(Shim::calInfo(\CAL_GREGORIAN), cal_info(\CAL_GREGORIAN));
        $this->assertSame(Shim::calInfo(\CAL_JEWISH), cal_info(\CAL_JEWISH));
        $this->assertSame(Shim::calInfo(\CAL_JULIAN), cal_info(\CAL_JULIAN));
    }

    /**
     * Test the implementation of Shim::calInfo() against cal_info().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calInfo
     *
     * @return void
     */
    public function testCalInfoAll(): void
    {
        $this->assertSame(Shim::calInfo(-1), cal_info(-1));
    }

    /**
     * Test the implementation of Shim::calInfo() against cal_info().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calInfo
     *
     * @return void
     */
    public function testCalInfoInvalid1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_info(): Argument #1 ($calendar) must be a valid calendar ID');

        Shim::calInfo(999);
    }

    /**
     * Test the implementation of Shim::calInfo() against cal_info().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalInfoInvalid2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_info(): Argument #1 ($calendar) must be a valid calendar ID');

        cal_info(999);
    }

    /**
     * Test the implementation of Shim::calToJd() against cal_to_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calToJd
     *
     * @return void
     */
    public function testCalToJdInvalidCalendar1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_to_jd(): Argument #1 ($calendar) must be a valid calendar ID');

        Shim::calToJd(999, 1, 1, 1);
    }

    /**
     * Test the implementation of Shim::calToJd() against cal_to_jd().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testCalToJdInvalidCalendar2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('cal_to_jd(): Argument #1 ($calendar) must be a valid calendar ID');

        cal_to_jd(999, 1, 1, 1);
    }

    /**
     * Test the implementation of Shim::easterDate() against easter_date().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::easterDate
     *
     * @return void
     */
    public function testEasterDate(): void
    {
        $this->assertSame(Shim::easterDate(2013), easter_date(2013));
        $this->assertSame(Shim::easterDate(2014), easter_date(2014));
    }

    /**
     * Test the implementation of Shim::easterDate() against easter_date().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::easterDate
     *
     * @return void
     */
    public function testEasterDateHighYear1(): void
    {
        $this->assertSame(Shim::easterDate(2038), easter_date(2038));
    }

    /**
     * Test the implementation of Shim::easterDate() against easter_date().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testEasterDateHighYear2(): void
    {
        $this->assertSame(Shim::easterDate(2050), easter_date(2050));
    }

    /**
     * Test the implementation of Shim::easterDate() against easter_date().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::easterDate
     *
     * @return void
     */
    public function testEasterDateLowYear1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('easter_date(): Argument #1 ($year) must be a year after 1970 (inclusive)');

        Shim::easterDate(1969);
    }

    /**
     * Test the implementation of Shim::easterDate() against easter_date().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testEasterDateLowYear2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('easter_date(): Argument #1 ($year) must be a year after 1970 (inclusive)');

        easter_date(1969);
    }

    /**
     * Test the implementation of Shim::easterDays() against easter_days().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::easterDays
     *
     * @return void
     */
    public function testEasterDays(): void
    {
        foreach ([1751, 1752, 1753, 1581, 1582, 1583] as $year) {
            $this->assertSame(Shim::easterDays($year, 999), easter_days($year, 999));
            $this->assertSame(Shim::easterDays($year, \CAL_EASTER_DEFAULT), easter_days($year, \CAL_EASTER_DEFAULT));
            $this->assertSame(Shim::easterDays($year, \CAL_EASTER_ROMAN), easter_days($year, \CAL_EASTER_ROMAN));
            $this->assertSame(Shim::easterDays($year, \CAL_EASTER_ALWAYS_GREGORIAN), easter_days($year, \CAL_EASTER_ALWAYS_GREGORIAN));
            $this->assertSame(Shim::easterDays($year, \CAL_EASTER_ALWAYS_JULIAN), easter_days($year, \CAL_EASTER_ALWAYS_JULIAN));
        }
    }

    /**
     * Test the implementation of Shim::frenchToJd() against FrenchToJd()
     * Test the implementation of Shim::calToJd() against cal_to_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calToJd
     * @covers \Fisharebest\ExtCalendar\Shim::frenchToJd
     *
     * @large
     *
     * @return void
     */
    public function testFrenchToJd(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $year = mt_rand(1, 14);
            $month = mt_rand(1, 13);
            $day = mt_rand(1, $month == 13 ? 6 : 28);

            $this->assertSame(Shim::frenchToJd($month, $day, $year), frenchtojd($month, $day, $year));
            $this->assertSame(Shim::calToJd(\CAL_FRENCH, $month, $day, $year), cal_to_jd(\CAL_FRENCH, $month, $day, $year));
        }
    }

    /**
     * Test the implementation of Shim::frenchToJd() against FrenchToJd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::frenchToJd
     *
     * @return void
     */
    public function testFrenchToJdOutOfRange(): void
    {
        $this->assertSame(Shim::frenchToJd(1, 1, 0), frenchtojd(1, 1, 0));
    }

    /**
     * Test the implementation of Shim::gregorianToJd() against GregorianToJD()
     * Test the implementation of Shim::calToJd() against cal_to_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calToJd
     * @covers \Fisharebest\ExtCalendar\Shim::gregorianToJd
     *
     * @large
     *
     * @return void
     */
    public function testGregorianToJD(): void
    {
        $this->assertSame(Shim::gregorianToJd(1, 1, 0), gregoriantojd(1, 1, 0));

        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $year = mt_rand(-4713, 9999);
            $month = mt_rand(1, 12);
            $day = mt_rand(1, 30);

            $this->assertSame(Shim::gregorianToJd($month, $day, $year), gregoriantojd($month, $day, $year));
            $this->assertSame(Shim::calToJd(\CAL_GREGORIAN, $month, $day, $year), cal_to_jd(\CAL_GREGORIAN, $month, $day, $year));
        }
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @link https://bugs.php.net/bug.php?id=67960
     *
     * @return void
     */
    public function testJdDayOfWeekSunday(): void
    {
        $julian_day = gregoriantojd(8, 31, 2014);

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), 'Sunday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), 'Sun');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO), 0);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG), 'Sunday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT), 'Sun');

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 0), jddayofweek($julian_day, 0));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), jddayofweek($julian_day, 1));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), jddayofweek($julian_day, 2));
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @link https://bugs.php.net/bug.php?id=67960
     *
     * @return void
     */
    public function testJdDayOfWeekMonday(): void
    {
        $julian_day = gregoriantojd(9, 1, 2014); // 2456902

        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO), 1);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG), 'Monday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT), 'Mon');

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 0), jddayofweek($julian_day, 0));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), jddayofweek($julian_day, 1));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), jddayofweek($julian_day, 2));
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @link https://bugs.php.net/bug.php?id=67960
     *
     * @return void
     */
    public function testJdDayOfWeekTuesday(): void
    {
        $julian_day = gregoriantojd(9, 2, 2014); // 2456903

        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO), 2);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG), 'Tuesday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT), 'Tue');

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 0), jddayofweek($julian_day, 0));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), jddayofweek($julian_day, 1));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), jddayofweek($julian_day, 2));
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @link https://bugs.php.net/bug.php?id=67960
     *
     * @return void
     */
    public function testJdDayOfWeekWednesday(): void
    {
        $julian_day = gregoriantojd(9, 3, 2014); // 2456904

        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO), 3);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG), 'Wednesday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT), 'Wed');

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 0), jddayofweek($julian_day, 0));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), jddayofweek($julian_day, 1));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), jddayofweek($julian_day, 2));
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @link https://bugs.php.net/bug.php?id=67960
     *
     * @return void
     */
    public function testJdDayOfWeekThursday(): void
    {
        $julian_day = gregoriantojd(9, 4, 2014); // 2456905

        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO), 4);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG), 'Thursday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT), 'Thu');

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 0), jddayofweek($julian_day, 0));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), jddayofweek($julian_day, 1));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), jddayofweek($julian_day, 2));
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @link https://bugs.php.net/bug.php?id=67960
     *
     * @return void
     */
    public function testJdDayOfWeekFriday(): void
    {
        $julian_day = gregoriantojd(9, 5, 2014); // 2456906

        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO), 5);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG), 'Friday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT), 'Fri');

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 0), jddayofweek($julian_day, 0));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), jddayofweek($julian_day, 1));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), jddayofweek($julian_day, 2));
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @link https://bugs.php.net/bug.php?id=67960
     *
     * @return void
     */
    public function testJdDayOfWeekSaturday(): void
    {
        $julian_day = gregoriantojd(9, 6, 2014); // 2456907

        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO), 6);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG), 'Saturday');
        $this->assertSame(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT), 'Sat');

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 0), jddayofweek($julian_day, 0));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 1), jddayofweek($julian_day, 1));
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 2), jddayofweek($julian_day, 2));
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @return void
     */
    public function testJdDayOfWeekNegative(): void
    {
        $this->assertSame(Shim::jdDayOfWeek(-2, 0), 6);
    }

    /**
     * Test the implementation of Shim::jdDayOfWeek() against JDDayOfWeek().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdDayOfWeek
     *
     * @return void
     */
    public function testJdDayOfWeekInvalidMode(): void
    {
        $julian_day = gregoriantojd(8, 31, 2014); // 2456901

        $this->assertSame(Shim::jdDayOfWeek($julian_day, 999), 0);
        $this->assertSame(Shim::jdDayOfWeek($julian_day, 999), jddayofweek($julian_day, 999));
    }

    /**
     * Test the implementation of Shim::jdMonthName() against JDMonthName().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdMonthName
     *
     * @return void
     */
    public function testJdMonthNameFrench(): void
    {
        for ($month = 1; $month <= 13; ++$month) {
            $julian_day = frenchtojd($month, 1, 10);
            $this->assertSame(Shim::jdMonthName($julian_day, \CAL_MONTH_FRENCH), jdmonthname($julian_day, \CAL_MONTH_FRENCH));
        }
    }

    /**
     * Test the implementation of Shim::jdMonthName() against JDMonthName().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdMonthName
     *
     * @return void
     */
    public function testJdMonthNameGregorian(): void
    {
        for ($month = 1; $month <= 12; ++$month) {
            $julian_day = gregoriantojd($month, 1, 2014);
            $this->assertSame(Shim::jdMonthName($julian_day, \CAL_MONTH_GREGORIAN_LONG), jdmonthname($julian_day, \CAL_MONTH_GREGORIAN_LONG));
            $this->assertSame(Shim::jdMonthName($julian_day, \CAL_MONTH_GREGORIAN_SHORT), jdmonthname($julian_day, \CAL_MONTH_GREGORIAN_SHORT));
        }
    }

    /**
     * Test the implementation of Shim::jdMonthName() against JDMonthName().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdMonthName
     *
     * @return void
     */
    public function testJdMonthNameJewish(): void
    {
        // Both leap and non-leap years
        foreach ([5000, 5001] as $year) {
            for ($month = 1; $month <= 13; ++$month) {
                $julian_day = jewishtojd($month, 1, $year);
                $this->assertSame(Shim::jdMonthName($julian_day, \CAL_MONTH_JEWISH), jdmonthname($julian_day, \CAL_MONTH_JEWISH));
            }
        }
    }

    /**
     * Test the implementation of Shim::jdMonthName() against JDMonthName().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdMonthName
     *
     * @return void
     */
    public function testJdMonthNameJulian(): void
    {
        for ($month = 1; $month <= 12; ++$month) {
            $julian_day = juliantojd($month, 1, 2014);
            $this->assertSame(Shim::jdMonthName($julian_day, \CAL_MONTH_JULIAN_LONG), jdmonthname($julian_day, \CAL_MONTH_JULIAN_LONG));
            $this->assertSame(Shim::jdMonthName($julian_day, \CAL_MONTH_JULIAN_SHORT), jdmonthname($julian_day, \CAL_MONTH_JULIAN_SHORT));
        }
    }

    /**
     * Test the implementation of Shim::jdMonthName() against JDMonthName().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdMonthName
     *
     * @return void
     */
    public function testJdMonthNameInvalidMode(): void
    {
        $julian_day = juliantojd(1, 1, 2014);

        $this->assertSame(Shim::jdMonthName($julian_day, 999), 'Jan');
        $this->assertSame(Shim::jdMonthName($julian_day, 999), jdmonthname($julian_day, 999));
    }

    /**
     * Test the implementation of Shim::jdToFrench() against JDToFrench()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     * @covers \Fisharebest\ExtCalendar\Shim::jdToFrench
     *
     * @large
     *
     * @return void
     */
    public function testJdToFrench(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $julian_day = mt_rand(gregoriantojd(9, 22, 1792), gregoriantojd(9, 22, 1806));
            $this->assertSame(Shim::jdToFrench($julian_day), jdtofrench($julian_day));
            $this->assertSame(Shim::calFromJd($julian_day, \CAL_FRENCH), cal_from_jd($julian_day, \CAL_FRENCH));
        }
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd()
     * Test the implementation of Shim::jdToFrench() against JDToFrench().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToFrench
     *
     * @return void
     */
    public function testJdToFrenchEdgeCases(): void
    {
        $this->assertSame(cal_from_jd(2375839, \CAL_FRENCH), Shim::calFromJd(2375839, \CAL_FRENCH));
        $this->assertSame('0/0/0', Shim::jdToFrench(2375839));
        $this->assertSame('0/0/0', jdtofrench(2375839));

        $this->assertSame(cal_from_jd(2375840, \CAL_FRENCH), Shim::calFromJd(2375840, \CAL_FRENCH));
        $this->assertSame('1/1/1', Shim::jdToFrench(2375840));
        $this->assertSame('1/1/1', jdtofrench(2375840));

        $this->assertSame(cal_from_jd(2380952, \CAL_FRENCH), Shim::calFromJd(2380952, \CAL_FRENCH));
        $this->assertSame('13/5/14', Shim::jdToFrench(2380952));
        $this->assertSame('13/5/14', jdtofrench(2380952));

        $this->assertSame(cal_from_jd(2380953, \CAL_FRENCH), Shim::calFromJd(2380953, \CAL_FRENCH));
        $this->assertSame('0/0/0', Shim::jdToFrench(2380953));
        $this->assertSame('0/0/0', jdtofrench(2380953));
    }

    /**
     * Test the implementation of Shim::jdToFrench() against JDToFrench()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     * @covers \Fisharebest\ExtCalendar\Shim::jdToFrench
     *
     * @return void
     */
    public function testJdToFrenchOutOfRange(): void
    {
        $julian_day_low = 2375840 - 1;
        $julian_day_high = 2380953 + 2;

        $this->assertSame(Shim::jdToFrench($julian_day_low), jdtofrench($julian_day_low));
        $this->assertSame(Shim::jdToFrench($julian_day_high), jdtofrench($julian_day_high));
        $this->assertSame(Shim::calFromJd($julian_day_low, \CAL_FRENCH), cal_from_jd($julian_day_low, \CAL_FRENCH));
        $this->assertSame(Shim::calFromJd($julian_day_high, \CAL_FRENCH), cal_from_jd($julian_day_high, \CAL_FRENCH));
    }

    /**
     * Test the implementation of Shim::jdToGregorian() against JDToGregorian()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     * @covers \Fisharebest\ExtCalendar\Shim::jdToGregorian
     *
     * @large
     *
     * @return void
     */
    public function testJdToGregorian(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $julian_day = mt_rand(1, gregoriantojd(12, 31, 9999));

            $this->assertSame(Shim::jdToGregorian($julian_day), jdtogregorian($julian_day));
            $this->assertSame(Shim::calFromJd($julian_day, \CAL_GREGORIAN), cal_from_jd($julian_day, \CAL_GREGORIAN));
        }
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd()
     * Test the implementation of Shim::jdToGregorian() against JDToGregorian().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToGregorian
     *
     * @return void
     */
    public function testJdToGregorianEdgeCases(): void
    {
        $MAX_JD = \PHP_INT_SIZE == 4 ? 536838866 : 784350656097;

        $this->assertSame(cal_from_jd(-1, \CAL_GREGORIAN), Shim::calFromJd(-1, \CAL_GREGORIAN));
        $this->assertSame('0/0/0', Shim::jdToGregorian(-1));
        $this->assertSame('0/0/0', jdtogregorian(-1));

        $this->assertSame(cal_from_jd(0, \CAL_GREGORIAN), Shim::calFromJd(0, \CAL_GREGORIAN));
        $this->assertSame('0/0/0', Shim::jdToGregorian(0));
        $this->assertSame('0/0/0', jdtogregorian(0));

        $this->assertSame(cal_from_jd(1, \CAL_GREGORIAN), Shim::calFromJd(1, \CAL_GREGORIAN));
        $this->assertSame('11/25/-4714', Shim::jdToGregorian(1));
        $this->assertSame('11/25/-4714', jdtogregorian(1));

        // PHP overflows and gives bogus results
        $this->assertSame(jdtogregorian($MAX_JD), Shim::jdToGregorian($MAX_JD));
    }

    /**
     * Test the implementation of Shim::jdToJewish() against jdtojewish()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     * @covers \Fisharebest\ExtCalendar\Shim::jdToJewish
     *
     * @large
     *
     * @return void
     */
    public function testJdToJewish(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $julian_day = mt_rand(712878, 2539109);

            $this->assertSame(Shim::jdToJewish($julian_day, false, 0), jdtojewish($julian_day));
            $this->assertSame(Shim::calFromJd($julian_day, \CAL_JEWISH), cal_from_jd($julian_day, \CAL_JEWISH));
        }
    }

    /**
     * Test the implementation of Shim::jdToJewish() against jdtojewish()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToJewish
     *
     * @large
     *
     * @return void
     */
    public function testJdToJewishEdgeCases(): void
    {
        $this->assertSame(cal_from_jd(347997, \CAL_JEWISH), Shim::calFromJd(347997, \CAL_JEWISH));
        $this->assertSame('0/0/0', Shim::jdToJewish(347997, false, 0));
        $this->assertSame('0/0/0', jdtojewish(347997));

        $this->assertSame(cal_from_jd(347998, \CAL_JEWISH), Shim::calFromJd(347998, \CAL_JEWISH));
        $this->assertSame('1/1/1', Shim::jdToJewish(347998, false, 0));
        $this->assertSame('1/1/1', jdtojewish(347998));

        $this->assertSame(cal_from_jd(4000075, \CAL_JEWISH), Shim::calFromJd(4000075, \CAL_JEWISH));
        $this->assertSame('13/29/9999', Shim::jdToJewish(4000075, false, 0));
        $this->assertSame('13/29/9999', jdtojewish(4000075));

        $this->assertSame(cal_from_jd(4000076, \CAL_JEWISH), Shim::calFromJd(4000076, \CAL_JEWISH));
        $this->assertSame('1/1/10000', Shim::jdToJewish(4000076, false, 0));
        $this->assertSame('1/1/10000', jdtojewish(4000076));

        $this->assertSame(cal_from_jd(324542846, \CAL_JEWISH), Shim::calFromJd(324542846, \CAL_JEWISH));
        $this->assertSame('12/13/887605', Shim::jdToJewish(324542846, false, 0));
        $this->assertSame('12/13/887605', jdtojewish(324542846));

        $this->assertSame(cal_from_jd(324542847, \CAL_JEWISH), Shim::calFromJd(324542847, \CAL_JEWISH));
        $this->assertSame('0/0/0', Shim::jdToJewish(324542847, false, 0));
        $this->assertSame('0/0/0', jdtojewish(324542847));
    }

    /**
     * Test the implementation of Shim::jdToJewish() against jdtojewish()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @large
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToJewish
     *
     * @return void
     */
    public function testJdToJewishHebrew(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $julian_day = mt_rand(712878, 2539109);
            $flags = mt_rand(0, 7);
            $this->assertSame(Shim::jdToJewish($julian_day, true, $flags), jdtojewish($julian_day, true, $flags));
        }
    }

    /**
     * Test the implementation of Shim::jdToJewish() against jdtojewish()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToJewish
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\WithoutErrorHandler]
    public function testJdToJewishHebrewOutOfRangeLow1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Year out of range (0-9999)');

        $julian_day = jewishtojd(1, 1, 1) - 1;
        Shim::jdToJewish($julian_day, true, 0);
    }

    /**
     * Test the implementation of Shim::jdToJewish() against jdtojewish()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @coversNothing
     *
     * @return void
     */
    public function testJdToJewishHebrewOutOfRangeLow2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Year out of range (0-9999)');

        $julian_day = jewishtojd(1, 1, 1) - 1;
        jdtojewish($julian_day, true, 0);
    }

    /**
     * Test the implementation of Shim::jdToJewish() against jdtojewish()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToJewish
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\WithoutErrorHandler]
    public function testJdToJewishHebrewOutOfRangeHigh1(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Year out of range (0-9999)');

        Shim::jdToJewish(4000076, true, 0);
    }

    /**
     * Test the implementation of Shim::jdToJewish() against jdtojewish()
     * Test the implementation of Shim::calFromJd() against cal_from_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jewishToJd
     *
     * @return void
     */
    public function testJdToJewishHebrewOutOfRangeHigh2(): void
    {
        $this->expectException('ValueError');
        $this->expectExceptionMessage('Year out of range (0-9999)');

        jdtojewish(4000076, true, 0);
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd()
     * Test the implementation of Shim::jdToJulian() against JDToJulian().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calFromJd
     * @covers \Fisharebest\ExtCalendar\Shim::jdToJulian
     *
     * @return void
     */
    public function testJdToJulian(): void
    {
        $start_jd = juliantojd(1, 1, -2500);
        $end_jd = juliantojd(1, 1, 2500);

        for ($julian_day = $start_jd; $julian_day <= $end_jd; $julian_day += static::LARGE_PRIME) {
            $this->assertSame(Shim::calFromJd($julian_day, \CAL_JULIAN), cal_from_jd($julian_day, \CAL_JULIAN));
            $this->assertSame(Shim::jdToJulian($julian_day), jdtojulian($julian_day));
        }
    }

    /**
     * Test the implementation of Shim::calFromJd() against cal_from_jd()
     * Test the implementation of Shim::jdToJulian() against JDToJulian().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToJulian
     *
     * @return void
     */
    public function testJdToJulianEdgeCases(): void
    {
        $MAX_JD = \PHP_INT_SIZE == 4 ? 536838829 : 784368370349;

        $this->assertSame(cal_from_jd(-1, \CAL_JULIAN), Shim::calFromJd(-1, \CAL_JULIAN));
        $this->assertSame('0/0/0', Shim::jdToJulian(-1));
        $this->assertSame('0/0/0', jdtojulian(-1));

        $this->assertSame(cal_from_jd(0, \CAL_JULIAN), Shim::calFromJd(0, \CAL_JULIAN));
        $this->assertSame('0/0/0', Shim::jdToJulian(0));
        $this->assertSame('0/0/0', jdtojulian(0));

        $this->assertSame(cal_from_jd(1, \CAL_JULIAN), Shim::calFromJd(1, \CAL_JULIAN));
        $this->assertSame('1/2/-4713', Shim::jdToJulian(1));
        $this->assertSame('1/2/-4713', jdtojulian(1));

        // PHP overflows and gives bogus results
        //$this->assertSame(cal_from_jd($MAX_JD, CAL_JULIAN), Shim::calFromJd($MAX_JD, CAL_JULIAN));
        //$this->assertSame(JDToJulian($MAX_JD), Shim::jdToJulian($MAX_JD));
        $this->assertNotSame('0/0/0', jdtojulian($MAX_JD));
        $this->assertNotSame('0/0/0', Shim::jdToJulian($MAX_JD));

        $this->assertSame(cal_from_jd($MAX_JD + 1, \CAL_JULIAN), Shim::calFromJd($MAX_JD + 1, \CAL_JULIAN));
        $this->assertSame('0/0/0', Shim::jdToJulian($MAX_JD + 1));
        $this->assertSame('0/0/0', jdtojulian($MAX_JD + 1));
    }

    /**
     * Test the implementation of Shim::jdToUnix() against jdtojunix().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToUnix
     *
     * @return void
     */
    public function testJdToUnix(): void
    {
        $julian_day_start = gregoriantojd(1, 1, 1980);
        $julian_day_end = gregoriantojd(12, 31, 2030);

        for ($julian_day = $julian_day_start; $julian_day <= $julian_day_end; $julian_day += 23) {
            $this->assertSame(Shim::jdToUnix($julian_day), jdtounix($julian_day));
        }
    }

    /**
     * Test the implementation of Shim::jdToUnix() against jdtojunix().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToUnix
     * @covers \Fisharebest\ExtCalendar\Shim::jdToUnixUpperLimit
     *
     * @return void
     */
    public function testJdToUnixEdgeCasesLowerLimit(): void
    {
        $lower_limit = 2440588;

        $this->assertIsInt(Shim::jdToUnix($lower_limit));
        $this->assertSame(Shim::jdToUnix($lower_limit), jdtounix($lower_limit));

        $this->expectException('ValueError');
        $this->expectExceptionMessage('jday must be between 2440588 and ' . Shim::jdToUnixUpperLimit());

        Shim::jdToUnix($lower_limit - 1);
    }

    /**
     * Test the implementation of Shim::jdToUnix() against jdtojunix().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::jdToUnix
     * @covers \Fisharebest\ExtCalendar\Shim::jdToUnixUpperLimit
     *
     * @return void
     */
    public function testJdToUnixEdgeCasesUpperLimit(): void
    {
        $upper_limit = Shim::jdToUnixUpperLimit();

        $this->assertIsInt(Shim::jdToUnix($upper_limit));
        $this->assertSame(Shim::jdToUnix($upper_limit), jdtounix($upper_limit));

        $this->expectException('ValueError');
        $this->expectExceptionMessage('jday must be between 2440588 and ' . Shim::jdToUnixUpperLimit());

        Shim::jdToUnix($upper_limit + 1);
    }

    /**
     * Test the implementation of Shim::jewishToJD() against JewishToJD()
     * Test the implementation of Shim::calToJd() against cal_to_jd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calToJd
     * @covers \Fisharebest\ExtCalendar\Shim::jewishToJd
     *
     * @large
     *
     * @return void
     */
    public function testJewishToJd(): void
    {
        $this->assertSame(Shim::jewishToJD(1, 1, 0), jewishtojd(1, 1, 0));

        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $year = mt_rand(1, 5999);
            $month = mt_rand(1, 13);
            $day = mt_rand(1, 29);

            $this->assertSame(Shim::jewishToJD($month, $day, $year), jewishtojd($month, $day, $year));
            $this->assertSame(Shim::calToJd(\CAL_JEWISH, $month, $day, $year), cal_to_jd(\CAL_JEWISH, $month, $day, $year));
        }
    }

    /**
     * Test the implementation of Shim::julianToJd() against JulianToJd()
     * Test the implementation of Shim::calToJd() against cal_to_jd().
     *
     * @large
     *
     * @covers \Fisharebest\ExtCalendar\Shim::calToJd
     * @covers \Fisharebest\ExtCalendar\Shim::julianToJd
     *
     * @return void
     */
    public function testJulianToJd(): void
    {
        $this->assertSame(Shim::julianToJd(1, 1, 0), juliantojd(1, 1, 0));

        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $year = mt_rand(-4713, 9999);
            $month = mt_rand(1, 12);
            $day = mt_rand(1, 30);

            $this->assertSame(Shim::julianToJd($month, $day, $year), juliantojd($month, $day, $year));
            $this->assertSame(Shim::calToJd(\CAL_JULIAN, $month, $day, $year), cal_to_jd(\CAL_JULIAN, $month, $day, $year));
        }
    }

    /**
     * Test the implementation of Shim::unixToJd() against unixtojd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::unixToJd
     *
     * @return void
     */
    public function testUnixToJd(): void
    {
        for ($n = 0; $n < static::ITERATIONS; ++$n) {
            $unix = mt_rand(1, 2147483647);
            $this->assertSame(Shim::unixToJd($unix), unixtojd($unix));
        }
    }

    /**
     * Test the implementation of Shim::unixToJd() against unixtojd().
     *
     * @covers \Fisharebest\ExtCalendar\Shim::unixToJd
     *
     * @return void
     */
    public function testUnixToJdEdgeCases(): void
    {
        $this->assertSame(Shim::unixToJd(2147483647), 2465443);
        $this->assertSame(Shim::unixToJd(2147483647), unixtojd(2147483647));

        $this->expectException('ValueError');
        $this->expectExceptionMessage('unixtojd(): Argument #1 ($timestamp) must be greater than or equal to 0');

        Shim::unixToJd(-1);
    }
}
