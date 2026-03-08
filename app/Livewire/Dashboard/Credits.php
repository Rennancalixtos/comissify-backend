<?php

namespace App\Livewire\Dashboard;

use App\Models\Setting;
use App\Models\TokenPackage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Credits extends Component
{
    public function render()
    {
        return view('livewire.dashboard.credits', [
            'packages'  => TokenPackage::active()->orderBy('price')->get(),
            'balance'   => Auth::user()->tokenBalance(),
            'whatsapp'  => Setting::get('contact.whatsapp'),
        ])->layout('layouts.app', ['title' => 'Comprar Créditos']);
    }
}
