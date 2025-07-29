MySchoolGH Management Software
======

**MySchoolGH is a PHP Web School Management Software suitable for all purposes**

At its heart, MySchoolGH is (mostly) a [PHP 8.1] compliant
HTML layout and rendering engine written in PHP. It is a style-driven renderer:
it will download and read external stylesheets, inline style tags, and the style
attributes of individual HTML elements.

*This document applies to the latest stable code which may not reflect the current 
release. For released code please
[navigate to the appropriate tag](https://github.com/testingmic/SchoolManager/releases).*

----


## Features

 * Students, Guardian, Courses & Lessons, Assignment and Events Management
 * Library Management System, Fees Allocation and Fees Payment System.
 * Fees category allocation to students/class
 * Attendance Manager and Role Based Dashboard
 * Payroll Generation
 * There is the panel for the uploading of examination results, modification of 
    marks and subsequent submission of results for approval.
 * It features a detailed debtors list, fees payment and reversals a subject class attendance & grading.
 
 
## Requirements

 * PHP version 7.1 or higher
 * DOM extension
 * MBString extension
 * php-font-lib
 * php-svg-lib
 
Note that some required dependencies may have further dependencies 
(notably php-svg-lib requires sabberworm/php-css-parser).

### Recommendations

 * GD (for image processing)
   * Additionally, the IMagick or GMagick extension improves image processing performance for certain image types
 * OPcache (OPcache, XCache, APC, etc.): improves performance

Visit the wiki for more information:
https://github.com/testingmic/SchoolManager/wiki/Requirements

## Easy Installation

### Install with composer

To install with [Composer](https://getcomposer.org/), simply require the
latest version of this package.

```bash
composer require testingmic/SchoolManager
composer require phpmailer/phpmailer
composer require dompdf/dompdf
```

Make sure that the autoload file from Composer is loaded.

```php
// somewhere early in your project's loading, require the Composer autoloader
// see: http://getcomposer.org/doc/00-intro.md
require 'vendor/autoload.php';
```