<x-filament-panels::page>
    @if($this->status)
        @if($this->isFirstSetup)
            @include('filament.simple-loading', ['status' => $this->status])
        @else
            <x-filament::section>
                @include('filament.simple-loading', ['status' => $this->status])
            </x-filament::section>
        @endif
    @else
        <div x-data="{ submitting: false }">
            {{-- Loading shown immediately on submit --}}
            <div x-show="submitting" x-cloak>
                @if($this->isFirstSetup)
                    @include('filament.simple-loading', ['status' => 'loading'])
                @else
                    <x-filament::section>
                        @include('filament.simple-loading', ['status' => 'loading'])
                    </x-filament::section>
                @endif
            </div>

            <form wire:submit="save" x-on:submit="submitting = true" x-show="!submitting">
                @if($this->isFirstSetup)
                    <div style="margin-bottom: 2rem; text-align: center;">
                        <div style="margin-bottom: 1rem; display: flex; justify-content: center;">
                            <div style="border-radius: 9999px; background: rgba(var(--primary-500), 0.1); padding: 1rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 2.5rem; height: 2.5rem; color: var(--primary-500);">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </div>
                        </div>
                        <h2 style="font-size: 1.875rem; font-weight: 800; margin: 0;">
                            Bem-vindo ao sistema!
                        </h2>
                        <p style="margin: 0.5rem auto 0; max-width: 28rem; font-size: 1rem; color: var(--gray-500);">
                            Configure suas taxas e o token da API para começar a gerenciar seus repasses.
                        </p>
                    </div>

                    <x-filament::section>
                        {{ $this->form }}

                        <x-slot name="footer">
                            <div style="display: flex; justify-content: center;">
                                <x-filament::button type="submit" size="xl">
                                    Começar
                                </x-filament::button>
                            </div>
                        </x-slot>
                    </x-filament::section>
                @else
                    <x-filament::section>
                        {{ $this->form }}

                        <x-slot name="footer">
                            <div class="flex justify-end">
                                <x-filament::button type="submit" size="lg">
                                    Salvar
                                </x-filament::button>
                            </div>
                        </x-slot>
                    </x-filament::section>
                @endif
            </form>
        </div>
    @endif
</x-filament-panels::page>
