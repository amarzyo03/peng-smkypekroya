<div class="relative w-full">
    {{-- Header --}}
    <flux:heading size="xl" level="1">Upload SK Kelulusan</flux:heading>
    <flux:subheading size="lg">Upload template SK Kelulusan</flux:subheading>
    <flux:separator variant="subtle" class="my-6" />

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <flux:text class="text-zinc-500">{{ $this->templateSkStatus['message'] }}</flux:text>
        <div class="flex flex-wrap gap-2">
            <flux:button variant="filled" icon="trash" wire:click="cleanupTempSkFiles">
                Bersihkan File Temp
            </flux:button>
            <flux:button variant="primary" color="red" icon="trash" wire:click="deleteTemplateSk">
                Hapus Template
            </flux:button>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="save" class="max-auto space-y-6">
        <flux:card>
            <flux:field>
                <flux:label>Template SK Kelulusan</flux:label>
                <label
                    class="flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-zinc-300 p-8 text-center cursor-pointer transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900">
                    <flux:icon.arrow-up-tray class="size-10 text-zinc-400" />
                    <div>
                        <p class="font-medium">Klik untuk memilih file SK Kelulusan</p>
                        <p class="text-sm text-zinc-500">Maksimal ukuran file 1 MB</p>
                    </div>
                    <input type="file" wire:model="file_sk" accept=".docx" class="hidden" />
                </label>

                {{-- Loading Upload --}}
                <div wire:loading wire:target="file_sk" class="mt-3">
                    <flux:badge color="blue">Mengunggah file...</flux:badge>
                </div>

                {{-- Preview File --}}
                @if ($file_sk)
                    <flux:callout color="green" class="mt-3"> File dipilih:
                        <strong>{{ $file_sk->getClientOriginalName() }}</strong>
                    </flux:callout>
                @endif
                <flux:error name="file_sk" />
            </flux:field>
        </flux:card>

        {{-- Action --}}
        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" wire:click="$refresh">Reset</flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Simpan SK</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </flux:button>
        </div>
    </form>
</div>
