<?php

namespace App\Livewire\Admin;

use App\Models\LandingScreenshot;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Component;
use Livewire\WithFileUploads;

class Screenshots extends Component
{
    use WithFileUploads;

    public $screenshots = [];
    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields
    public string $title = '';
    public int    $order  = 0;
    public bool   $is_active = true;
    public $image; // uploaded file

    public function mount(): void
    {
        $this->loadScreenshots();
    }

    private function loadScreenshots(): void
    {
        $this->screenshots = LandingScreenshot::orderBy('order')->get()->toArray();
    }

    public function openNew(): void
    {
        $this->reset(['editingId', 'title', 'order', 'image']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $s = LandingScreenshot::findOrFail($id);
        $this->editingId = $id;
        $this->title     = $s->title ?? '';
        $this->order     = $s->order;
        $this->is_active = $s->is_active;
        $this->image     = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'title'  => 'nullable|string|max:200',
            'order'  => 'required|integer|min:0',
            'image'  => $this->editingId ? 'nullable|image|max:4096' : 'required|image|max:4096',
        ]);

        $path = null;
        if ($this->image) {
            $manager  = new ImageManager(new Driver());
            $img      = $manager->read($this->image->getRealPath());
            $img->cover(1600, 1000);

            $filename = 'landing/' . \Illuminate\Support\Str::uuid() . '.jpg';
            $fullPath = storage_path('app/public/' . $filename);
            @mkdir(dirname($fullPath), 0755, true);
            $img->toJpeg(90)->save($fullPath);
            $path = $filename;
        }

        if ($this->editingId) {
            $s = LandingScreenshot::findOrFail($this->editingId);
            $s->update(array_filter([
                'title'     => $this->title ?: null,
                'order'     => $this->order,
                'is_active' => $this->is_active,
                'image_path'=> $path,
            ], fn ($v) => $v !== null));
        } else {
            LandingScreenshot::create([
                'title'      => $this->title ?: null,
                'image_path' => $path,
                'order'      => $this->order,
                'is_active'  => $this->is_active,
            ]);
        }

        $this->showModal = false;
        $this->loadScreenshots();
        session()->flash('success', 'Screenshot salvo.');
    }

    public function delete(int $id): void
    {
        LandingScreenshot::findOrFail($id)->delete();
        $this->loadScreenshots();
    }

    public function toggleActive(int $id): void
    {
        $s = LandingScreenshot::findOrFail($id);
        $s->update(['is_active' => !$s->is_active]);
        $this->loadScreenshots();
    }

    public function render()
    {
        return view('livewire.admin.screenshots')
            ->layout('layouts.admin', ['title' => 'Landing — Screenshots']);
    }
}
