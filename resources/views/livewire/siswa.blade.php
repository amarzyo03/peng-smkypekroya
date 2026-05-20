<div class="relative mb-6 w-full">
    <flux:heading size="xl" level="1">{{ __('Siswa') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Halaman manage siswa') }}</flux:subheading>
    <flux:separator variant="subtle" class="my-6" />


    <div class="flex w-full items-center justify-end gap-3 md:w-auto mb-2">

        <flux:input icon="magnifying-glass" placeholder="Search members" size="sm"
            wire:model.live.debounce.300ms="search" class="w-full" />

        <flux:button variant="primary" color="emerald" size="sm" wire:click="Import">
            Import
        </flux:button>

        <flux:modal.trigger name="modals-member">
            <flux:button variant="primary" color="blue" size="sm" wire:click="create">
                + Add
            </flux:button>
        </flux:modal.trigger>

    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>#</flux:table.column>
            <flux:table.column>Customer</flux:table.column>
            <flux:table.column>Date</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Amount</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            <flux:table.row>
                <flux:table.cell>1</flux:table.cell>
                <flux:table.cell>Lindsey Aminoff</flux:table.cell>
                <flux:table.cell>Jul 29, 10:45 AM</flux:table.cell>
                <flux:table.cell>
                    <flux:badge color="green" size="sm" inset="top bottom">Paid</flux:badge>
                </flux:table.cell>
                <flux:table.cell variant="strong">$49.00</flux:table.cell>
                <flux:table.cell>
                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                    </flux:button>
                </flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</div>
