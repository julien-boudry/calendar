<?php declare(strict_types=1);
use CondorcetPHP\PhpCalendars\{ArabicCalendar, Shim};

/**
 * Create the shim functions, so we can run tests on servers which do
 * not have the ext/calendar library installed.  For example HHVM.
 */
beforeEach(function (): void {
    Shim::create();
});
test('constants', function (): void {
    $calendar = new ArabicCalendar;

    expect($calendar->gedcomCalendarEscape())->toBe('@#DHIJRI@');
    expect($calendar->jdStart())->toBe(1948440);
    expect($calendar->jdEnd())->toBe(\PHP_INT_MAX);
    expect($calendar->daysInWeek())->toBe(7);
    expect($calendar->monthsInYear())->toBe(12);
});
test('is leap year', function (): void {
    $arabic = new ArabicCalendar;

    expect(false)->toBe($arabic->isLeapYear(1201));
    expect(true)->toBe($arabic->isLeapYear(1202));
    expect(false)->toBe($arabic->isLeapYear(1203));
    expect(false)->toBe($arabic->isLeapYear(1204));
    expect(true)->toBe($arabic->isLeapYear(1205));
    expect(false)->toBe($arabic->isLeapYear(1206));
    expect(true)->toBe($arabic->isLeapYear(1207));
    expect(false)->toBe($arabic->isLeapYear(1208));
    expect(false)->toBe($arabic->isLeapYear(1209));
    expect(true)->toBe($arabic->isLeapYear(1210));
    expect(false)->toBe($arabic->isLeapYear(1211));
    expect(false)->toBe($arabic->isLeapYear(1212));
    expect(true)->toBe($arabic->isLeapYear(1213));
    expect(false)->toBe($arabic->isLeapYear(1214));
    expect(false)->toBe($arabic->isLeapYear(1215));
    expect(true)->toBe($arabic->isLeapYear(1216));
    expect(false)->toBe($arabic->isLeapYear(1217));
    expect(true)->toBe($arabic->isLeapYear(1218));
    expect(false)->toBe($arabic->isLeapYear(1219));
    expect(false)->toBe($arabic->isLeapYear(1220));
    expect(true)->toBe($arabic->isLeapYear(1221));
    expect(false)->toBe($arabic->isLeapYear(1222));
    expect(false)->toBe($arabic->isLeapYear(1223));
    expect(true)->toBe($arabic->isLeapYear(1224));
    expect(false)->toBe($arabic->isLeapYear(1225));
    expect(true)->toBe($arabic->isLeapYear(1226));
    expect(false)->toBe($arabic->isLeapYear(1227));
    expect(false)->toBe($arabic->isLeapYear(1228));
    expect(true)->toBe($arabic->isLeapYear(1229));
    expect(false)->toBe($arabic->isLeapYear(1230));
});
test('days in month', function (): void {
    $arabic = new ArabicCalendar;

    expect(30)->toBe($arabic->daysInMonth(1201, 1));
    expect(28)->toBe($arabic->daysInMonth(1201, 2));
    expect(30)->toBe($arabic->daysInMonth(1201, 3));
    expect(29)->toBe($arabic->daysInMonth(1201, 4));
    expect(30)->toBe($arabic->daysInMonth(1201, 5));
    expect(29)->toBe($arabic->daysInMonth(1201, 6));
    expect(30)->toBe($arabic->daysInMonth(1201, 7));
    expect(29)->toBe($arabic->daysInMonth(1201, 8));
    expect(30)->toBe($arabic->daysInMonth(1201, 9));
    expect(29)->toBe($arabic->daysInMonth(1201, 10));
    expect(30)->toBe($arabic->daysInMonth(1201, 11));
    expect(29)->toBe($arabic->daysInMonth(1201, 12));
    expect(30)->toBe($arabic->daysInMonth(1202, 1));
    expect(28)->toBe($arabic->daysInMonth(1202, 2));
    expect(30)->toBe($arabic->daysInMonth(1202, 3));
    expect(29)->toBe($arabic->daysInMonth(1202, 4));
    expect(30)->toBe($arabic->daysInMonth(1202, 5));
    expect(29)->toBe($arabic->daysInMonth(1202, 6));
    expect(30)->toBe($arabic->daysInMonth(1202, 7));
    expect(29)->toBe($arabic->daysInMonth(1202, 8));
    expect(30)->toBe($arabic->daysInMonth(1202, 9));
    expect(29)->toBe($arabic->daysInMonth(1202, 10));
    expect(30)->toBe($arabic->daysInMonth(1202, 11));
    expect(30)->toBe($arabic->daysInMonth(1202, 12));
});
test('ymd tojd', function (): void {
    $arabic = new ArabicCalendar;

    expect(1948440)->toBe($arabic->ymdToJd(1, 1, 1));
    // 19 JUL 622 (Gregorian)
    expect([1, 1, 1])->toBe($arabic->jdToYmd(1948440));
    expect(2373709)->toBe($arabic->ymdToJd(1201, 1, 30));
    expect([1201, 1, 30])->toBe($arabic->jdToYmd(2373709));
    expect(2373737)->toBe($arabic->ymdToJd(1201, 2, 28));
    expect([1201, 2, 28])->toBe($arabic->jdToYmd(2373737));
    expect(2373768)->toBe($arabic->ymdToJd(1201, 3, 30));
    expect([1201, 3, 30])->toBe($arabic->jdToYmd(2373768));
    expect(2373797)->toBe($arabic->ymdToJd(1201, 4, 29));
    expect([1201, 4, 29])->toBe($arabic->jdToYmd(2373797));
    expect(2373827)->toBe($arabic->ymdToJd(1201, 5, 30));
    expect([1201, 5, 30])->toBe($arabic->jdToYmd(2373827));
    expect(2373856)->toBe($arabic->ymdToJd(1201, 6, 29));
    expect([1201, 6, 29])->toBe($arabic->jdToYmd(2373856));
    expect(2373886)->toBe($arabic->ymdToJd(1201, 7, 30));
    expect([1201, 7, 30])->toBe($arabic->jdToYmd(2373886));
    expect(2373915)->toBe($arabic->ymdToJd(1201, 8, 29));
    expect([1201, 8, 29])->toBe($arabic->jdToYmd(2373915));
    expect(2373945)->toBe($arabic->ymdToJd(1201, 9, 30));
    expect([1201, 9, 30])->toBe($arabic->jdToYmd(2373945));
    expect(2373974)->toBe($arabic->ymdToJd(1201, 10, 29));
    expect([1201, 10, 29])->toBe($arabic->jdToYmd(2373974));
    expect(2374004)->toBe($arabic->ymdToJd(1201, 11, 30));
    expect([1201, 11, 30])->toBe($arabic->jdToYmd(2374004));
    expect(2374033)->toBe($arabic->ymdToJd(1201, 12, 29));
    expect([1201, 12, 29])->toBe($arabic->jdToYmd(2374033));
    expect(2374063)->toBe($arabic->ymdToJd(1202, 1, 30));
    expect([1202, 1, 30])->toBe($arabic->jdToYmd(2374063));
    expect(2374091)->toBe($arabic->ymdToJd(1202, 2, 28));
    expect([1202, 2, 28])->toBe($arabic->jdToYmd(2374091));
    expect(2374122)->toBe($arabic->ymdToJd(1202, 3, 30));
    expect([1202, 3, 30])->toBe($arabic->jdToYmd(2374122));
    expect(2374151)->toBe($arabic->ymdToJd(1202, 4, 29));
    expect([1202, 4, 29])->toBe($arabic->jdToYmd(2374151));
    expect(2374181)->toBe($arabic->ymdToJd(1202, 5, 30));
    expect([1202, 5, 30])->toBe($arabic->jdToYmd(2374181));
    expect(2374210)->toBe($arabic->ymdToJd(1202, 6, 29));
    expect([1202, 6, 29])->toBe($arabic->jdToYmd(2374210));
    expect(2374240)->toBe($arabic->ymdToJd(1202, 7, 30));
    expect([1202, 7, 30])->toBe($arabic->jdToYmd(2374240));
    expect(2374269)->toBe($arabic->ymdToJd(1202, 8, 29));
    expect([1202, 8, 29])->toBe($arabic->jdToYmd(2374269));
    expect(2374299)->toBe($arabic->ymdToJd(1202, 9, 30));
    expect([1202, 9, 30])->toBe($arabic->jdToYmd(2374299));
    expect(2374328)->toBe($arabic->ymdToJd(1202, 10, 29));
    expect([1202, 10, 29])->toBe($arabic->jdToYmd(2374328));
    expect(2374358)->toBe($arabic->ymdToJd(1202, 11, 30));
    expect([1202, 11, 30])->toBe($arabic->jdToYmd(2374358));
    expect(2374388)->toBe($arabic->ymdToJd(1202, 12, 30));
    expect([1202, 12, 30])->toBe($arabic->jdToYmd(2374388));
});
test('jd to ymd reciprocity', function (): void {
    $calendar = new ArabicCalendar;

    for ($jd = $calendar->jdStart(); $jd < min(2457755, $calendar->jdEnd()); $jd += 79) {
        [$y, $m, $d] = $calendar->jdToYmd($jd);
        expect($calendar->ymdToJd($y, $m, $d))->toBe($jd);
    }
});
test('ymd to jd invalid month', function (): void {
    $this->expectExceptionMessage('Month 14 is invalid for this calendar');
    $this->expectException('InvalidArgumentException');

    $calendar = new ArabicCalendar;
    $calendar->ymdToJd(4, 14, 1);
});
