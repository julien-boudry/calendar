<?php declare(strict_types=1);
use CondorcetPHP\PhpCalendars\{GregorianCalendar, Shim};

/**
 * Create the shim functions, so we can run tests on servers which do
 * not have the ext/calendar library installed.  For example HHVM.
 */
beforeEach(function (): void {
    Shim::create();
});
test('constants', function (): void {
    $calendar = new GregorianCalendar;

    expect($calendar->gedcomCalendarEscape())->toBe('@#DGREGORIAN@');
    expect($calendar->jdStart())->toBe(1);
    expect($calendar->jdEnd())->toBe(\PHP_INT_MAX);
    expect($calendar->daysInWeek())->toBe(7);
    expect($calendar->monthsInYear())->toBe(12);
});
test('is leap year', function (): void {
    $gregorian = new GregorianCalendar;

    expect(true)->toBe($gregorian->isLeapYear(-5));
    expect(false)->toBe($gregorian->isLeapYear(-4));
    expect(false)->toBe($gregorian->isLeapYear(-3));
    expect(false)->toBe($gregorian->isLeapYear(-2));
    expect(true)->toBe($gregorian->isLeapYear(-1));
    expect(false)->toBe($gregorian->isLeapYear(1500));
    expect(true)->toBe($gregorian->isLeapYear(1600));
    expect(false)->toBe($gregorian->isLeapYear(1700));
    expect(false)->toBe($gregorian->isLeapYear(1800));
    expect(false)->toBe($gregorian->isLeapYear(1900));
    expect(false)->toBe($gregorian->isLeapYear(1999));
    expect(true)->toBe($gregorian->isLeapYear(2000));
    expect(false)->toBe($gregorian->isLeapYear(2001));
    expect(false)->toBe($gregorian->isLeapYear(2002));
    expect(false)->toBe($gregorian->isLeapYear(2003));
    expect(true)->toBe($gregorian->isLeapYear(2004));
    expect(false)->toBe($gregorian->isLeapYear(2005));
    expect(false)->toBe($gregorian->isLeapYear(2100));
    expect(false)->toBe($gregorian->isLeapYear(2200));
});
test('easter days coverage', function (): void {
    $gregorian = new GregorianCalendar;

    foreach ([2037, 2035, 2030, 1981, 1894, 1875] as $year) {
        expect(easter_days($year, \CAL_EASTER_ALWAYS_GREGORIAN))->toBe($gregorian->easterDays($year));
    }
});
test('easter days modern times', function (): void {
    $gregorian = new GregorianCalendar;

    for ($year = 1970; $year <= 2037; ++$year) {
        expect(easter_days($year, \CAL_EASTER_ALWAYS_GREGORIAN))->toBe($gregorian->easterDays($year));
    }
});
test('days in month', function (): void {
    $gregorian = new GregorianCalendar;

    foreach ([-5, -4, -1, 1, 1500, 1600, 1700, 1800, 1900, 1999, 2000, 2001, 2002, 2003, 2004, 2005, 2100, 2200] as $year) {
        for ($month = 1; $month <= 12; ++$month) {
            expect(cal_days_in_month(\CAL_GREGORIAN, $month, $year))->toBe($gregorian->daysInMonth($year, $month));
        }
    }
});
test('ymd to jd days', function (): void {
    $gregorian = new GregorianCalendar;

    foreach ([2012, 2014] as $year) {
        for ($day = 1; $day <= 28; ++$day) {
            $julian_day = gregoriantojd(8, $day, $year);
            $ymd = $gregorian->jdToYmd($julian_day);

            expect($julian_day)->toBe($gregorian->ymdToJd($year, 8, $day));
            expect(jdtogregorian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
        }
    }
});
test('ymd to jd months', function (): void {
    $gregorian = new GregorianCalendar;

    foreach ([2012, 2014] as $year) {
        for ($month = 1; $month <= 12; ++$month) {
            $julian_day = gregoriantojd($month, 9, $year);
            $ymd = $gregorian->jdToYmd($julian_day);

            expect($julian_day)->toBe($gregorian->ymdToJd($year, $month, 9));
            expect(jdtogregorian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
        }
    }
});
test('ymd to jd years', function (): void {
    $gregorian = new GregorianCalendar;

    for ($year = 1970; $year <= 2037; ++$year) {
        $julian_day = gregoriantojd(8, 9, $year);
        $ymd = $gregorian->jdToYmd($julian_day);

        expect($julian_day)->toBe($gregorian->ymdToJd($year, 8, 9));
        expect(jdtogregorian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
    }
});
test('ymd to jd years bc', function (): void {
    $gregorian = new GregorianCalendar;

    for ($year = -5; $year <= 5; ++$year) {
        if ($year != 0) {
            $julian_day = gregoriantojd(1, 1, $year);
            $ymd = $gregorian->jdToYmd($julian_day);

            expect($julian_day)->toBe($gregorian->ymdToJd($year, 1, 1));
            expect(jdtogregorian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);

            $julian_day = gregoriantojd(12, 31, $year);
            $ymd = $gregorian->jdToYmd($julian_day);

            expect($julian_day)->toBe($gregorian->ymdToJd($year, 12, 31));
            expect(jdtogregorian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
        }
    }
});
test('jd to ymd reciprocity', function (): void {
    $calendar = new GregorianCalendar;

    for ($jd = $calendar->jdStart(); $jd < min(2457755, $calendar->jdEnd()); $jd += 79) {
        [$y, $m, $d] = $calendar->jdToYmd($jd);
        expect($calendar->ymdToJd($y, $m, $d))->toBe($jd);
    }
});
test('ymd to jd invalid month', function (): void {
    $this->expectExceptionMessage('Month 14 is invalid for this calendar');
    $this->expectException('InvalidArgumentException');

    $calendar = new GregorianCalendar;
    $calendar->ymdToJd(4, 14, 1);
});
