<x-filament-panels::page>
    <form wire:submit="save">
        <x-filament::section>
            {{ $this->form }}

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-filament::button type="submit" size="lg">
                        Salvar
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::section>
    </form>
</x-filament-panels::page>