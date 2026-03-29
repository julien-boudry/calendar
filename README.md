PHP Calendars
=============

Pure PHP implementation of six historical and religious calendars, with an object-oriented API and a drop-in replacement for PHP's `ext/calendar` extension.

This package provides a pure PHP implementation of the [Arabic (Hijri)](https://en.wikipedia.org/wiki/Islamic_calendar), [French Republican](https://en.wikipedia.org/wiki/French_Republican_Calendar), [Gregorian](https://en.wikipedia.org/wiki/Gregorian_calendar), [Julian](https://en.wikipedia.org/wiki/Julian_calendar), [Jewish](https://en.wikipedia.org/wiki/Hebrew_calendar) and [Persian (Jalali)](https://en.wikipedia.org/wiki/Iranian_calendars) calendars, plus a drop-in replacement for PHP‘s [ext/calendar](https://php.net/calendar) extension.

> [!NOTE]
> This project is a fork of [fisharebest/ext-calendar](https://github.com/fisharebest/ext-calendar), originally written by Greg Roach for the [webtrees](http://www.webtrees.net) project. It modernises the codebase (PHP 8.5+, strict types, native type declarations) and continues development under the `CondorcetPHP` namespace.

Requirements
============

* PHP >= 8.5

Installation
============

``` bash
composer require condorcet-php/php-calendars
```

Object-oriented API
===================

Use the calendar classes directly.  This API supports two additional
calendars (Arabic and Persian) that are not available in the native extension.

``` php
use CondorcetPHP\PhpCalendars\ArabicCalendar;
use CondorcetPHP\PhpCalendars\FrenchCalendar;
use CondorcetPHP\PhpCalendars\GregorianCalendar;
use CondorcetPHP\PhpCalendars\JewishCalendar;
use CondorcetPHP\PhpCalendars\JulianCalendar;
use CondorcetPHP\PhpCalendars\PersianCalendar;

// Create a calendar
$calendar = new GregorianCalendar();

// Date conversions
$julian_day = $calendar->ymdToJd($year, $month, $day);
[$year, $month, $day] = $calendar->jdToYmd($julian_day);

// Information about days, weeks and months
$is_leap_year   = $calendar->isLeapYear($year);
$days_in_month  = $calendar->daysInMonth($year, $month);
$months_in_year = $calendar->monthsInYear();       // Not all calendars have 12
$months_in_year = $calendar->monthsInYear($year);  // In a specific year
$days_in_week   = $calendar->daysInWeek();         // Not all calendars have 7

// Which dates are valid for this calendar?
$jd = $calendar->jdStart();
$jd = $calendar->jdEnd();

// Hebrew numerals
$jewish = new JewishCalendar();
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

``` php
print_r(cal_info(CAL_GREGORIAN)); // Works whether ext-calendar is installed or not
```

Known restrictions and limitations
===================================

The functions `easter_date()` and `jdtounix()` use PHP‘s timezone, instead of the operating system‘s timezone.  These may be different.

Development and contributions
=============================

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
