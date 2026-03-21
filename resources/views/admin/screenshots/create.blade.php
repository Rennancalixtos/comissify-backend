@extends('layouts.admin', ['title' => 'Novo Screenshot'])

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.screenshots.index') }}" class="text-d-muted hover:text-d-text text-sm">← Voltar</a>
        <h1 class="text-lg font-bold text-d-text">Novo Screenshot</h1>
    </div>

    @if($errors->any())
    <div class="alert-error mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('admin.screenshots.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs text-d-muted mb-1.5">Título (opcional)</label>
                <input name="title" type="text" value="{{ old('title') }}"
                       class="form-input w-full" placeholder="Ex: Dashboard de produtos"/>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Ordem</label>
                    <input name="order" type="number" min="0" value="{{ old('order', 0) }}" class="form-input w-full"/>
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 text-sm text-d-text cursor-pointer">
                        <input name="is_active" type="checkbox" value="1" checked class="rounded border-d-border bg-d-input"/> Ativo
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs text-d-muted mb-1.5">Imagem *</label>
                <input name="image" type="file" accept="image/*" required
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
