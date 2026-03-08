<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Services\ThemeService;
use Livewire\Component;

class Settings extends Component
{
    public array  $settings   = [];
    public ?int   $editingId  = null;
    public string $editValue  = '';
    public bool   $showCreate = false;

    // New setting form
    public string $newKey         = '';
    public string $newValue       = '';
    public string $newDescription = '';

    // Theme
    public string $activeTheme = 'purple';

    protected function createRules(): array
    {
        return [
            'newKey'         => 'required|string|max:100|unique:settings,key',
            'newValue'       => 'required|string',
            'newDescription' => 'nullable|string|max:255',
        ];
    }

    public function mount(): void
    {
        $this->loadSettings();
        $this->activeTheme = Setting::get('theme_color', 'purple');
    }

    private function loadSettings(): void
    {
        $this->settings = Setting::orderBy('key')->get()->toArray();
    }

    public function startEdit(int $id): void
    {
        $setting = Setting::findOrFail($id);
        $this->editingId = $id;
        $this->editValue = $setting->value ?? '';
    }

    public function saveEdit(): void
    {
        $this->validate(['editValue' => 'required|string']);

        Setting::findOrFail($this->editingId)->update(['value' => $this->editValue]);
        Setting::flushCache();

        $this->editingId = null;
        $this->loadSettings();
        session()->flash('success', 'Configuração salva.');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function saveNew(): void
    {
        $this->validate($this->createRules());

        Setting::create([
            'key'         => $this->newKey,
            'value'       => $this->newValue,
            'description' => $this->newDescription ?: null,
        ]);

        $this->reset(['newKey', 'newValue', 'newDescription']);
        $this->showCreate = false;
        $this->loadSettings();
        session()->flash('success', 'Configuração criada.');
    }

    public function deleteSetting(int $id): void
    {
        Setting::findOrFail($id)->delete();
        Setting::flushCache();
        $this->loadSettings();
        session()->flash('success', 'Configuração removida.');
    }


    public function setTheme(string $slug): void
    {
        if (!array_key_exists($slug, ThemeService::THEME_NAMES)) return;

        Setting::set('theme_color', $slug, 'Tema de cor da plataforma');
        Setting::flushCache();

        $this->activeTheme = $slug;
        session()->flash('success', 'Tema atualizado!');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'themes' => ThemeService::allThemes(),
        ])->layout('layouts.admin', ['title' => 'Configurações']);
    }
}
