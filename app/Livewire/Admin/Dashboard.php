<?php

namespace App\Livewire\Admin;

use App\Models\PaymentOrder;
use App\Models\User;
use App\Models\UserCredential;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $metrics = [
            'sales_today'        => PaymentOrder::where('status', 'approved')->whereDate('paid_at', today())->count(),
            'sales_month'        => PaymentOrder::where('status', 'approved')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->count(),
            'revenue_today'      => PaymentOrder::where('status', 'approved')->whereDate('paid_at', today())->sum('amount'),
            'revenue_month'      => PaymentOrder::where('status', 'approved')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            'revenue_total'      => PaymentOrder::where('status', 'approved')->sum('amount'),
            'users_total'        => User::count(),
            'users_connected'    => UserCredential::count(),
        ];

        $scrapeCredits = $this->fetchScrapeCredits();

        return view('livewire.admin.dashboard', compact('metrics', 'scrapeCredits'))
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }

    private function fetchScrapeCredits(): array
    {
        return Cache::remember('scrapecreators.credits', now()->addMinutes(5), function () {
            try {
                $response = Http::timeout(5)
                    ->withHeader('x-api-key', config('services.pinterest.scraper_key'))
                    ->get('https://api.scrapecreators.com/v1/credit-balance');

                if ($response->successful()) {
                    return ['ok' => true, 'data' => $response->json(), 'cached_at' => now()->toIso8601String()];
                }

                return ['ok' => false, 'error' => 'HTTP '.$response->status()];
            } catch (\Throwable $e) {
                return ['ok' => false, 'error' => $e->getMessage()];
            }
        });
    }
}
