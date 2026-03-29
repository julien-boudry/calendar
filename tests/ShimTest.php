<?php declare(strict_types=1);
use Fisharebest\ExtCalendar\Shim;
use Tests\TestCase;

beforeAll(function (): void {
    // Sync the C library timezone with PHP's timezone so that native
    // ext/calendar functions (which use the C library's mktime/localtime)
    // match PHP's date/mktime functions.
    putenv('TZ=' . date_default_timezone_get());
});
test('constants exist', function (): void {
    expect(\defined('CAL_GREGORIAN'))->toBeTrue();
    expect(\defined('CAL_JULIAN'))->toBeTrue();
    expect(\defined('CAL_JEWISH'))->toBeTrue();
    expect(\defined('CAL_FRENCH'))->toBeTrue();
    expect(\defined('CAL_NUM_CALS'))->toBeTrue();
    expect(\defined('CAL_DOW_DAYNO'))->toBeTrue();
    expect(\defined('CAL_DOW_SHORT'))->toBeTrue();
    expect(\defined('CAL_DOW_LONG'))->toBeTrue();
    expect(\defined('CAL_MONTH_GREGORIAN_SHORT'))->toBeTrue();
    expect(\defined('CAL_MONTH_GREGORIAN_LONG'))->toBeTrue();
    expect(\defined('CAL_MONTH_JULIAN_SHORT'))->toBeTrue();
    expect(\defined('CAL_MONTH_JULIAN_LONG'))->toBeTrue();
    expect(\defined('CAL_MONTH_JEWISH'))->toBeTrue();
    expect(\defined('CAL_MONTH_FRENCH'))->toBeTrue();
    expect(\defined('CAL_EASTER_DEFAULT'))->toBeTrue();
    expect(\defined('CAL_EASTER_ROMAN'))->toBeTrue();
    expect(\defined('CAL_EASTER_ALWAYS_GREGORIAN'))->toBeTrue();
    expect(\defined('CAL_EASTER_ALWAYS_JULIAN'))->toBeTrue();
    expect(\defined('CAL_JEWISH_ADD_ALAFIM_GERESH'))->toBeTrue();
    expect(\defined('CAL_JEWISH_ADD_ALAFIM'))->toBeTrue();
    expect(\defined('CAL_JEWISH_ADD_GERESHAYIM'))->toBeTrue();

    expect(\CAL_GREGORIAN)->toBe(0);
    expect(\CAL_JULIAN)->toBe(1);
    expect(\CAL_JEWISH)->toBe(2);
    expect(\CAL_FRENCH)->toBe(3);
    expect(\CAL_NUM_CALS)->toBe(4);
    expect(\CAL_DOW_DAYNO)->toBe(0);
    expect(\CAL_DOW_SHORT)->toBe(2);
    expect(\CAL_DOW_LONG)->toBe(1);
    expect(\CAL_MONTH_GREGORIAN_SHORT)->toBe(0);
    expect(\CAL_MONTH_GREGORIAN_LONG)->toBe(1);
    expect(\CAL_MONTH_JULIAN_SHORT)->toBe(2);
    expect(\CAL_MONTH_JULIAN_LONG)->toBe(3);
    expect(\CAL_MONTH_JEWISH)->toBe(4);
    expect(\CAL_MONTH_FRENCH)->toBe(5);
    expect(\CAL_EASTER_DEFAULT)->toBe(0);
    expect(\CAL_EASTER_ROMAN)->toBe(1);
    expect(\CAL_EASTER_ALWAYS_GREGORIAN)->toBe(2);
    expect(\CAL_EASTER_ALWAYS_JULIAN)->toBe(3);
    expect(\CAL_JEWISH_ADD_ALAFIM_GERESH)->toBe(2);
    expect(\CAL_JEWISH_ADD_ALAFIM)->toBe(4);
    expect(\CAL_JEWISH_ADD_GERESHAYIM)->toBe(8);
});
test('functions exist', function (): void {
    expect(\function_exists('cal_days_in_month'))->toBeTrue();
    expect(\function_exists('cal_from_jd'))->toBeTrue();
    expect(\function_exists('cal_info'))->toBeTrue();
    expect(\function_exists('easter_date'))->toBeTrue();
    expect(\function_exists('easter_days'))->toBeTrue();
    expect(\function_exists('FrenchToJD'))->toBeTrue();
    expect(\function_exists('GregorianToJD'))->toBeTrue();
    expect(\function_exists('JDDayOfWeek'))->toBeTrue();
    expect(\function_exists('JDMonthName'))->toBeTrue();
    expect(\function_exists('JDToFrench'))->toBeTrue();
    expect(\function_exists('JDToGregorian'))->toBeTrue();
    expect(\function_exists('jdtojewish'))->toBeTrue();
    expect(\function_exists('JDToJulian'))->toBeTrue();
    expect(\function_exists('jdtounix'))->toBeTrue();
    expect(\function_exists('JewishToJD'))->toBeTrue();
    expect(\function_exists('JulianToJD'))->toBeTrue();
    expect(\function_exists('unixtojd'))->toBeTrue();
});
test('cal days in month french', function (): void {
    foreach ([3, 4] as $year) {
        foreach ([1, 12, 13] as $month) {
            expect(cal_days_in_month(\CAL_FRENCH, $month, $year))->toBe(Shim::calDaysInMonth(\CAL_FRENCH, $month, $year));
        }
    }
});
test('cal days in month french bug67976', function (): void {
    expect(cal_days_in_month(\CAL_FRENCH, 13, 14))->toBe(Shim::calDaysInMonth(\CAL_FRENCH, 13, 14));
});
test('cal days in month french invalid month1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_FRENCH, 14, 10);
});
test('cal days in month french invalid month2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_FRENCH, 14, 10);
});
test('cal days in month french zero year1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_FRENCH, 1, 0);
});
test('cal days in month french zero year2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_FRENCH, 1, 0);
});
test('cal days in month french negative year1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_FRENCH, 1, -1);
});
test('cal days in month french negative year2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_FRENCH, 1, -1);
});
test('cal days in month french high year1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_FRENCH, 1, 15);
});
test('cal days in month french high year2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_FRENCH, 1, 15);
});
test('cal days in month gregorian', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $year = $this->randomizer->getInt(-4713, 9999);
        $month = $this->randomizer->getInt(1, 12);
        if ($year != 0) {
            expect(cal_days_in_month(\CAL_GREGORIAN, $month, $year))->toBe(Shim::calDaysInMonth(\CAL_GREGORIAN, $month, $year));
        }
    }
});
test('cal days in month gregorian invalid month1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_GREGORIAN, 13, 2014);
});
test('cal days in month gregorian invalid month2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_GREGORIAN, 13, 2014);
});
test('cal days in month gregorian invalid year1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_GREGORIAN, 1, 0);
});
test('cal days in month gregorian invalid year2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_GREGORIAN, 1, 0);
});
test('cal days in month julian', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $year = $this->randomizer->getInt(-4713, 9999);
        $month = $this->randomizer->getInt(1, 12);
        if ($year != 0) {
            expect(cal_days_in_month(\CAL_GREGORIAN, $month, $year))->toBe(Shim::calDaysInMonth(\CAL_GREGORIAN, $month, $year));
        }
    }
});
test('cal days in month jewish', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $year = $this->randomizer->getInt(1, 5999);
        $month = $this->randomizer->getInt(1, 13);
        expect(cal_days_in_month(\CAL_JEWISH, $month, $year))->toBe(Shim::calDaysInMonth(\CAL_JEWISH, $month, $year));
    }
});
test('cal days in month jewish invalid month1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_JEWISH, 14, 2014);
});
test('cal days in month jewish invalid month2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_JEWISH, 14, 2014);
});
test('cal days in month jewish invalid year1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_JEWISH, 1, 0);
});
test('cal days in month jewish invalid year2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_JEWISH, 1, 0);
});
test('cal days in month julian invalid month1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_JULIAN, 13, 2014);
});
test('cal days in month julian invalid month2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_JULIAN, 13, 2014);
});
test('cal days in month julian invalid year1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    Shim::calDaysInMonth(\CAL_JULIAN, 1, 0);
});
test('cal days in month julian invalid year2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Invalid date');

    cal_days_in_month(\CAL_JULIAN, 1, 0);
});
test('cal days in month invalid calendar1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_days_in_month(): Argument #1 ($calendar) must be a valid calendar ID');

    Shim::calDaysInMonth(999, 1, 1);
});
test('cal days in month invalid calendar2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_days_in_month(): Argument #1 ($calendar) must be a valid calendar ID');

    cal_days_in_month(999, 1, 1);
});
test('cal from jd french', function (): void {
    // 0/0/0
    expect(Shim::calFromJd(2375839, \CAL_FRENCH))->toBe(cal_from_jd(2375839, \CAL_FRENCH));

    // 1/1/1
    expect(Shim::calFromJd(2375840, \CAL_FRENCH))->toBe(cal_from_jd(2375840, \CAL_FRENCH));

    // 13/5/14
    expect(Shim::calFromJd(2380952, \CAL_FRENCH))->toBe(cal_from_jd(2380952, \CAL_FRENCH));

    // 0/0/0
    expect(Shim::calFromJd(2380953, \CAL_FRENCH))->toBe(cal_from_jd(2380953, \CAL_FRENCH));
});
test('cal from jd gregorian', function (): void {
    // 0/0/0
    expect(Shim::calFromJd(0, \CAL_GREGORIAN))->toBe(cal_from_jd(0, \CAL_GREGORIAN));

    // 11/25/-4714
    expect(Shim::calFromJd(1, \CAL_GREGORIAN))->toBe(cal_from_jd(1, \CAL_GREGORIAN));

    // 12/31/-1
    expect(Shim::calFromJd(1721425, \CAL_GREGORIAN))->toBe(cal_from_jd(1721425, \CAL_GREGORIAN));

    // 1/1/1
    expect(Shim::calFromJd(1721426, \CAL_GREGORIAN))->toBe(cal_from_jd(1721426, \CAL_GREGORIAN));
});
test('cal from jd jewish', function (): void {
    // 0/0/0
    expect(Shim::calFromJd(347997, \CAL_JEWISH))->toBe(cal_from_jd(347997, \CAL_JEWISH));

    // 1/1/1
    expect(Shim::calFromJd(347998, \CAL_JEWISH))->toBe(cal_from_jd(347998, \CAL_JEWISH));
});
test('cal from jd julian', function (): void {
    // 0/0/0
    expect(Shim::calFromJd(0, \CAL_JULIAN))->toBe(cal_from_jd(0, \CAL_JULIAN));

    // 1/2/-4713
    expect(Shim::calFromJd(1, \CAL_JULIAN))->toBe(cal_from_jd(1, \CAL_JULIAN));

    // 12/31/-1
    expect(Shim::calFromJd(1721423, \CAL_JULIAN))->toBe(cal_from_jd(1721423, \CAL_JULIAN));

    // 1/1/1
    expect(Shim::calFromJd(1721424, \CAL_JULIAN))->toBe(cal_from_jd(1721424, \CAL_JULIAN));
});
test('cal from jd invalid calendar1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_from_jd(): Argument #2 ($calendar) must be a valid calendar ID');

    Shim::calFromJd(2345678, 999);
});
test('cal from jd invalid calendar2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_from_jd(): Argument #2 ($calendar) must be a valid calendar ID');

    cal_from_jd(2345678, 999);
});
test('cal info', function (): void {
    expect(cal_info(\CAL_FRENCH))->toBe(Shim::calInfo(\CAL_FRENCH));
    expect(cal_info(\CAL_GREGORIAN))->toBe(Shim::calInfo(\CAL_GREGORIAN));
    expect(cal_info(\CAL_JEWISH))->toBe(Shim::calInfo(\CAL_JEWISH));
    expect(cal_info(\CAL_JULIAN))->toBe(Shim::calInfo(\CAL_JULIAN));
});
test('cal info all', function (): void {
    expect(cal_info(-1))->toBe(Shim::calInfo(-1));
});
test('cal info invalid1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_info(): Argument #1 ($calendar) must be a valid calendar ID');

    Shim::calInfo(999);
});
test('cal info invalid2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_info(): Argument #1 ($calendar) must be a valid calendar ID');

    cal_info(999);
});
test('cal to jd invalid calendar1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_to_jd(): Argument #1 ($calendar) must be a valid calendar ID');

    Shim::calToJd(999, 1, 1, 1);
});
test('cal to jd invalid calendar2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('cal_to_jd(): Argument #1 ($calendar) must be a valid calendar ID');

    cal_to_jd(999, 1, 1, 1);
});
test('easter date', function (): void {
    expect(easter_date(2013))->toBe(Shim::easterDate(2013));
    expect(easter_date(2014))->toBe(Shim::easterDate(2014));
});
test('easter date high year1', function (): void {
    expect(easter_date(2038))->toBe(Shim::easterDate(2038));
});
test('easter date high year2', function (): void {
    expect(easter_date(2050))->toBe(Shim::easterDate(2050));
});
test('easter date low year1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('easter_date(): Argument #1 ($year) must be a year after 1970 (inclusive)');

    Shim::easterDate(1969);
});
test('easter date low year2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('easter_date(): Argument #1 ($year) must be a year after 1970 (inclusive)');

    easter_date(1969);
});
test('easter days', function (): void {
    foreach ([1751, 1752, 1753, 1581, 1582, 1583] as $year) {
        expect(easter_days($year, 999))->toBe(Shim::easterDays($year, 999));
        expect(easter_days($year, \CAL_EASTER_DEFAULT))->toBe(Shim::easterDays($year, \CAL_EASTER_DEFAULT));
        expect(easter_days($year, \CAL_EASTER_ROMAN))->toBe(Shim::easterDays($year, \CAL_EASTER_ROMAN));
        expect(easter_days($year, \CAL_EASTER_ALWAYS_GREGORIAN))->toBe(Shim::easterDays($year, \CAL_EASTER_ALWAYS_GREGORIAN));
        expect(easter_days($year, \CAL_EASTER_ALWAYS_JULIAN))->toBe(Shim::easterDays($year, \CAL_EASTER_ALWAYS_JULIAN));
    }
});
test('french to jd', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $year = $this->randomizer->getInt(1, 14);
        $month = $this->randomizer->getInt(1, 13);
        $day = $this->randomizer->getInt(1, $month == 13 ? 6 : 28);

        expect(frenchtojd($month, $day, $year))->toBe(Shim::frenchToJd($month, $day, $year));
        expect(cal_to_jd(\CAL_FRENCH, $month, $day, $year))->toBe(Shim::calToJd(\CAL_FRENCH, $month, $day, $year));
    }
});
test('french to jd out of range', function (): void {
    expect(frenchtojd(1, 1, 0))->toBe(Shim::frenchToJd(1, 1, 0));
});
test('gregorian to jd', function (): void {
    expect(gregoriantojd(1, 1, 0))->toBe(Shim::gregorianToJd(1, 1, 0));

    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $year = $this->randomizer->getInt(-4713, 9999);
        $month = $this->randomizer->getInt(1, 12);
        $day = $this->randomizer->getInt(1, 30);

        expect(gregoriantojd($month, $day, $year))->toBe(Shim::gregorianToJd($month, $day, $year));
        expect(cal_to_jd(\CAL_GREGORIAN, $month, $day, $year))->toBe(Shim::calToJd(\CAL_GREGORIAN, $month, $day, $year));
    }
});
test('jd day of week sunday', function (): void {
    $julian_day = gregoriantojd(8, 31, 2014);

    expect('Sunday')->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect('Sun')->toBe(Shim::jdDayOfWeek($julian_day, 2));
    expect(0)->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO));
    expect('Sunday')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG));
    expect('Sun')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT));

    expect(jddayofweek($julian_day, 0))->toBe(Shim::jdDayOfWeek($julian_day, 0));
    expect(jddayofweek($julian_day, 1))->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect(jddayofweek($julian_day, 2))->toBe(Shim::jdDayOfWeek($julian_day, 2));
});
test('jd day of week monday', function (): void {
    $julian_day = gregoriantojd(9, 1, 2014);

    // 2456902
    expect(1)->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO));
    expect('Monday')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG));
    expect('Mon')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT));

    expect(jddayofweek($julian_day, 0))->toBe(Shim::jdDayOfWeek($julian_day, 0));
    expect(jddayofweek($julian_day, 1))->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect(jddayofweek($julian_day, 2))->toBe(Shim::jdDayOfWeek($julian_day, 2));
});
test('jd day of week tuesday', function (): void {
    $julian_day = gregoriantojd(9, 2, 2014);

    // 2456903
    expect(2)->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO));
    expect('Tuesday')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG));
    expect('Tue')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT));

    expect(jddayofweek($julian_day, 0))->toBe(Shim::jdDayOfWeek($julian_day, 0));
    expect(jddayofweek($julian_day, 1))->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect(jddayofweek($julian_day, 2))->toBe(Shim::jdDayOfWeek($julian_day, 2));
});
test('jd day of week wednesday', function (): void {
    $julian_day = gregoriantojd(9, 3, 2014);

    // 2456904
    expect(3)->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO));
    expect('Wednesday')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG));
    expect('Wed')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT));

    expect(jddayofweek($julian_day, 0))->toBe(Shim::jdDayOfWeek($julian_day, 0));
    expect(jddayofweek($julian_day, 1))->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect(jddayofweek($julian_day, 2))->toBe(Shim::jdDayOfWeek($julian_day, 2));
});
test('jd day of week thursday', function (): void {
    $julian_day = gregoriantojd(9, 4, 2014);

    // 2456905
    expect(4)->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO));
    expect('Thursday')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG));
    expect('Thu')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT));

    expect(jddayofweek($julian_day, 0))->toBe(Shim::jdDayOfWeek($julian_day, 0));
    expect(jddayofweek($julian_day, 1))->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect(jddayofweek($julian_day, 2))->toBe(Shim::jdDayOfWeek($julian_day, 2));
});
test('jd day of week friday', function (): void {
    $julian_day = gregoriantojd(9, 5, 2014);

    // 2456906
    expect(5)->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO));
    expect('Friday')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG));
    expect('Fri')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT));

    expect(jddayofweek($julian_day, 0))->toBe(Shim::jdDayOfWeek($julian_day, 0));
    expect(jddayofweek($julian_day, 1))->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect(jddayofweek($julian_day, 2))->toBe(Shim::jdDayOfWeek($julian_day, 2));
});
test('jd day of week saturday', function (): void {
    $julian_day = gregoriantojd(9, 6, 2014);

    // 2456907
    expect(6)->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_DAYNO));
    expect('Saturday')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_LONG));
    expect('Sat')->toBe(Shim::jdDayOfWeek($julian_day, \CAL_DOW_SHORT));

    expect(jddayofweek($julian_day, 0))->toBe(Shim::jdDayOfWeek($julian_day, 0));
    expect(jddayofweek($julian_day, 1))->toBe(Shim::jdDayOfWeek($julian_day, 1));
    expect(jddayofweek($julian_day, 2))->toBe(Shim::jdDayOfWeek($julian_day, 2));
});
test('jd day of week negative', function (): void {
    expect(6)->toBe(Shim::jdDayOfWeek(-2, 0));
});
test('jd day of week invalid mode', function (): void {
    $julian_day = gregoriantojd(8, 31, 2014);

    // 2456901
    expect(0)->toBe(Shim::jdDayOfWeek($julian_day, 999));
    expect(jddayofweek($julian_day, 999))->toBe(Shim::jdDayOfWeek($julian_day, 999));
});
test('jd month name french', function (): void {
    for ($month = 1; $month <= 13; ++$month) {
        $julian_day = frenchtojd($month, 1, 10);
        expect(jdmonthname($julian_day, \CAL_MONTH_FRENCH))->toBe(Shim::jdMonthName($julian_day, \CAL_MONTH_FRENCH));
    }
});
test('jd month name gregorian', function (): void {
    for ($month = 1; $month <= 12; ++$month) {
        $julian_day = gregoriantojd($month, 1, 2014);
        expect(jdmonthname($julian_day, \CAL_MONTH_GREGORIAN_LONG))->toBe(Shim::jdMonthName($julian_day, \CAL_MONTH_GREGORIAN_LONG));
        expect(jdmonthname($julian_day, \CAL_MONTH_GREGORIAN_SHORT))->toBe(Shim::jdMonthName($julian_day, \CAL_MONTH_GREGORIAN_SHORT));
    }
});
test('jd month name jewish', function (): void {
    // Both leap and non-leap years
    foreach ([5000, 5001] as $year) {
        for ($month = 1; $month <= 13; ++$month) {
            $julian_day = jewishtojd($month, 1, $year);
            expect(jdmonthname($julian_day, \CAL_MONTH_JEWISH))->toBe(Shim::jdMonthName($julian_day, \CAL_MONTH_JEWISH));
        }
    }
});
test('jd month name julian', function (): void {
    for ($month = 1; $month <= 12; ++$month) {
        $julian_day = juliantojd($month, 1, 2014);
        expect(jdmonthname($julian_day, \CAL_MONTH_JULIAN_LONG))->toBe(Shim::jdMonthName($julian_day, \CAL_MONTH_JULIAN_LONG));
        expect(jdmonthname($julian_day, \CAL_MONTH_JULIAN_SHORT))->toBe(Shim::jdMonthName($julian_day, \CAL_MONTH_JULIAN_SHORT));
    }
});
test('jd month name invalid mode', function (): void {
    $julian_day = juliantojd(1, 1, 2014);

    expect('Jan')->toBe(Shim::jdMonthName($julian_day, 999));
    expect(jdmonthname($julian_day, 999))->toBe(Shim::jdMonthName($julian_day, 999));
});
test('jd to french', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $julian_day = $this->randomizer->getInt(gregoriantojd(9, 22, 1792), gregoriantojd(9, 22, 1806));
        expect(jdtofrench($julian_day))->toBe(Shim::jdToFrench($julian_day));
        expect(cal_from_jd($julian_day, \CAL_FRENCH))->toBe(Shim::calFromJd($julian_day, \CAL_FRENCH));
    }
});
test('jd to french edge cases', function (): void {
    expect(Shim::calFromJd(2375839, \CAL_FRENCH))->toBe(cal_from_jd(2375839, \CAL_FRENCH));
    expect(Shim::jdToFrench(2375839))->toBe('0/0/0');
    expect(jdtofrench(2375839))->toBe('0/0/0');

    expect(Shim::calFromJd(2375840, \CAL_FRENCH))->toBe(cal_from_jd(2375840, \CAL_FRENCH));
    expect(Shim::jdToFrench(2375840))->toBe('1/1/1');
    expect(jdtofrench(2375840))->toBe('1/1/1');

    expect(Shim::calFromJd(2380952, \CAL_FRENCH))->toBe(cal_from_jd(2380952, \CAL_FRENCH));
    expect(Shim::jdToFrench(2380952))->toBe('13/5/14');
    expect(jdtofrench(2380952))->toBe('13/5/14');

    expect(Shim::calFromJd(2380953, \CAL_FRENCH))->toBe(cal_from_jd(2380953, \CAL_FRENCH));
    expect(Shim::jdToFrench(2380953))->toBe('0/0/0');
    expect(jdtofrench(2380953))->toBe('0/0/0');
});
test('jd to french out of range', function (): void {
    $julian_day_low = 2375840 - 1;
    $julian_day_high = 2380953 + 2;

    expect(jdtofrench($julian_day_low))->toBe(Shim::jdToFrench($julian_day_low));
    expect(jdtofrench($julian_day_high))->toBe(Shim::jdToFrench($julian_day_high));
    expect(cal_from_jd($julian_day_low, \CAL_FRENCH))->toBe(Shim::calFromJd($julian_day_low, \CAL_FRENCH));
    expect(cal_from_jd($julian_day_high, \CAL_FRENCH))->toBe(Shim::calFromJd($julian_day_high, \CAL_FRENCH));
});
test('jd to gregorian', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $julian_day = $this->randomizer->getInt(1, gregoriantojd(12, 31, 9999));

        expect(jdtogregorian($julian_day))->toBe(Shim::jdToGregorian($julian_day));
        expect(cal_from_jd($julian_day, \CAL_GREGORIAN))->toBe(Shim::calFromJd($julian_day, \CAL_GREGORIAN));
    }
});
test('jd to gregorian edge cases', function (): void {
    $MAX_JD = \PHP_INT_SIZE == 4 ? 536838866 : 784350656097;

    expect(Shim::calFromJd(-1, \CAL_GREGORIAN))->toBe(cal_from_jd(-1, \CAL_GREGORIAN));
    expect(Shim::jdToGregorian(-1))->toBe('0/0/0');
    expect(jdtogregorian(-1))->toBe('0/0/0');

    expect(Shim::calFromJd(0, \CAL_GREGORIAN))->toBe(cal_from_jd(0, \CAL_GREGORIAN));
    expect(Shim::jdToGregorian(0))->toBe('0/0/0');
    expect(jdtogregorian(0))->toBe('0/0/0');

    expect(Shim::calFromJd(1, \CAL_GREGORIAN))->toBe(cal_from_jd(1, \CAL_GREGORIAN));
    expect(Shim::jdToGregorian(1))->toBe('11/25/-4714');
    expect(jdtogregorian(1))->toBe('11/25/-4714');

    // PHP overflows and gives bogus results
    expect(Shim::jdToGregorian($MAX_JD))->toBe(jdtogregorian($MAX_JD));
});
test('jd to jewish', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $julian_day = $this->randomizer->getInt(712878, 2539109);

        expect(jdtojewish($julian_day))->toBe(Shim::jdToJewish($julian_day, false, 0));
        expect(cal_from_jd($julian_day, \CAL_JEWISH))->toBe(Shim::calFromJd($julian_day, \CAL_JEWISH));
    }
});
test('jd to jewish edge cases', function (): void {
    expect(Shim::calFromJd(347997, \CAL_JEWISH))->toBe(cal_from_jd(347997, \CAL_JEWISH));
    expect(Shim::jdToJewish(347997, false, 0))->toBe('0/0/0');
    expect(jdtojewish(347997))->toBe('0/0/0');

    expect(Shim::calFromJd(347998, \CAL_JEWISH))->toBe(cal_from_jd(347998, \CAL_JEWISH));
    expect(Shim::jdToJewish(347998, false, 0))->toBe('1/1/1');
    expect(jdtojewish(347998))->toBe('1/1/1');

    expect(Shim::calFromJd(4000075, \CAL_JEWISH))->toBe(cal_from_jd(4000075, \CAL_JEWISH));
    expect(Shim::jdToJewish(4000075, false, 0))->toBe('13/29/9999');
    expect(jdtojewish(4000075))->toBe('13/29/9999');

    expect(Shim::calFromJd(4000076, \CAL_JEWISH))->toBe(cal_from_jd(4000076, \CAL_JEWISH));
    expect(Shim::jdToJewish(4000076, false, 0))->toBe('1/1/10000');
    expect(jdtojewish(4000076))->toBe('1/1/10000');

    expect(Shim::calFromJd(324542846, \CAL_JEWISH))->toBe(cal_from_jd(324542846, \CAL_JEWISH));
    expect(Shim::jdToJewish(324542846, false, 0))->toBe('12/13/887605');
    expect(jdtojewish(324542846))->toBe('12/13/887605');

    expect(Shim::calFromJd(324542847, \CAL_JEWISH))->toBe(cal_from_jd(324542847, \CAL_JEWISH));
    expect(Shim::jdToJewish(324542847, false, 0))->toBe('0/0/0');
    expect(jdtojewish(324542847))->toBe('0/0/0');
});
test('jd to jewish hebrew', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $julian_day = $this->randomizer->getInt(712878, 2539109);
        $flags = $this->randomizer->getInt(0, 7);
        expect(jdtojewish($julian_day, true, $flags))->toBe(Shim::jdToJewish($julian_day, true, $flags));
    }
});
test('jd to jewish hebrew out of range low1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Year out of range (0-9999)');

    $julian_day = jewishtojd(1, 1, 1) - 1;
    Shim::jdToJewish($julian_day, true, 0);
});
test('jd to jewish hebrew out of range low2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Year out of range (0-9999)');

    $julian_day = jewishtojd(1, 1, 1) - 1;
    jdtojewish($julian_day, true, 0);
});
test('jd to jewish hebrew out of range high1', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Year out of range (0-9999)');

    Shim::jdToJewish(4000076, true, 0);
});
test('jd to jewish hebrew out of range high2', function (): void {
    $this->expectException('ValueError');
    $this->expectExceptionMessage('Year out of range (0-9999)');

    jdtojewish(4000076, true, 0);
});
test('jd to julian', function (): void {
    $start_jd = juliantojd(1, 1, -2500);
    $end_jd = juliantojd(1, 1, 2500);

    for ($julian_day = $start_jd; $julian_day <= $end_jd; $julian_day += TestCase::LARGE_PRIME) {
        expect(cal_from_jd($julian_day, \CAL_JULIAN))->toBe(Shim::calFromJd($julian_day, \CAL_JULIAN));
        expect(jdtojulian($julian_day))->toBe(Shim::jdToJulian($julian_day));
    }
});
test('jd to julian edge cases', function (): void {
    $MAX_JD = \PHP_INT_SIZE == 4 ? 536838829 : 784368370349;

    expect(Shim::calFromJd(-1, \CAL_JULIAN))->toBe(cal_from_jd(-1, \CAL_JULIAN));
    expect(Shim::jdToJulian(-1))->toBe('0/0/0');
    expect(jdtojulian(-1))->toBe('0/0/0');

    expect(Shim::calFromJd(0, \CAL_JULIAN))->toBe(cal_from_jd(0, \CAL_JULIAN));
    expect(Shim::jdToJulian(0))->toBe('0/0/0');
    expect(jdtojulian(0))->toBe('0/0/0');

    expect(Shim::calFromJd(1, \CAL_JULIAN))->toBe(cal_from_jd(1, \CAL_JULIAN));
    expect(Shim::jdToJulian(1))->toBe('1/2/-4713');
    expect(jdtojulian(1))->toBe('1/2/-4713');

    // PHP overflows and gives bogus results
    //$this->assertSame(cal_from_jd($MAX_JD, CAL_JULIAN), Shim::calFromJd($MAX_JD, CAL_JULIAN));
    //$this->assertSame(JDToJulian($MAX_JD), Shim::jdToJulian($MAX_JD));
    $this->assertNotSame('0/0/0', jdtojulian($MAX_JD));
    $this->assertNotSame('0/0/0', Shim::jdToJulian($MAX_JD));

    expect(Shim::calFromJd($MAX_JD + 1, \CAL_JULIAN))->toBe(cal_from_jd($MAX_JD + 1, \CAL_JULIAN));
    expect(Shim::jdToJulian($MAX_JD + 1))->toBe('0/0/0');
    expect(jdtojulian($MAX_JD + 1))->toBe('0/0/0');
});
test('jd to unix', function (): void {
    $julian_day_start = gregoriantojd(1, 1, 1980);
    $julian_day_end = gregoriantojd(12, 31, 2030);

    for ($julian_day = $julian_day_start; $julian_day <= $julian_day_end; $julian_day += 23) {
        expect(jdtounix($julian_day))->toBe(Shim::jdToUnix($julian_day));
    }
});
test('jd to unix edge cases lower limit', function (): void {
    $lower_limit = 2440588;

    expect(Shim::jdToUnix($lower_limit))->toBeInt();
    expect(jdtounix($lower_limit))->toBe(Shim::jdToUnix($lower_limit));

    $this->expectException('ValueError');
    $this->expectExceptionMessage('jday must be between 2440588 and ' . Shim::jdToUnixUpperLimit());

    Shim::jdToUnix($lower_limit - 1);
});
test('jd to unix edge cases upper limit', function (): void {
    $upper_limit = Shim::jdToUnixUpperLimit();

    expect(Shim::jdToUnix($upper_limit))->toBeInt();
    expect(jdtounix($upper_limit))->toBe(Shim::jdToUnix($upper_limit));

    $this->expectException('ValueError');
    $this->expectExceptionMessage('jday must be between 2440588 and ' . Shim::jdToUnixUpperLimit());

    Shim::jdToUnix($upper_limit + 1);
});
test('jewish to jd', function (): void {
    expect(jewishtojd(1, 1, 0))->toBe(Shim::jewishToJD(1, 1, 0));

    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $year = $this->randomizer->getInt(1, 5999);
        $month = $this->randomizer->getInt(1, 13);
        $day = $this->randomizer->getInt(1, 29);

        expect(jewishtojd($month, $day, $year))->toBe(Shim::jewishToJD($month, $day, $year));
        expect(cal_to_jd(\CAL_JEWISH, $month, $day, $year))->toBe(Shim::calToJd(\CAL_JEWISH, $month, $day, $year));
    }
});
test('julian to jd', function (): void {
    expect(juliantojd(1, 1, 0))->toBe(Shim::julianToJd(1, 1, 0));

    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $year = $this->randomizer->getInt(-4713, 9999);
        $month = $this->randomizer->getInt(1, 12);
        $day = $this->randomizer->getInt(1, 30);

        expect(juliantojd($month, $day, $year))->toBe(Shim::julianToJd($month, $day, $year));
        expect(cal_to_jd(\CAL_JULIAN, $month, $day, $year))->toBe(Shim::calToJd(\CAL_JULIAN, $month, $day, $year));
    }
});
test('unix to jd', function (): void {
    for ($n = 0; $n < TestCase::ITERATIONS; ++$n) {
        $unix = $this->randomizer->getInt(1, 2147483647);
        expect(unixtojd($unix))->toBe(Shim::unixToJd($unix));
    }
});
test('unix to jd edge cases', function (): void {
    expect(2465443)->toBe(Shim::unixToJd(2147483647));
    expect(unixtojd(2147483647))->toBe(Shim::unixToJd(2147483647));

    $this->expectException('ValueError');
    $this->expectExceptionMessage('unixtojd(): Argument #1 ($timestamp) must be greater than or equal to 0');

    Shim::unixToJd(-1);
});
