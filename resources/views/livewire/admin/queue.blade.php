<div class="space-y-4" x-data="queuePage()">

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <select wire:model.live="status" class="form-select w-40">
            <option value="">Todos status</option>
            <option value="pending">Pendente</option>
            <option value="processing">Processando</option>
            <option value="posted">Postado</option>
            <option value="failed">Falhou</option>
            <option value="cancelled">Cancelado</option>
        </select>
        <select wire:model.live="accountId" class="form-select w-48">
            <option value="">Todas as contas</option>
            @foreach ($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->name }}</option>
            @endforeach
        </select>
        <div class="ml-auto">
            <button wire:click="openCreate" class="btn-primary">+ Adicionar à Fila</button>
        </div>
    </div>

    {{-- Cards de fila (substituindo tabela para acomodar player + inline edit) --}}
    <div class="space-y-4">
        @forelse ($queue as $item)
            @php
                $videoPath  = $item->video?->local_path ? asset('storage/'.ltrim(str_replace('public/', '', $item->video->local_path), '/')) : null;
                $thumbPath  = $item->video ? asset('storage/thumbnails/'.$item->video->id.'.jpg') : null;
                $badge      = match($item->status) {
                    'posted'     => 'badge-green',
                    'pending'    => 'badge-yellow',
                    'processing' => 'badge-blue',
                    'failed'     => 'badge-red',
                    default      => 'badge-gray',
                };
            @endphp

            <div class="card" x-data="{
                    title: @js($item->title),
                    description: @js($item->description ?? ''),
                    copied: false,
                }">

                <div class="flex gap-4">

                    {{-- ── Player / Thumbnail ──────────────────────────────── --}}
                    <div class="flex-shrink-0 w-40">
                        @if ($videoPath)
                            <video
                                class="w-40 h-24 rounded-lg object-cover bg-black"
                                controls
                                preload="metadata"
                                poster="{{ $thumbPath }}"
                                src="{{ $videoPath }}"
                            ></video>
                        @elseif ($thumbPath)
                            <img src="{{ $thumbPath }}" class="w-40 h-24 rounded-lg object-cover bg-gray-100" alt="thumb" />
                        @else
                            <div class="w-40 h-24 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 text-xs">sem vídeo</div>
                        @endif

                        {{-- Download link --}}
                        @if ($videoPath)
                            <a href="{{ $videoPath }}" download
                               class="mt-1 flex items-center gap-1 text-xs text-blue-600 hover:underline">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        @endif
                    </div>

                    {{-- ── Conteúdo editável ───────────────────────────────── --}}
                    <div class="flex-1 min-w-0 space-y-2">

                        {{-- Produto + Status --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                {{ $item->video->product->name ?? '—' }}
                            </span>
                            <span class="{{ $badge }}">{{ $item->status }}</span>
                            <span class="text-xs text-gray-400">· Prio {{ $item->priority }} · {{ $item->account->name ?? '—' }}</span>
                            @if ($item->scheduled_at)
                                <span class="text-xs text-gray-400">· {{ $item->scheduled_at->format('d/m H:i') }}</span>
                            @endif
                        </div>

                        {{-- Título inline --}}
                        <div>
                            <label class="block text-xs text-gray-400 mb-0.5">Título</label>
                            <input
                                x-model="title"
                                type="text"
                                maxlength="100"
                                class="form-input w-full text-sm font-medium"
                                @change="$wire.saveField({{ $item->id }}, 'title', title)"
                            />
                            <p class="text-xs text-gray-300 text-right" x-text="title.length + '/100'"></p>
                        </div>

                        {{-- Descrição inline --}}
                        <div>
                            <label class="block text-xs text-gray-400 mb-0.5">Descrição</label>
                            <textarea
                                x-model="description"
                                rows="2"
                                class="form-input w-full text-sm"
                                @change="$wire.saveField({{ $item->id }}, 'description', description)"
                            ></textarea>
                        </div>

                        {{-- Hashtags + link --}}
                        <div class="flex gap-3 text-xs text-gray-500">
                            <span class="truncate">{{ $item->hashtags_string }}</span>
                            @if ($item->affiliate_link)
                                <a href="{{ $item->affiliate_link }}" target="_blank" class="text-shopee-600 hover:underline truncate flex-shrink-0">🔗 link</a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Barra de ações ──────────────────────────────────────── --}}
                <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100">

                    {{-- Copiar tudo --}}
                    <button
                        @click="
                            const text = title + '\n\n' + description + '\n\n'
                                + @js($item->hashtags_string) + '\n\n'
                                + '🔗 ' + @js($item->affiliate_link ?? '');
                            navigator.clipboard.writeText(text);
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        class="btn-secondary text-xs py-1.5"
                        :class="copied ? 'bg-green-50 border-green-300 text-green-700' : ''"
                    >
                        <span x-show="!copied">Copiar tudo</span>
                        <span x-show="copied">Copiado!</span>
                    </button>

                    {{-- Marcar como Postado --}}
                    @if (in_array($item->status, ['pending', 'processing']))
                        <button wire:click="markAsPosted({{ $item->id }})"
                                wire:confirm="Confirmar que este vídeo foi postado manualmente?"
                                class="btn-primary text-xs py-1.5">
                            Marcar como Postado
                        </button>
                    @endif

                    {{-- Rejeitar e buscar novo --}}
                    @if (in_array($item->status, ['pending', 'failed']))
                        <button wire:click="rejectAndRequeue({{ $item->id }})"
                                wire:confirm="Rejeitar este vídeo e iniciar novo download para o mesmo produto?"
                                class="text-xs py-1.5 px-3 rounded-md border border-orange-300 text-orange-600 hover:bg-orange-50 transition">
                            Rejeitar e buscar novo
                        </button>
                    @endif

                    {{-- Retentar --}}
                    @if ($item->status === 'failed')
                        <button wire:click="retryItem({{ $item->id }})" class="text-xs py-1.5 px-3 rounded-md border border-blue-300 text-blue-600 hover:bg-blue-50 transition">
                            Retentar
                        </button>
                    @endif

                    {{-- Cancelar / Excluir --}}
                    @if (in_array($item->status, ['pending', 'processing']))
                        <button wire:click="cancelItem({{ $item->id }})" class="text-xs text-yellow-600 hover:underline ml-auto">Cancelar</button>
                    @endif
                    <button wire:click="confirmDeleteItem({{ $item->id }})" class="text-xs text-red-500 hover:underline {{ in_array($item->status, ['pending', 'processing']) ? '' : 'ml-auto' }}">Excluir</button>

                    {{-- Erro --}}
                    @if ($item->error_message)
                        <p class="w-full text-xs text-red-500 mt-1 truncate" title="{{ $item->error_message }}">
                            Erro: {{ $item->error_message }}
                        </p>
                    @endif
                </div>

            </div>
        @empty
            <div class="card text-center text-gray-400 py-12">
                Fila vazia. Os itens aparecem aqui após o pipeline de IA processar os produtos.
            </div>
        @endforelse
    </div>

    <div>{{ $queue->links() }}</div>

    {{-- Modal: novo item --}}
    @if ($showForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 overflow-y-auto py-6">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 space-y-4">
                <h2 class="text-lg font-semibold">Novo Item na Fila</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Vídeo *</label>
                        <select wire:model="videoId" class="form-select w-full">
                            <option value="">Selecione...</option>
                            @foreach ($videos as $video)
                                <option value="{{ $video->id }}">{{ $video->product->name ?? $video->id }} ({{ $video->resolution }})</option>
                            @endforeach
                        </select>
                        @error('videoId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Conta *</label>
                        <select wire:model="formAccountId" class="form-select w-full">
                            <option value="">Selecione...</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->remaining_posts }} posts restantes)</option>
                            @endforeach
                        </select>
                        @error('formAccountId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Título *</label>
                        <input wire:model="title" type="text" maxlength="100" class="form-input w-full" />
                        @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea wire:model="description" rows="3" class="form-input w-full"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Hashtags (separadas por vírgula)</label>
                        <input wire:model="hashtags" type="text" class="form-input w-full" placeholder="#shopee, #oferta" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Link Afiliado</label>
                            <input wire:model="affiliateLink" type="url" class="form-input w-full" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Prioridade (1=alta)</label>
                            <input wire:model="priority" type="number" min="1" max="10" class="form-input w-full" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Agendar para</label>
                        <input wire:model="scheduledAt" type="datetime-local" class="form-input w-full" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('showForm', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="save" class="btn-primary">Adicionar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: confirmar exclusão --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4 text-center">
                <p class="text-gray-700">Excluir este item da fila?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('confirmDelete', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="deleteItem" class="btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    @endif

</div>

@script
<script>
    function queuePage() {
        return {};
    }
</script>
@endscript
