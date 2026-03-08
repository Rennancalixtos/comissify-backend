<div class="space-y-6">

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- ── Aparência / Tema ─────────────────────────────────────────── --}}
    <div class="card">
        <h3 class="text-base font-semibold text-d-text mb-1">Aparência</h3>
        <p class="text-sm text-d-muted mb-5">Escolha a cor primária da plataforma. A mudança é imediata para todos os usuários.</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($themes as $theme)
            @php $active = $activeTheme === $theme['slug']; @endphp
            <button wire:click="setTheme('{{ $theme['slug'] }}')"
                    class="relative flex flex-col items-center gap-3 p-4 rounded-xl border-2 transition-all duration-150"
                    style="{{ $active
                        ? 'border-color:' . $theme['primary'] . ';background:' . $theme['primary'] . '15'
                        : 'border-color:#2E2E2E;background:#1A1A1A' }}">
                {{-- Color swatch --}}
                <div class="w-10 h-10 rounded-full shadow-lg" style="background:{{ $theme['primary'] }}"></div>
                <div class="text-center">
                    <p class="text-sm font-semibold text-d-text">{{ $theme['name'] }}</p>
                    {{-- Sample button preview --}}
                    <div class="mt-2 px-3 py-1 rounded text-xs font-medium text-white"
                         style="background:{{ $theme['primary'] }}">Botão</div>
                </div>
                {{-- Active checkmark --}}
                @if($active)
                <div class="absolute top-2 right-2 w-5 h-5 rounded-full flex items-center justify-center text-white text-xs"
                     style="background:{{ $theme['primary'] }}">✓</div>
                @endif
            </button>
            @endforeach
        </div>
    </div>

    {{-- ── Settings Table ────────────────────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-d-muted">Configurações armazenadas no banco e cacheadas no Redis.</p>
            <button wire:click="$toggle('showCreate')" class="btn-primary !py-1.5 !px-3 text-xs">+ Nova</button>
        </div>

        {{-- New setting form --}}
        @if ($showCreate)
        <div class="card space-y-4 mb-4">
            <h3 class="text-sm font-semibold text-d-text">Nova Configuração</h3>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-d-muted mb-1">Chave *</label>
                    <input wire:model="newKey" type="text" class="form-input w-full" placeholder="discovery.my_setting" />
                    @error('newKey') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-d-muted mb-1">Valor *</label>
                    <input wire:model="newValue" type="text" class="form-input w-full" />
                    @error('newValue') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-d-muted mb-1">Descrição</label>
                    <input wire:model="newDescription" type="text" class="form-input w-full" />
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button wire:click="$set('showCreate', false)" class="btn-secondary">Cancelar</button>
                <button wire:click="saveNew" class="btn-primary">Criar</button>
            </div>
        </div>
        @endif

        {{-- Table --}}
        <div class="card !p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-d-border text-xs text-d-muted uppercase tracking-wider" style="background:#111111">
                    <tr>
                        <th class="px-4 py-3 text-left w-1/4">Chave</th>
                        <th class="px-4 py-3 text-left w-1/4">Valor</th>
                        <th class="px-4 py-3 text-left">Descrição</th>
                        <th class="px-4 py-3 text-left w-24">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-d-border">
                    @forelse ($settings as $setting)
                    <tr class="hover:bg-d-card2 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-d-muted">{{ $setting['key'] }}</td>
                        <td class="px-4 py-3">
                            @if ($editingId === $setting['id'])
                            <div class="flex items-center gap-2">
                                <input wire:model="editValue" type="text" class="form-input flex-1 !py-1 text-sm" />
                                <button wire:click="saveEdit" class="text-xs font-medium" style="color:var(--color-primary)">Salvar</button>
                                <button wire:click="cancelEdit" class="text-xs text-d-muted">×</button>
                            </div>
                            @error('editValue') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            @else
                            <span class="font-mono text-sm" style="color:var(--color-primary)">{{ $setting['value'] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-d-muted text-xs">{{ $setting['description'] ?? '—' }}</td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            @if ($editingId !== $setting['id'])
                            <button wire:click="startEdit({{ $setting['id'] }})" class="text-xs text-blue-400 hover:underline">Editar</button>
                            @endif
                            <button wire:click="deleteSetting({{ $setting['id'] }})"
                                    wire:confirm="Tem certeza que deseja remover esta configuração?"
                                    class="text-xs text-red-400 hover:underline">Excluir</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-d-muted">Nenhuma configuração.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
