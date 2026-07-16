<div class="relative w-full">
    {{-- Header --}}
    <flux:heading size="xl" level="1"> Import Data Siswa </flux:heading>
    <flux:subheading size="lg"> Import data siswa dari file Microsoft Excel (.xlsx)</flux:subheading>
    <flux:separator variant="subtle" class="my-6" />

    {{-- Download Template --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <flux:text class="text-zinc-500">Import data siswa dengan format yang sesuai | Template tersedia</flux:text>
        <div class="flex flex-wrap gap-2">
            <flux:button variant="primary" color="red" icon="trash" wire:click="truncateStudents">
                Hapus Semua Siswa
            </flux:button>
            <flux:button variant="primary" color="green" icon="arrow-down-tray" wire:click="downloadTemplateSiswa">
                Download Template
            </flux:button>
        </div>
    </div>

    {{-- Form Import --}}
    <form wire:submit="import" class="max-auto space-y-6">
        <flux:card>
            <flux:field>
                <flux:label>File Excel Data Siswa</flux:label>
                <label
                    class="flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-zinc-300 p-8 text-center cursor-pointer transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900">
                    <flux:icon.document-arrow-up class="size-10 text-zinc-400" />
                    <div>
                        <p class="font-medium">Klik untuk memilih file Excel</p>
                        <p class="text-sm text-zinc-500">Format .xlsx atau .xls</p>
                        <p class="text-xs text-zinc-400 mt-1">Maksimal ukuran file 2 MB</p>
                    </div>
                    <input type="file" wire:model="file" accept=".xlsx,.xls" class="hidden" />
                </label>

                {{-- Loading --}}
                <div wire:loading wire:target="file" class="mt-3">
                    <flux:badge color="blue">Mengunggah file...</flux:badge>
                </div>

                {{-- Preview --}}
                @if ($file)
                    <flux:callout color="green" class="mt-3">
                        <div class="font-medium">File dipilih</div>
                        <div class="text-sm">
                            {{ $file->getClientOriginalName() }}
                        </div>
                    </flux:callout>
                @endif
                <flux:error name="file" />
            </flux:field>
        </flux:card>

        {{-- Tombol --}}
        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" wire:click="$refresh">Reset</flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="import">Import Data</span>
                <span wire:loading wire:target="import">Mengimpor...</span>
            </flux:button>
        </div>
    </form>
</div>
