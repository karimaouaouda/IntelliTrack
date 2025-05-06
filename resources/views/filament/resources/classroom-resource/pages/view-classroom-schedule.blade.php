<x-filament::page>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold tracking-tight">
                Schedule for {{ $this->record->name }}
            </h2>
        </div>

        {{ $this->table }}
    </div>
</x-filament::page> 