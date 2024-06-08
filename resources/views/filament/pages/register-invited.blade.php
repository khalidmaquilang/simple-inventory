<x-filament-panels::card>
    <x-filament-panels::form wire:submit="register">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::card>
