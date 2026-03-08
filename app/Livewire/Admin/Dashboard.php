<?php

namespace App\Livewire\Admin;

use App\Models\Account;
use App\Models\PostQueue;
use App\Models\Product;
use App\Models\Video;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $metrics = [
            'products_active'   => Product::where('status', 'active')->count(),
            'products_total'    => Product::count(),
            'videos_valid'      => Video::whereIn('validation_status', ['valid', 'normalized'])->count(),
            'videos_pending'    => Video::where('validation_status', 'pending')->count(),
            'queue_pending'     => PostQueue::where('status', 'pending')->count(),
            'queue_posted_today' => PostQueue::where('status', 'posted')
                ->whereDate('posted_at', today())->count(),
            'accounts_active'   => Account::where('status', 'active')->count(),
            'posts_today'       => Account::sum('posts_today'),
        ];

        $recentQueue = PostQueue::with(['video.product', 'account'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard', compact('metrics', 'recentQueue'))
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
