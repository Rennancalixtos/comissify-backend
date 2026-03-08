<?php

namespace App\Livewire\Admin;

use App\Models\Video;
use Livewire\Component;
use Livewire\WithPagination;

class Videos extends Component
{
    use WithPagination;

    public string $search           = '';
    public string $validationStatus = '';
    public bool   $confirmDelete    = false;
    public ?int   $deleteId         = null;

    protected $queryString = ['search', 'validationStatus'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDeleteVideo(int $id): void
    {
        $this->deleteId      = $id;
        $this->confirmDelete = true;
    }

    public function deleteVideo(): void
    {
        $video = Video::findOrFail($this->deleteId);

        // Remove local file
        if ($video->local_path && file_exists($video->absolute_path)) {
            @unlink($video->absolute_path);
        }

        $video->delete();
        $this->confirmDelete = false;
        $this->deleteId      = null;
        session()->flash('success', 'Vídeo removido.');
    }

    public function render()
    {
        $videos = Video::with('product')
            ->when($this->search, fn ($q) => $q->whereHas('product', fn ($p) => $p->where('name', 'like', "%{$this->search}%")))
            ->when($this->validationStatus, fn ($q) => $q->where('validation_status', $this->validationStatus))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.videos', compact('videos'))
            ->layout('layouts.admin', ['title' => 'Vídeos']);
    }
}
