<x-filament-panels::page>
    {{ $this->form }}

    {{-- ⭐ BOTÕES DO FILAMENT --}}
    <x-filament-panels::form.actions
        :actions="$this->getFormActions()"
    />

    <x-filament::modal
        id="register-link-modal"
        width="xl"
        x-on:open-register-link-modal.window="$dispatch('open-modal', { id: 'register-link-modal' })"
    >
        <x-slot name="heading">
            Link de cadastro do cliente
        </x-slot>

        <div class="space-y-4">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="text"
                    :value="$this->registerUrl"
                    readonly
                    x-ref="linkInput"
                />
            </x-filament::input.wrapper>

            <x-filament::button
                icon="heroicon-o-clipboard"
                x-on:click="navigator.clipboard.writeText($refs.linkInput.value)"
            >
                Copiar link
            </x-filament::button>
        </div>
    </x-filament::modal>
</x-filament-panels::page>