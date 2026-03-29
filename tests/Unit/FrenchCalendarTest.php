<?php declare(strict_types=1);
use CondorcetPHP\PhpCalendars\{FrenchCalendar, Shim};

/**
 * Create the shim functions, so we can run tests on servers which do
 * not have the ext/calendar library installed.  For example HHVM.
 */
beforeEach(function (): void {
    Shim::create();
});
test('constants', function (): void {
    $calendar = new FrenchCalendar;

    expect($calendar->gedcomCalendarEscape())->toBe('@#DFRENCH R@');
    expect($calendar->jdStart())->toBe(2375840);
    expect($calendar->jdEnd())->toBe(2380687);
    expect($calendar->daysInWeek())->toBe(10);
    expect($calendar->monthsInYear())->toBe(13);
});
test('is leap year', function (): void {
    $french = new FrenchCalendar;

    expect(false)->toBe($french->isLeapYear(1));
    expect(false)->toBe($french->isLeapYear(2));
    expect(true)->toBe($french->isLeapYear(3));
    expect(false)->toBe($french->isLeapYear(4));
    expect(false)->toBe($french->isLeapYear(5));
    expect(false)->toBe($french->isLeapYear(6));
    expect(true)->toBe($french->isLeapYear(7));
    expect(false)->toBe($french->isLeapYear(8));
    expect(false)->toBe($french->isLeapYear(9));
    expect(false)->toBe($french->isLeapYear(10));
    expect(true)->toBe($french->isLeapYear(11));
    expect(false)->toBe($french->isLeapYear(12));
    expect(false)->toBe($french->isLeapYear(13));
    expect(false)->toBe($french->isLeapYear(14));
});
test('days in month', function (): void {
    $french = new FrenchCalendar;

    // Cannot test year 14 against PHP, due to PHP bug 67976.
    for ($year = 1; $year <= 13; ++$year) {
        for ($month = 1; $month <= 13; ++$month) {
            expect(cal_days_in_month(\CAL_FRENCH, $month, $year))->toBe($french->daysInMonth($year, $month));
        }
    }
});
test('ymd tojd', function (): void {
    $french = new FrenchCalendar;

    expect(2375840)->toBe($french->ymdToJd(1, 1, 1));
    expect(2380952)->toBe($french->ymdToJd(14, 13, 5));

    expect([1, 1, 1])->toBe($french->jdToYmd(2375840));
    expect([14, 13, 5])->toBe($french->jdToYmd(2380952));
});
test('ymd to jd days', function (): void {
    $french = new FrenchCalendar;

    foreach ([3, 4] as $year) {
        for ($day = 1; $day <= 30; ++$day) {
            $julian_day = frenchtojd(8, $day, $year);
            $ymd = $french->jdToYmd($julian_day);

            expect($julian_day)->toBe($french->ymdToJd($year, 8, $day));
            expect(jdtofrench($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
        }
    }
});
test('ymd to jd months', function (): void {
    $french = new FrenchCalendar;

    for ($month = 1; $month <= 12; ++$month) {
        $julian_day = frenchtojd($month, 9, 5);
        $ymd = $french->jdToYmd($julian_day);

        expect($julian_day)->toBe($french->ymdToJd(5, $month, 9));
        expect(jdtofrench($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);

        $julian_day = frenchtojd($month, 9, 5);
        $ymd = $french->jdToYmd($julian_day);

        expect($julian_day)->toBe($french->ymdToJd(5, $month, 9));
        expect(jdtofrench($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
    }
});
test('ymd to jd years', function (): void {
    $french = new FrenchCalendar;

    for ($year = 1; $year <= 14; ++$year) {
        $julian_day = frenchtojd(8, 9, $year);
        $ymd = $french->jdToYmd($julian_day);

        expect($julian_day)->toBe($french->ymdToJd($year, 8, 9));
        expect(jdtofrench($julian_day))->toBe($ymd[1] . '/' . $ymd[2] . '/' . $ymd[0]);
    }
});
test('jd to ymd reciprocity', function (): void {
    $calendar = new FrenchCalendar;

    for ($jd = $calendar->jdStart(); $jd < min(2457755, $calendar->jdEnd()); $jd++) {
        [$y, $m, $d] = $calendar->jdToYmd($jd);
        expect($calendar->ymdToJd($y, $m, $d))->toBe($jd);
    }
});
test('ymd to jd invalid month', function (): void {
    $this->expectExceptionMessage('Month 14 is invalid for this calendar');
    $this->expectException('InvalidArgumentException');

    $calendar = new FrenchCalendar;
    $calendar->ymdToJd(4, 14, 1);
});
