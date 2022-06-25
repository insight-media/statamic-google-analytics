<br>
<img src="https://www.insight-media.be/images/logo.svg" height="80">

# A Google Analytics 4 addon for Statamic.

[![Latest Version on Packagist](https://img.shields.io/badge/packagist-v1.0.0-blue)](https://packagist.org/p2/insight-media/statamic-google-analytics)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/insight-media/statamic-google-analytics/Check%20&%20fix%20styling?label=code%20style)](https://github.com/insight-media/statamic-google-analytics/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/insight-media/statamic-google-analytics.svg?style=flat-square)](https://packagist.org/packages/insight-media/statamic-google-analytics)

This addon provides two main features: Tracking and Analytics.

**Tracking**

A GA4 tracking script is injected in the views.

**Analytics reporting**

This addon provides analytics widgets and an analytics reporting tab per entry.

## Installation

You can install the package via composer:

```bash
composer require insight-media/statamic-google-analytics
```

You can optionally publish the config file with:

```bash
php artisan vendor:publish --tag="statamic-google-analytics-config"
```

## Configuration

**Tracking**

In your .env:

Set your Tracking ID (string)

GA_TRACKING_ID

CP Admins should not be tracked (bool)

GA_IGNORE_ADMINS

Tracking should only be active in production environment (bool)

GA_PRODUCTION_ONLY

Add the tracking script to your antler views:

{{ ga }}

<br>

**Analytics reporting**

Path to credentials file in your storage folder (string)

GA_CREDENTIALS_PATH

Property ID (string)

GA_PROPERTY_ID

Number of days for the analytics to show data (int)

GA_DAYS

Show analytics per entry (having a slug) (bool)

GA_PAGE_GRAPH

<br>

**Widgets**

Configure the widgets in your config/statamic/cp.php widgets key
`[
'type' => 'analytics',
'group' => 'totalVisitorsAndPageViews|topReferrers|mostVisitedPages|topBrowsers|topcountries',
'width' => 50,
'days' => 10,
'display' => 'table|bar|line|pie|doughnut'
]`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Insight Media](https://github.com/insight-media)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
