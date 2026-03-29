PHP Calendars
=============

Pure PHP implementation of six historical and religious calendars, with an object-oriented API and a drop-in replacement for PHP's `ext/calendar` extension.

This package provides a pure PHP implementation of the [Arabic (Hijri)](https://en.wikipedia.org/wiki/Islamic_calendar), [French Republican](https://en.wikipedia.org/wiki/French_Republican_Calendar), [Gregorian](https://en.wikipedia.org/wiki/Gregorian_calendar), [Julian](https://en.wikipedia.org/wiki/Julian_calendar), [Jewish](https://en.wikipedia.org/wiki/Hebrew_calendar) and [Persian (Jalali)](https://en.wikipedia.org/wiki/Iranian_calendars) calendars, plus a drop-in replacement for PHP‘s [ext/calendar](https://php.net/calendar) extension.

> [!NOTE]
> This project is a fork of [fisharebest/ext-calendar](https://github.com/fisharebest/ext-calendar), originally written by Greg Roach for the [webtrees](http://www.webtrees.net) project. It modernises the codebase (PHP 8.5+, strict types, native type declarations) and continues development under the `CondorcetPHP` namespace.

Requirements
============

* PHP (64bit) >= 8.5

Installation
============

``` bash
composer require condorcet-php/php-calendars
```

Object-oriented API
===================

All six calendars implement `CalendarInterface` with the following methods:

| Method | Description |
|---|---|
| `ymdToJd(int $year, int $month, int $day): int` | Convert a date to a Julian Day Number |
| `jdToYmd(int $julian_day): array` | Convert a Julian Day Number to `[$year, $month, $day]` |
| `daysInMonth(int $year, int $month): int` | Number of days in a given month |
| `monthsInYear(?int $year = null): int` | Number of months in a year (or the maximum if `$year` is `null`) |
| `daysInWeek(): int` | Number of days in a week (7 for most calendars, 10 for the French Republican) |
| `isLeapYear(int $year): bool` | Whether the given year is a leap year |
| `jdStart(): int` | Lowest Julian Day Number accepted by this calendar |
| `jdEnd(): int` | Highest Julian Day Number accepted by this calendar |
| `gedcomCalendarEscape(): string` | GEDCOM calendar escape sequence |

### Available calendars

| Class | Calendar | Months | Days/week | JD range |
|---|---|---|---|---|
| `ArabicCalendar` | Hijri (Islamic) | 12 | 7 | 1 948 440 – `PHP_INT_MAX` |
| `FrenchCalendar` | French Republican | 13 | 10 | 2 375 840 – 2 380 687 |
| `GregorianCalendar` | Gregorian | 12 | 7 | 1 – `PHP_INT_MAX` |
| `JewishCalendar` | Hebrew | 12 or 13 | 7 | 347 998 – `PHP_INT_MAX` |
| `JulianCalendar` | Julian | 12 | 7 | 1 – `PHP_INT_MAX` |
| `PersianCalendar` | Jalali (Iranian) | 12 | 7 | 1 948 321 – `PHP_INT_MAX` |

The Arabic and Persian calendars are **exclusive to this library** — they are
not available in the native `ext/calendar` extension.

### Usage example

``` php
use CondorcetPHP\PhpCalendars\ArabicCalendar;
use CondorcetPHP\PhpCalendars\FrenchCalendar;
use CondorcetPHP\PhpCalendars\GregorianCalendar;
use CondorcetPHP\PhpCalendars\JewishCalendar;
use CondorcetPHP\PhpCalendars\JulianCalendar;
use CondorcetPHP\PhpCalendars\PersianCalendar;

$calendar = new GregorianCalendar();

// Convert a date to a Julian Day Number and back
$jd = $calendar->ymdToJd(2026, 3, 29);
[$year, $month, $day] = $calendar->jdToYmd($jd);

// Leap year, days in month, months in year
$calendar->isLeapYear(2024);    // true
$calendar->daysInMonth(2024, 2); // 29
$calendar->monthsInYear();       // 12
$calendar->daysInWeek();         // 7

// Valid Julian Day range for this calendar
$calendar->jdStart(); // 1
$calendar->jdEnd();   // PHP_INT_MAX
```

### Jewish calendar extras

`JewishCalendar` provides an additional method to format numbers as Hebrew
numerals (UTF-8):

``` php
$jewish = new JewishCalendar();

// 13 months in a leap year
$jewish->monthsInYear(5784); // 13

// Hebrew numerals — with and without the thousands prefix
$jewish->numberToHebrewNumerals(5781, false); // "תשפ״א"
$jewish->numberToHebrewNumerals(5781, true);  // "ה׳תשפ״א"
```

Drop-in replacement for ext/calendar
=====================================

This package also provides shim functions that are automatically registered when
the native `ext/calendar` extension is not loaded.  All 18 functions and their
associated constants are supported:

* [cal_days_in_month()](https://php.net/cal_days_in_month)
* [cal_from_jd()](https://php.net/cal_from_jd)
* [cal_info()](https://php.net/cal_info)
* [cal_to_jd()](https://php.net/cal_to_jd)
* [easter_date()](https://php.net/easter_date)
* [easter_days()](https://php.net/easter_days)
* [FrenchToJD()](https://php.net/FrenchToJD)
* [GregorianToJD()](https://php.net/GregorianToJD)
* [JDDayOfWeek()](https://php.net/JDDayOfWeek)
* [JDMonthName()](https://php.net/JDMonthName)
* [JDToFrench()](https://php.net/JDToFrench)
* [JDToGregorian()](https://php.net/JDToGregorian)
* [jdtojewish()](https://php.net/jdtojewish)
* [JDToJulian()](https://php.net/JDToJulian)
* [jdtounix()](https://php.net/jdtounix)
* [JewishToJD()](https://php.net/JewishToJD)
* [JulianToJD()](https://php.net/JulianToJD)
* [unixtojd()](https://php.net/unixtojd)

All associated constants are also provided: `CAL_GREGORIAN`, `CAL_JULIAN`,
`CAL_JEWISH`, `CAL_FRENCH`, `CAL_NUM_CALS`, `CAL_DOW_DAYNO`, `CAL_DOW_SHORT`,
`CAL_DOW_LONG`, `CAL_MONTH_GREGORIAN_SHORT`, `CAL_MONTH_GREGORIAN_LONG`,
`CAL_MONTH_JULIAN_SHORT`, `CAL_MONTH_JULIAN_LONG`, `CAL_MONTH_JEWISH`,
`CAL_MONTH_FRENCH`, `CAL_EASTER_DEFAULT`, `CAL_EASTER_ROMAN`,
`CAL_EASTER_ALWAYS_GREGORIAN`, `CAL_EASTER_ALWAYS_JULIAN`,
`CAL_JEWISH_ADD_ALAFIM_GERESH`, `CAL_JEWISH_ADD_ALAFIM`,
`CAL_JEWISH_ADD_GERESHAYIM`.

``` php
// Works whether ext-calendar is installed or not
print_r(cal_info(CAL_GREGORIAN));

$jd = GregorianToJD(3, 29, 2026);
echo JDDayOfWeek($jd, CAL_DOW_LONG); // "Sunday"
echo jdtojewish($jd, true, CAL_JEWISH_ADD_GERESHAYIM);
```

You can also call the static methods on `CondorcetPHP\PhpCalendars\Shim`
directly, regardless of whether `ext/calendar` is installed:

``` php
use CondorcetPHP\PhpCalendars\Shim;

$jd = Shim::gregorianToJd(3, 29, 2026);
echo Shim::jdDayOfWeek($jd, CAL_DOW_LONG);
```

Known restrictions and limitations
===================================

* The functions `easter_date()` and `jdtounix()` use PHP's timezone, instead of the operating system's timezone.  These may be different.
* Invalid arguments (wrong calendar ID, out-of-range month, etc.) throw a `ValueError`.
* The French Republican calendar only covers years 1–14 (22 Sep 1792 – 31 Dec 1805).

Development and contributions
=============================

See the [CHANGELOG](CHANGELOG.md) for the full release history.

Pull requests are welcome.  Please ensure you include unit-tests where applicable.

``` bash
# Run the test suite (Pest)
composer test

# Run the benchmarks (native ext-calendar vs PHP implementation)
composer bench

# Static analysis
composer phpstan

# Code style
composer lint
```

Future plans
============

* Support alternate leap-year schemes for the French calendar (true equinox, Romme, 128-year cycle) as well as the 4-year cycle.
* Support other calendars, such as Ethiopian, Hindu, Chinese, etc.
