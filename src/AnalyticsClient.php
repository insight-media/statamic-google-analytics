<?php

namespace InsightMedia\StatamicGoogleAnalytics;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Google\Analytics\Data\V1beta\Row;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use InsightMedia\StatamicGoogleAnalytics\Exceptions\InvalidConfiguration;
use Statamic\Facades\Config;

class AnalyticsClient
{

    protected BetaAnalyticsDataClient $client;

    public function __construct()
    {

        $serviceAccountCredentialsJson = Config::get('statamic-google-analytics.analytics.service_account_credentials_json');

        if (! file_exists($serviceAccountCredentialsJson)) {
            throw InvalidConfiguration::credentialsJsonDoesNotExist($serviceAccountCredentialsJson);
        }

        $this->client = new BetaAnalyticsDataClient(
            [
                'credentials' => $serviceAccountCredentialsJson
            ]
        );
    }

    public function fetchVisitorsAndPageViews(Period $period): Collection
    {

        $response = $this->runReport($period, [
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'date',
                    ]
                ),
                new Dimension(
                    [
                        'name' => 'pageTitle',
                    ]
                )
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'totalUsers',
                    ]
                ),
                new Metric(
                    [
                        'name' => 'screenPageViews',
                    ]
                )
            ]
        ]);

        return collect($response->getRows() ?? [])->map(fn(Row $row) => [
            'date' => Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue()),
            'pageTitle' => $row->getDimensionValues()[1]->getValue(),
            'visitors' => (int)$row->getMetricValues()[0]->getValue(),
            'pageViews' => (int)$row->getMetricValues()[1]->getValue(),
        ]);
    }

    public function fetchTotalVisitorsAndPageViews(Period $period): Collection
    {

        $response = $this->runReport($period, [
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'date',
                    ]
                )
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'totalUsers',
                    ]
                ),
                new Metric(
                    [
                        'name' => 'screenPageViews',
                    ]
                )
            ],
            'orderBys' => [
                new OrderBy([
                    'dimension' => new DimensionOrderBy(
                        [
                            'dimension_name' => 'date'
                        ]
                    ),
                    'desc' => false
                ])
            ],
        ]);

        return collect($response->getRows() ?? [])->map(fn(Row $row) => [
            'date' => Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue()),
            'visitors' => (int)$row->getMetricValues()[0]->getValue(),
            'pageViews' => (int)$row->getMetricValues()[1]->getValue()
        ]);
    }

    public function fetchTotalVisitorsAndPageViewsForURL(Period $period, string $url): Collection
    {

        $response = $this->runReport($period, [
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'date',
                    ]
                ),
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'totalUsers',
                    ]
                ),
                new Metric(
                    [
                        'name' => 'screenPageViews',
                    ]
                )
            ],
            'dimensionFilter' => new FilterExpression([
                'filter' => new Filter([
                    'field_name' => 'pagePathPlusQueryString',
                    'string_filter' => new StringFilter([
                        'match_type' => MatchType::EXACT,
                        'value' => $url,
                        'case_sensitive' => false
                    ])
                ])
            ]),
            'orderBys' => [
                new OrderBy([
                    'dimension' => new DimensionOrderBy(
                        [
                            'dimension_name' => 'date'
                        ]
                    ),
                    'desc' => false
                ])
            ],
        ]);

        return collect($response->getRows() ?? [])->map(fn(Row $row) => [
            'date' => Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue()),
            'visitors' => (int)$row->getMetricValues()[0]->getValue(),
            'pageViews' => (int)$row->getMetricValues()[1]->getValue()
        ]);
    }

    public function fetchMostVisitedPages(Period $period, int $maxResults = 20): Collection
    {

        $response = $this->runReport($period, [
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'pagePathPlusQueryString',
                    ]
                ),
                new Dimension(
                    [
                        'name' => 'pageTitle',
                    ]
                )
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'screenPageViews',
                    ]
                )
            ],
            'orderBys' => [
                new OrderBy([
                    'metric' => new MetricOrderBy(
                        [
                            'metric_name' => 'screenPageViews'
                        ]
                    ),
                    'desc' => true
                ])
            ],
            'limit' => $maxResults
        ]);

        return collect($response->getRows() ?? [])->map(fn(Row $row) => [
            'url' => $row->getDimensionValues()[0]->getValue(),
            'pageTitle' => $row->getDimensionValues()[1]->getValue(),
            'pageViews' => (int)$row->getMetricValues()[0]->getValue()
        ]);
    }

    public function fetchTopReferrers(Period $period, int $maxResults = 20): Collection
    {

        $response = $this->runReport($period, [
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'pageReferrer',
                    ]
                )
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'screenPageViews',
                    ]
                )
            ],
            'orderBys' => [
                new OrderBy([
                    'metric' => new MetricOrderBy(
                        [
                            'metric_name' => 'screenPageViews'
                        ]
                    ),
                    'desc' => true
                ])
            ],
            'limit' => $maxResults
        ]);

        return collect($response->getRows() ?? [])->map(fn(Row $row) => [
            'url' => $row->getDimensionValues()[0]->getValue(),
            'pageViews' => (int)$row->getMetricValues()[0]->getValue()
        ]);
    }

    public function fetchTopCountries(Period $period, int $maxResults = 10): Collection
    {

        $response = $this->runReport($period, [
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'country',
                    ]
                )
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'sessions',
                    ]
                )
            ],
            'orderBys' => [
                new OrderBy([
                    'metric' => new MetricOrderBy(
                        [
                            'metric_name' => 'sessions'
                        ]
                    ),
                    'desc' => true
                ])
            ],
            'limit' => $maxResults
        ]);

        return collect($response->getRows() ?? [])->map(fn(Row $row) => [
            'country' => $row->getDimensionValues()[0]->getValue(),
            'sessions' => (int)$row->getMetricValues()[0]->getValue()
        ]);
    }

    public function fetchTopBrowsers(Period $period, int $maxResults = 10): Collection
    {

        $response = $this->runReport($period, [
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'browser',
                    ]
                )
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'sessions',
                    ]
                )
            ],
            'orderBys' => [
                new OrderBy([
                    'metric' => new MetricOrderBy(
                        [
                            'metric_name' => 'sessions'
                        ]
                    ),
                    'desc' => true
                ])
            ]
        ]);

        $topBrowsers = collect($response->getRows() ?? [])->map(fn(Row $row) => [
            'browser' => $row->getDimensionValues()[0]->getValue(),
            'sessions' => (int)$row->getMetricValues()[0]->getValue()
        ]);

        if ($topBrowsers->count() <= $maxResults) {
            return $topBrowsers;
        }

        return $this->summarizeTopBrowsers($topBrowsers, $maxResults);

    }

    protected function summarizeTopBrowsers(Collection $topBrowsers, int $maxResults): Collection
    {
        return $topBrowsers
            ->take($maxResults - 1)
            ->push([
                'browser' => 'Others',
                'sessions' => $topBrowsers->splice($maxResults - 1)->sum('sessions'),
            ]);
    }

    protected function runReport(Period $period, array $data): RunReportResponse
    {

        $propertyId = Config::get('statamic-google-analytics.analytics.property_id');

        if (!$propertyId) {
            throw InvalidConfiguration::propertyIdDoesNotExist();
        }

        return $this->client->runReport([
                'property' => 'properties/' . $propertyId,
                'dateRanges' => [
                    new DateRange([
                        'start_date' => $period->startDate->format('Y-m-d'),
                        'end_date' => $period->endDate->format('Y-m-d'),
                    ]),
                ]
            ] + $data);
    }

}
