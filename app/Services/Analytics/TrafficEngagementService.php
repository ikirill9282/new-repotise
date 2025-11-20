<?php

namespace App\Services\Analytics;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrafficEngagementService
{
    protected ?GA4Service $ga4Service = null;

    public function __construct()
    {
        // Only initialize GA4Service if property_id is configured
        $propertyId = config('services.ga4.property_id');
        if (!$propertyId) {
            return; // GA4 not configured, will use fallback
        }
        
        try {
            $ga4Service = new GA4Service();
            // Check if GA4Service has a working client (property_id configured and client initialized)
            if ($ga4Service && method_exists($ga4Service, 'getClient')) {
                $reflection = new \ReflectionClass($ga4Service);
                $property = $reflection->getProperty('client');
                $property->setAccessible(true);
                $client = $property->getValue($ga4Service);
                
                // Only use GA4Service if it has a working client
                if ($client !== null) {
                    $this->ga4Service = $ga4Service;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('GA4Service not available: ' . $e->getMessage());
        }
    }
    
    protected function hasWorkingGA4(): bool
    {
        if (!$this->ga4Service) {
            return false;
        }
        
        // Check if GA4Service has a working client
        try {
            $reflection = new \ReflectionClass($this->ga4Service);
            $property = $reflection->getProperty('client');
            $property->setAccessible(true);
            $client = $property->getValue($this->ga4Service);
            return $client !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTotalVisits(Carbon $startDate, Carbon $endDate): int
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getSessions($startDate, $endDate);
        }
        // Fallback: count sessions from database
        return DB::table('sessions')
            ->where('last_activity', '>=', $startDate->timestamp)
            ->where('last_activity', '<=', $endDate->timestamp)
            ->count();
    }

    public function getUniqueVisitors(Carbon $startDate, Carbon $endDate): int
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getTotalUsers($startDate, $endDate);
        }
        // Fallback: count unique user sessions
        return DB::table('sessions')
            ->where('last_activity', '>=', $startDate->timestamp)
            ->where('last_activity', '<=', $endDate->timestamp)
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');
    }

    public function getPageviews(Carbon $startDate, Carbon $endDate): int
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getScreenPageViews($startDate, $endDate);
        }
        // Fallback: sum of all product and article views
        // Note: views field is cumulative (total views since creation)
        // We show total views as we don't track individual view timestamps
        $productViews = DB::table('products')->sum('views') ?? 0;
        $articleViews = DB::table('articles')->sum('views') ?? 0;
        
        return (int) ($productViews + $articleViews);
    }

    public function getAvgSessionDuration(Carbon $startDate, Carbon $endDate): string
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getAverageSessionDuration($startDate, $endDate);
        }
        // Fallback: estimate based on session lifetime (default 120 minutes)
        $sessionLifetime = config('session.lifetime', 120);
        $minutes = floor($sessionLifetime / 2); // Estimate average as half of lifetime
        return sprintf('%d:%02d', $minutes, 0);
    }

    public function getBounceRate(Carbon $startDate, Carbon $endDate): float
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getBounceRate($startDate, $endDate);
        }
        // Fallback: return 0 (no bounce rate data available)
        return 0.0;
    }

    public function getVisitsTrendData(Carbon $startDate, Carbon $endDate): array
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getVisitsTrend($startDate, $endDate);
        }
        // Fallback: group sessions by date
        $sessions = DB::table('sessions')
            ->selectRaw('DATE(FROM_UNIXTIME(last_activity)) as date')
            ->selectRaw('COUNT(DISTINCT id) as sessions')
            ->selectRaw('COUNT(DISTINCT user_id) as users')
            ->where('last_activity', '>=', $startDate->timestamp)
            ->where('last_activity', '<=', $endDate->timestamp)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return $sessions->map(function ($row) {
            return [
                'date' => $row->date,
                'sessions' => (int) $row->sessions,
                'users' => (int) $row->users,
            ];
        })->toArray();
    }

    public function getTrafficSources(Carbon $startDate, Carbon $endDate): array
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getTrafficSources($startDate, $endDate);
        }
        // Fallback: return empty array (no traffic source data available without GA4)
        return [];
    }

    public function getLandingPages(Carbon $startDate, Carbon $endDate, int $limit = 20): array
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getLandingPages($startDate, $endDate, $limit);
        }
        // Fallback: return empty array (no landing page data available without GA4)
        return [];
    }

    public function getTopContent(Carbon $startDate, Carbon $endDate, int $limit = 20): array
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getTopContent($startDate, $endDate, $limit);
        }
        // Fallback: get top products and articles by total views (all-time top)
        // Note: views is cumulative, so we show all-time top content
        $products = DB::table('products')
            ->select('id', 'title', 'slug', 'views as pageviews')
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'url' => '/products/' . $row->slug . '?pid=' . \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $row->id]),
                    'title' => $row->title,
                    'pageviews' => (int) $row->pageviews,
                    'unique_pageviews' => (int) $row->pageviews,
                    'avg_time' => '0:00',
                ];
            })
            ->toArray();
        
        $articles = DB::table('articles')
            ->select('title', 'slug', 'views as pageviews')
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'url' => '/insights/' . $row->slug,
                    'title' => $row->title,
                    'pageviews' => (int) $row->pageviews,
                    'unique_pageviews' => (int) $row->pageviews,
                    'avg_time' => '0:00',
                ];
            })
            ->toArray();
        
        $combined = array_merge($products, $articles);
        usort($combined, fn($a, $b) => $b['pageviews'] <=> $a['pageviews']);
        
        return array_slice($combined, 0, $limit);
    }

    public function getLocationData(Carbon $startDate, Carbon $endDate): array
    {
        if ($this->hasWorkingGA4()) {
            return $this->ga4Service->getLocationData($startDate, $endDate);
        }
        // Fallback: try to get location from IP addresses in sessions (basic implementation)
        $sessions = DB::table('sessions')
            ->select('ip_address')
            ->selectRaw('COUNT(DISTINCT id) as sessions')
            ->selectRaw('COUNT(DISTINCT user_id) as users')
            ->where('last_activity', '>=', $startDate->timestamp)
            ->where('last_activity', '<=', $endDate->timestamp)
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->get();
        
        // Group by country (simplified - would need IP geolocation service for real data)
        $locationData = [];
        foreach ($sessions as $session) {
            $locationData[] = [
                'location' => 'Unknown', // Would need IP geolocation to determine country
                'sessions' => (int) $session->sessions,
                'users' => (int) $session->users,
                'new_users' => 0,
            ];
        }
        
        return $locationData;
    }

    public function getPreviousPeriodValue(Carbon $startDate, Carbon $endDate, callable $callback): mixed
    {
        $daysDiff = $startDate->diffInDays($endDate);
        $prevStart = $startDate->copy()->subDays($daysDiff + 1);
        $prevEnd = $startDate->copy()->subDay();

        return $callback($prevStart, $prevEnd);
    }
}

