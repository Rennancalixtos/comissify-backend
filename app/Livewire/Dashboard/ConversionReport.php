<?php

namespace App\Livewire\Dashboard;

use App\Services\ShopeeAffiliateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConversionReport extends Component
{
    // ─── Filters ───────────────────────────────────────────────────────────────
    public string $period = '30';   // days: 7, 30, 90, or 'custom'
    public string $dateStart = '';
    public string $dateEnd   = '';
    public string $orderStatus = '';

    // ─── State ─────────────────────────────────────────────────────────────────
    public array  $nodes    = [];
    public array  $pageInfo = [];
    public ?string $error   = null;
    public bool   $loading  = false;
    public bool   $hasAccess = false;

    // ─── Pagination ────────────────────────────────────────────────────────────
    public ?string $scrollId     = null;
    public array   $scrollStack  = [];  // stack for back-navigation
    public int     $currentPage  = 1;

    public function mount(): void
    {
        $user = Auth::user();
        $this->hasAccess = $user->hasReportAccess();

        if ($this->hasAccess) {
            $this->loadReport();
        }
    }

    public function updatedPeriod(): void
    {
        $this->resetPagination();
        if ($this->period !== 'custom') {
            $this->loadReport();
        }
    }

    public function updatedOrderStatus(): void
    {
        $this->resetPagination();
        $this->loadReport();
    }

    public function applyCustomDates(): void
    {
        $this->resetPagination();
        $this->loadReport();
    }

    public function nextPage(): void
    {
        if (empty($this->pageInfo['hasNextPage']) || empty($this->pageInfo['scrollId'])) {
            return;
        }

        $this->scrollStack[] = $this->scrollId;
        $this->scrollId      = $this->pageInfo['scrollId'];
        $this->currentPage++;
        $this->loadReport();
    }

    public function prevPage(): void
    {
        if ($this->currentPage <= 1) {
            return;
        }

        $this->scrollId  = array_pop($this->scrollStack);
        $this->currentPage--;
        $this->loadReport();
    }

    private function resetPagination(): void
    {
        $this->scrollId     = null;
        $this->scrollStack  = [];
        $this->currentPage  = 1;
    }

    private function loadReport(): void
    {
        $user = Auth::user();

        if (! $user->hasReportAccess()) {
            $this->hasAccess = false;
            return;
        }

        $cred = $user->credential;
        if (! $cred?->is_connected) {
            $this->error = 'Conecte sua conta Shopee para ver o relatório.';
            return;
        }

        $this->loading = true;
        $this->error   = null;

        [$start, $end] = $this->resolveDateRange();

        $params = array_filter([
            'purchaseTimeStart' => $start,
            'purchaseTimeEnd'   => $end,
            'orderStatus'       => $this->orderStatus ?: null,
            'limit'             => 50,
            'scrollId'          => $this->scrollId,
        ], fn ($v) => $v !== null);

        $service  = app(ShopeeAffiliateService::class);
        $result   = $service->getConversionReport($cred, $params);

        $this->loading = false;

        if (isset($result['error'])) {
            $this->error = $result['error'];
            return;
        }

        $this->nodes    = $result['nodes'];
        $this->pageInfo = $result['pageInfo'];
    }

    private function resolveDateRange(): array
    {
        if ($this->period === 'custom') {
            $start = $this->dateStart ? strtotime($this->dateStart . ' 00:00:00') : null;
            $end   = $this->dateEnd   ? strtotime($this->dateEnd   . ' 23:59:59') : null;
            return [$start, $end];
        }

        $days = (int) $this->period;
        return [strtotime("-{$days} days"), time()];
    }

    // ─── Summary helpers ───────────────────────────────────────────────────────

    public function getTotalCommissionProperty(): string
    {
        $total = collect($this->nodes)->sum(fn ($n) => (float) $n['totalCommission']);
        return number_format($total, 2, ',', '.');
    }

    public function getNetCommissionProperty(): string
    {
        $total = collect($this->nodes)->sum(fn ($n) => (float) $n['netCommission']);
        return number_format($total, 2, ',', '.');
    }

    public function getPendingCountProperty(): int
    {
        return collect($this->nodes)->filter(function ($n) {
            foreach ($n['orders'] ?? [] as $order) {
                if ($order['orderStatus'] === 'PENDING') return true;
            }
            return false;
        })->count();
    }

    public function getCompletedCountProperty(): int
    {
        return collect($this->nodes)->filter(function ($n) {
            foreach ($n['orders'] ?? [] as $order) {
                if ($order['orderStatus'] === 'COMPLETED') return true;
            }
            return false;
        })->count();
    }

    public function render()
    {
        return view('livewire.dashboard.conversion-report')
            ->layout('layouts.app', ['title' => 'Relatório de Conversões']);
    }
}
