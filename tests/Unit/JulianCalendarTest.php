<?php declare(strict_types=1);
use CondorcetPHP\PhpCalendars\{JulianCalendar, Shim};

/**
 * Create the shim functions, so we can run tests on servers which do
 * not have the ext/calendar library installed.  For example HHVM.
 */
beforeEach(function (): void {
    Shim::create();
});
test('constants', function (): void {
    $calendar = new JulianCalendar;

    expect($calendar->gedcomCalendarEscape())->toBe('@#DJULIAN@');
    expect($calendar->jdStart())->toBe(1);
    expect($calendar->jdEnd())->toBe(\PHP_INT_MAX);
    expect($calendar->daysInWeek())->toBe(7);
    expect($calendar->monthsInYear())->toBe(12);
});
test('is leap year', function (): void {
    $julian = new JulianCalendar;

    expect(true)->toBe($julian->isLeapYear(-5));
    expect(false)->toBe($julian->isLeapYear(-4));
    expect(false)->toBe($julian->isLeapYear(-3));
    expect(false)->toBe($julian->isLeapYear(-2));
    expect(true)->toBe($julian->isLeapYear(-1));
    expect(true)->toBe($julian->isLeapYear(1500));
    expect(true)->toBe($julian->isLeapYear(1600));
    expect(true)->toBe($julian->isLeapYear(1700));
    expect(true)->toBe($julian->isLeapYear(1800));
    expect(true)->toBe($julian->isLeapYear(1900));
    expect(false)->toBe($julian->isLeapYear(1999));
    expect(true)->toBe($julian->isLeapYear(2000));
    expect(false)->toBe($julian->isLeapYear(2001));
    expect(false)->toBe($julian->isLeapYear(2002));
    expect(false)->toBe($julian->isLeapYear(2003));
    expect(true)->toBe($julian->isLeapYear(2004));
    expect(false)->toBe($julian->isLeapYear(2005));
    expect(true)->toBe($julian->isLeapYear(2100));
    expect(true)->toBe($julian->isLeapYear(2200));
});
test('easter days coverage', function (): void {
    $julian = new JulianCalendar;

    foreach ([2037, 2036, 2029, 1972] as $year) {
        expect(easter_days($year, \CAL_EASTER_ALWAYS_JULIAN))->toBe($julian->easterDays($year));
    }
});
test('easter days modern times', function (): void {
    $julian = new JulianCalendar;

    for ($year = 1970; $year <= 2037; ++$year) {
        expect(easter_days($year, \CAL_EASTER_ALWAYS_JULIAN))->toBe($julian->easterDays($year));
    }
});
test('days in month', function (): void {
    $julian = new JulianCalendar;

    foreach ([-5, -4, -1, 1, 1500, 1600, 1700, 1800, 1900, 1999, 2000, 2001, 2002, 2003, 2004, 2005, 2100, 2200] as $year) {
        for ($month = 1; $month <= 12; ++$month) {
            expect(cal_days_in_month(\CAL_JULIAN, $month, $year))->toBe($julian->daysInMonth($year, $month));
        }
    }
});
test('ymd to jd days', function (): void {
    $julian = new JulianCalendar;

    foreach ([2012, 2014] as $year) {
        for ($day = 1; $day <= 28; ++$day) {
            $julian_day = juliantojd(8, $day, $year);
            $ymd = $julian->jdToYmd($julian_day);

            expect($julian_day)->toBe($julian->ymdToJd($year, 8, $day));
            expect(jdtojulian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
        }
    }
});
test('ymd to jd months', function (): void {
    $julian = new JulianCalendar;

    for ($month = 1; $month <= 12; ++$month) {
        foreach ([2012, 2014] as $year) {
            $julian_day = juliantojd($month, 9, $year);
            $ymd = $julian->jdToYmd($julian_day);

            expect($julian_day)->toBe($julian->ymdToJd($year, $month, 9));
            expect(jdtojulian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
        }
    }
});
test('ymd to jd years', function (): void {
    $julian = new JulianCalendar;

    for ($year = 1970; $year <= 2037; ++$year) {
        $julian_day = juliantojd(8, 9, $year);
        $ymd = $julian->jdToYmd($julian_day);

        expect($julian_day)->toBe($julian->ymdToJd($year, 8, 9));
        expect(jdtojulian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
    }
});
test('ymd to jd years bc', function (): void {
    $julian = new JulianCalendar;

    for ($year = -5; $year <= 5; ++$year) {
        if ($year != 0) {
            $julian_day = juliantojd(1, 1, $year);
            $ymd = $julian->jdToYmd($julian_day);

            expect($julian_day)->toBe($julian->ymdToJd($year, 1, 1));
            expect(jdtojulian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);

            $julian_day = juliantojd(12, 31, $year);
            $ymd = $julian->jdToYmd($julian_day);

            expect($julian_day)->toBe($julian->ymdToJd($year, 12, 31));
            expect(jdtojulian($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
        }
    }
});
test('jd to ymd reciprocity', function (): void {
    $calendar = new JulianCalendar;

    for ($jd = $calendar->jdStart(); $jd < min(2457755, $calendar->jdEnd()); $jd += 79) {
        [$y, $m, $d] = $calendar->jdToYmd($jd);
        expect($calendar->ymdToJd($y, $m, $d))->toBe($jd);
    }
});
test('ymd to jd invalid month', function (): void {
    $this->expectExceptionMessage('Month 14 is invalid for this calendar');
    $this->expectException('InvalidArgumentException');

    $calendar = new JulianCalendar;
    $calendar->ymdToJd(4, 14, 1);
});
