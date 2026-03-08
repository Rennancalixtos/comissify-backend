<?php

namespace App\Livewire\Admin;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Accounts extends Component
{
    use WithPagination;

    public string $search        = '';
    public bool   $showForm      = false;
    public bool   $confirmDelete = false;
    public ?int   $editingId     = null;
    public ?int   $deleteId      = null;

    // Form fields
    public string $name            = '';
    public string $shopeeUsername  = '';
    public int    $dailyPostLimit  = 10;
    public string $accountStatus   = 'active';

    protected $queryString = ['search'];

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|max:100',
            'shopeeUsername' => 'required|string|max:100|unique:accounts,shopee_username,'.($this->editingId ?? 'NULL'),
            'dailyPostLimit' => 'required|integer|min:1|max:50',
            'accountStatus'  => 'required|in:active,inactive,banned,cooldown',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'shopeeUsername']);
        $this->dailyPostLimit = 10;
        $this->accountStatus  = 'active';
        $this->showForm       = true;
    }

    public function openEdit(int $id): void
    {
        $account = Account::findOrFail($id);
        $this->editingId      = $id;
        $this->name           = $account->name;
        $this->shopeeUsername = $account->shopee_username;
        $this->dailyPostLimit = $account->daily_post_limit;
        $this->accountStatus  = $account->status;
        $this->showForm       = true;
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            'name'             => $this->name,
            'shopee_username'  => $this->shopeeUsername,
            'daily_post_limit' => $this->dailyPostLimit,
            'status'           => $this->accountStatus,
        ];

        if ($this->editingId) {
            Account::findOrFail($this->editingId)->update($payload);
            session()->flash('success', 'Conta atualizada.');
        } else {
            Account::create($payload);
            session()->flash('success', 'Conta criada.');
        }

        $this->showForm = false;
    }

    public function resetPostCount(int $id): void
    {
        Account::findOrFail($id)->update(['posts_today' => 0]);
        session()->flash('success', 'Contador de posts zerado.');
    }

    public function confirmDeleteAccount(int $id): void
    {
        $this->deleteId      = $id;
        $this->confirmDelete = true;
    }

    public function deleteAccount(): void
    {
        Account::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId      = null;
        session()->flash('success', 'Conta removida.');
    }

    public function render()
    {
        $accounts = Account::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('shopee_username', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.accounts', compact('accounts'))
            ->layout('layouts.admin', ['title' => 'Contas']);
    }
}
