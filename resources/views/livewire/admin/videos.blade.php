<div class="space-y-4">

    @if (session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por produto..."
               class="form-input w-64" />
        <select wire:model.live="validationStatus" class="form-select w-44">
            <option value="">Todos status</option>
            <option value="pending">Pendente</option>
            <option value="valid">Válido</option>
            <option value="normalized">Normalizado</option>
            <option value="invalid">Inválido</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wide">
                    <th class="px-4 py-3">Produto</th>
                    <th class="px-4 py-3">Resolução</th>
                    <th class="px-4 py-3">Duração</th>
                    <th class="px-4 py-3">Tamanho</th>
                    <th class="px-4 py-3">Codec</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($videos as $video)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800 max-w-[200px] truncate">
                            {{ $video->product->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $video->resolution ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $video->duration_seconds ? $video->duration_seconds.'s' : '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $video->file_size_mb ? $video->file_size_mb.'MB' : '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 uppercase">{{ $video->codec ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($video->validation_status) {
                                    'valid', 'normalized' => 'badge-green',
                                    'pending'             => 'badge-yellow',
                                    'invalid'             => 'badge-red',
                                    default               => 'badge-gray',
                                };
                            @endphp
                            <span class="{{ $badge }}">{{ $video->validation_status }}</span>
                            @if ($video->rejection_reason)
                                <p class="text-xs text-red-500 mt-1 max-w-[160px] truncate" title="{{ $video->rejection_reason }}">
                                    {{ $video->rejection_reason }}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="confirmDeleteVideo({{ $video->id }})" class="text-red-600 hover:underline text-xs">Excluir</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhum vídeo encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $videos->links() }}</div>
    </div>

    @if ($confirmDelete)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4 text-center">
                <p class="text-gray-700">Excluir este vídeo e o arquivo local?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('confirmDelete', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="deleteVideo" class="btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    @endif

</div>
