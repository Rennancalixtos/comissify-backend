<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    public string  $search   = '';
    public string  $status   = '';
    public string  $sortBy   = 'opportunity_score';
    public string  $sortDir  = 'desc';

    // Form fields
    public ?int    $editingId         = null;
    public string  $name              = '';
    public string  $shopeeProductId   = '';
    public string  $category          = '';
    public string  $price             = '';
    public string  $commissionRate    = '';
    public string  $productStatus     = 'active';
    public bool    $showForm          = false;
    public bool    $confirmDelete     = false;
    public ?int    $deleteId          = null;

    protected $queryString = ['search', 'status', 'sortBy', 'sortDir'];

    protected function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'shopeeProductId' => 'required|string|max:100|unique:products,shopee_product_id,'.($this->editingId ?? 'NULL'),
            'category'        => 'nullable|string|max:100',
            'price'           => 'required|numeric|min:0',
            'commissionRate'  => 'required|numeric|min:0|max:100',
            'productStatus'   => 'required|in:active,inactive,pending',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'desc';
        }
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'shopeeProductId', 'category', 'price', 'commissionRate']);
        $this->productStatus = 'active';
        $this->showForm      = true;
    }

    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editingId       = $id;
        $this->name            = $product->name;
        $this->shopeeProductId = $product->shopee_product_id;
        $this->category        = $product->category ?? '';
        $this->price           = (string) $product->price;
        $this->commissionRate  = (string) $product->commission_rate;
        $this->productStatus   = $product->status;
        $this->showForm        = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        $payload = [
            'name'            => $this->name,
            'shopee_product_id' => $this->shopeeProductId,
            'category'        => $this->category ?: null,
            'price'           => $this->price,
            'commission_rate' => $this->commissionRate,
            'status'          => $this->productStatus,
        ];

        if ($this->editingId) {
            Product::findOrFail($this->editingId)->update($payload);
            session()->flash('success', 'Produto atualizado.');
        } else {
            Product::create($payload);
            session()->flash('success', 'Produto criado.');
        }

        $this->showForm = false;
        $this->reset(['editingId', 'name', 'shopeeProductId', 'category', 'price', 'commissionRate']);
    }

    public function confirmDeleteProduct(int $id): void
    {
        $this->deleteId      = $id;
        $this->confirmDelete = true;
    }

    public function deleteProduct(): void
    {
        Product::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId      = null;
        session()->flash('success', 'Produto removido.');
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('shopee_product_id', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);

        return view('livewire.admin.products', compact('products'))
            ->layout('layouts.admin', ['title' => 'Produtos']);
    }
}
