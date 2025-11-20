<?php

namespace App\Services\Analytics;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GA4Service
{
    protected ?BetaAnalyticsDataClient $client = null;
    protected ?string $propertyId = null;

    public function __construct()
    {
        $this->propertyId = config('services.ga4.property_id');
        
        if (!$this->propertyId) {
            Log::warning('GA4 property_id is not configured');
            return;
        }

        try {
            $credentialsPath = config('services.ga4.credentials_path');
            
            if ($credentialsPath && file_exists($credentialsPath)) {
                putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
            }

            $this->client = new BetaAnalyticsDataClient();
        } catch (\Exception $e) {
            Log::error('Failed to initialize GA4 client: ' . $e->getMessage());
        }
    }

    protected function getClient(): ?BetaAnalyticsDataClient
    {
        return $this->client;
    }

    protected function getPropertyId(): string
    {
        if (!$this->propertyId) {
            return 'properties/0';
        }
        
        // Убеждаемся, что property_id в правильном формате
        if (!str_starts_with($this->propertyId, 'properties/')) {
            return 'properties/' . $this->propertyId;
        }
        
        return $this->propertyId;
    }

    protected function runReport(array $dimensions, array $metrics, Carbon $startDate, Carbon $endDate, int $limit = 100, array $orderBy = []): array
    {
        if (!$this->getClient()) {
            return [];
        }

        $cacheKey = 'ga4_' . md5(json_encode([
            $dimensions,
            $metrics,
            $startDate->toDateString(),
            $endDate->toDateString(),
            $limit,
            $orderBy,
        ]));

        return Cache::remember($cacheKey, 3600, function () use ($dimensions, $metrics, $startDate, $endDate, $limit, $orderBy) {
            try {
                $request = new RunReportRequest();
                $request->setProperty($this->getPropertyId());
                
                // Date range
                $dateRange = new DateRange();
                $dateRange->setStartDate($startDate->format('Y-m-d'));
                $dateRange->setEndDate($endDate->format('Y-m-d'));
                $request->setDateRanges([$dateRange]);

                // Dimensions
                $dimensionObjects = [];
                foreach ($dimensions as $dimension) {
                    $dimensionObj = new Dimension();
                    $dimensionObj->setName($dimension);
                    $dimensionObjects[] = $dimensionObj;
                }
                $request->setDimensions($dimensionObjects);

                // Metrics
                $metricObjects = [];
                foreach ($metrics as $metric) {
                    $metricObj = new Metric();
                    $metricObj->setName($metric);
                    $metricObjects[] = $metricObj;
                }
                $request->setMetrics($metricObjects);

                // Order by
                if (!empty($orderBy)) {
                    $orderByObjects = [];
                    foreach ($orderBy as $order) {
                        if (isset($order['metric'])) {
                            $orderByObj = new OrderBy();
                            $metricOrderBy = new MetricOrderBy();
                            $metricOrderBy->setMetricName($order['metric']);
                            $orderByObj->setMetric($metricOrderBy);
                            $orderByObj->setDesc($order['desc'] ?? true);
                            $orderByObjects[] = $orderByObj;
                        } elseif (isset($order['dimension'])) {
                            $orderByObj = new OrderBy();
                            $dimensionOrderBy = new DimensionOrderBy();
                            $dimensionOrderBy->setDimensionName($order['dimension']);
                            $orderByObj->setDimension($dimensionOrderBy);
                            $orderByObj->setDesc($order['desc'] ?? true);
                            $orderByObjects[] = $orderByObj;
                        }
                    }
                    $request->setOrderBys($orderByObjects);
                }

                // Limit
                $request->setLimit($limit);

                $response = $this->getClient()->runReport($request);

                $results = [];
                foreach ($response->getRows() as $row) {
                    $result = [];
                    
                    // Dimensions
                    $dimensionValues = $row->getDimensionValues();
                    foreach ($dimensionValues as $index => $dimensionValue) {
                        $dimensionName = $dimensions[$index] ?? "dimension_{$index}";
                        $result[$dimensionName] = $dimensionValue->getValue();
                    }
                    
                    // Metrics
                    $metricValues = $row->getMetricValues();
                    foreach ($metricValues as $index => $metricValue) {
                        $metricName = $metrics[$index] ?? "metric_{$index}";
                        $result[$metricName] = $metricValue->getValue();
                    }
                    
                    $results[] = $result;
                }

                return $results;
            } catch (\Exception $e) {
                Log::error('GA4 API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    public function getSessions(Carbon $startDate, Carbon $endDate): int
    {
        $results = $this->runReport(
            [],
            ['sessions'],
            $startDate,
            $endDate,
            1
        );

        return (int) ($results[0]['sessions'] ?? 0);
    }

    public function getTotalUsers(Carbon $startDate, Carbon $endDate): int
    {
        $results = $this->runReport(
            [],
            ['totalUsers'],
            $startDate,
            $endDate,
            1
        );

        return (int) ($results[0]['totalUsers'] ?? 0);
    }

    public function getScreenPageViews(Carbon $startDate, Carbon $endDate): int
    {
        $results = $this->runReport(
            [],
            ['screenPageViews'],
            $startDate,
            $endDate,
            1
        );

        return (int) ($results[0]['screenPageViews'] ?? 0);
    }

    public function getAverageSessionDuration(Carbon $startDate, Carbon $endDate): string
    {
        $results = $this->runReport(
            [],
            ['averageSessionDuration'],
            $startDate,
            $endDate,
            1
        );

        $seconds = (float) ($results[0]['averageSessionDuration'] ?? 0);
        return $this->formatDuration($seconds);
    }

    public function getBounceRate(Carbon $startDate, Carbon $endDate): float
    {
        $results = $this->runReport(
            [],
            ['bounceRate'],
            $startDate,
            $endDate,
            1
        );

        return (float) ($results[0]['bounceRate'] ?? 0);
    }

    public function getVisitsTrend(Carbon $startDate, Carbon $endDate): array
    {
        $results = $this->runReport(
            ['date'],
            ['sessions', 'totalUsers'],
            $startDate,
            $endDate,
            1000,
            [['dimension' => 'date', 'desc' => false]]
        );

        return array_map(function($row) {
            return [
                'date' => $row['date'] ?? '',
                'sessions' => (int) ($row['sessions'] ?? 0),
                'users' => (int) ($row['totalUsers'] ?? 0),
            ];
        }, $results);
    }

    public function getTrafficSources(Carbon $startDate, Carbon $endDate): array
    {
        $results = $this->runReport(
            ['sessionSource', 'sessionMedium'],
            ['sessions', 'totalUsers', 'newUsers', 'bounceRate', 'averageSessionDuration'],
            $startDate,
            $endDate,
            100,
            [['metric' => 'sessions', 'desc' => true]]
        );

        return array_map(function($row) {
            return [
                'source' => $row['sessionSource'] ?? 'direct',
                'medium' => $row['sessionMedium'] ?? '(none)',
                'sessions' => (int) ($row['sessions'] ?? 0),
                'users' => (int) ($row['totalUsers'] ?? 0),
                'new_users' => (int) ($row['newUsers'] ?? 0),
                'bounce_rate' => (float) ($row['bounceRate'] ?? 0),
                'avg_duration' => $this->formatDuration((float) ($row['averageSessionDuration'] ?? 0)),
            ];
        }, $results);
    }

    public function getLandingPages(Carbon $startDate, Carbon $endDate, int $limit = 20): array
    {
        $results = $this->runReport(
            ['landingPage'],
            ['sessions', 'totalUsers', 'bounceRate', 'averageSessionDuration'],
            $startDate,
            $endDate,
            $limit,
            [['metric' => 'sessions', 'desc' => true]]
        );

        return array_map(function($row) {
            return [
                'url' => $row['landingPage'] ?? '',
                'sessions' => (int) ($row['sessions'] ?? 0),
                'users' => (int) ($row['totalUsers'] ?? 0),
                'bounce_rate' => (float) ($row['bounceRate'] ?? 0),
                'avg_duration' => $this->formatDuration((float) ($row['averageSessionDuration'] ?? 0)),
            ];
        }, $results);
    }

    public function getTopContent(Carbon $startDate, Carbon $endDate, int $limit = 20): array
    {
        $results = $this->runReport(
            ['pagePath', 'pageTitle'],
            ['screenPageViews', 'uniquePageViews', 'averageTimeOnPage'],
            $startDate,
            $endDate,
            $limit,
            [['metric' => 'screenPageViews', 'desc' => true]]
        );

        return array_map(function($row) {
            return [
                'url' => $row['pagePath'] ?? '',
                'title' => $row['pageTitle'] ?? '',
                'pageviews' => (int) ($row['screenPageViews'] ?? 0),
                'unique_pageviews' => (int) ($row['uniquePageViews'] ?? 0),
                'avg_time' => $this->formatDuration((float) ($row['averageTimeOnPage'] ?? 0)),
            ];
        }, $results);
    }

    public function getLocationData(Carbon $startDate, Carbon $endDate): array
    {
        $results = $this->runReport(
            ['country'],
            ['sessions', 'totalUsers', 'newUsers'],
            $startDate,
            $endDate,
            100,
            [['metric' => 'sessions', 'desc' => true]]
        );

        return array_map(function($row) {
            return [
                'location' => $row['country'] ?? 'Unknown',
                'sessions' => (int) ($row['sessions'] ?? 0),
                'users' => (int) ($row['totalUsers'] ?? 0),
                'new_users' => (int) ($row['newUsers'] ?? 0),
            ];
        }, $results);
    }

    public function getContentViews(Carbon $startDate, Carbon $endDate, string $pathPattern = '/articles/*'): int
    {
        // Для получения просмотров контента нужно использовать фильтры
        // Пока возвращаем общее количество pageviews для статей
        $results = $this->runReport(
            ['pagePath'],
            ['screenPageViews'],
            $startDate,
            $endDate,
            1000
        );

        $total = 0;
        foreach ($results as $row) {
            $path = $row['pagePath'] ?? '';
            if (str_contains($path, '/articles/') || str_contains($path, '/news/')) {
                $total += (int) ($row['screenPageViews'] ?? 0);
            }
        }

        return $total;
    }

    protected function formatDuration(float $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = floor($seconds % 60);

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }
}

