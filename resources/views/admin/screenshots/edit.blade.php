@extends('layouts.admin', ['title' => 'Editar Screenshot'])

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.screenshots.index') }}" class="text-d-muted hover:text-d-text text-sm">← Voltar</a>
        <h1 class="text-lg font-bold text-d-text">Editar Screenshot</h1>
    </div>

    @if($errors->any())
    <div class="alert-error mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('admin.screenshots.update', $screenshot) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs text-d-muted mb-1.5">Título (opcional)</label>
                <input name="title" type="text" value="{{ old('title', $screenshot->title) }}"
                       class="form-input w-full" placeholder="Ex: Dashboard de produtos"/>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Ordem</label>
                    <input name="order" type="number" min="0" value="{{ old('order', $screenshot->order) }}" class="form-input w-full"/>
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 text-sm text-d-text cursor-pointer">
                        <input name="is_active" type="checkbox" value="1" {{ $screenshot->is_active ? 'checked' : '' }}
                               class="rounded border-d-border bg-d-input"/> Ativo
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs text-d-muted mb-1.5">Imagem (deixe vazio para manter)</label>
                @if($screenshot->image_path)
                <img src="{{ Storage::url($screenshot->image_path) }}" class="w-full h-32 object-cover rounded-lg mb-2 bg-d-input"/>
                @endif
                <input name="image" type="file" accept="image/*"
                       class="form-input w-full !py-1.5 text-xs"/>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 justify-center !py-2">Salvar</button>
                <a href="{{ route('admin.screenshots.index') }}" class="btn-secondary flex-1 text-center !py-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
