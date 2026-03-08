<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public string $search = '';

    public bool   $showAddTokensModal = false;
    public ?int   $addTokensUserId    = null;
    public float  $addTokensAmount    = 0;
    public string $addTokensDesc      = 'Crédito manual pelo admin';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);
    }

    public function openAddTokens(int $userId): void
    {
        $this->addTokensUserId = $userId;
        $this->addTokensAmount = 0;
        $this->addTokensDesc   = 'Crédito manual pelo admin';
        $this->showAddTokensModal = true;
    }

    public function confirmAddTokens(): void
    {
        $this->validate([
            'addTokensAmount' => 'required|numeric|min:0.0001',
            'addTokensDesc'   => 'required|string|max:255',
        ]);

        $user = User::findOrFail($this->addTokensUserId);
        $user->ensureTokenWallet()->credit($this->addTokensAmount, $this->addTokensDesc);

        $this->showAddTokensModal = false;
    }

    public function render()
    {
        $users = User::where('is_admin', false)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->with(['token', 'credential'])
            ->latest()
            ->paginate(20);

        return view('livewire.admin.users', compact('users'))
            ->layout('layouts.admin', ['title' => 'Usuários']);
    }
}
