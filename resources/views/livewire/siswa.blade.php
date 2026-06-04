<div class="relative mb-6 w-full">
    {{-- Heading --}}
    <flux:heading size="xl" level="1">{{ __('Siswa') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Halaman manage siswa') }}</flux:subheading>
    <flux:separator variant="subtle" class="my-6" />

    {{-- Control --}}
    <div class="flex w-full items-center gap-1 mb-2">

        {{-- Export Button --}}
        <flux:dropdown>
            <flux:button variant="primary" color="rose" size="sm">Export</flux:button>
            <flux:menu>
                <flux:menu.item wire:click="exportExcel" icon="document-text">
                    Excel (.xlsx)
                </flux:menu.item>
                <flux:menu.item wire:click="exportPDF" icon="document-text">
                    PDF (.pdf)
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        {{-- Upload SK Button --}}
        <flux:modal.trigger name="modal-upload-sk-template">
            <flux:button variant="primary" color="red" size="sm">
                Upload SK
            </flux:button>
        </flux:modal.trigger>

        {{-- Import Button --}}
        <flux:modal.trigger name="modal-upload-data-siswa">
            <flux:button variant="primary" color="emerald" size="sm">
                Import
            </flux:button>
        </flux:modal.trigger>

        {{-- Add Button --}}
        <flux:modal.trigger name="modal-tambah-siswa">
            <flux:button variant="primary" color="blue" size="sm" wire:click="create">
                Tambah
            </flux:button>
        </flux:modal.trigger>

    </div>

    {{-- Search --}}
    <flux:input icon="magnifying-glass" placeholder="Pencarian" size="sm"
        wire:model.live.debounce.300ms="search" />

    {{-- Separator --}}
    <flux:separator variant="subtle" class="mt-6" />

    {{-- Table Siswa --}}
    <flux:table :paginate="$this->siswas">
        <flux:table.columns>
            <flux:table.column>#</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'nis'" :direction="$sortDirection"
                wire:click="sort('nis')">NIS</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'nisn'" :direction="$sortDirection"
                wire:click="sort('nisn')">NISN</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'nama'" :direction="$sortDirection"
                wire:click="sort('nama')">Nama</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'no_ujian'" :direction="$sortDirection"
                wire:click="sort('no_ujian')">No Ujian</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'kompetensi_keahlian'" :direction="$sortDirection"
                wire:click="sort('kompetensi_keahlian')">Kompetensi</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'SK PDF'" :direction="$sortDirection"
                wire:click="sort('SK PDF')">SK PDF</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->siswas as $row)
                <flux:table.row>
                    <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                    <flux:table.cell>{{ $row->nis }}</flux:table.cell>
                    <flux:table.cell>{{ $row->nisn }}</flux:table.cell>
                    <flux:table.cell>{{ strtoupper($row->nama) }}</flux:table.cell>
                    <flux:table.cell>{{ $row->no_ujian }}</flux:table.cell>
                    <flux:table.cell>{{ strtoupper($row->kompetensi_keahlian) }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="solid" color="{{ $row->status == 'lulus' ? 'green' : 'red' }}"
                            size="sm" inset="top bottom">{{ strtoupper($row->status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:tooltip content="Download SK PDF" position="top">
                            <flux:button size="sm" variant="primary" color="red" icon="arrow-down-tray"
                                wire:click="downloadSKPDF({{ $row->id }})" wire:loading.attr="disabled"
                                wire:target="downloadSKPDF({{ $row->id }})">
                                <div class="flex items-center gap-2">
                                    <span wire:loading.remove wire:target="downloadSKPDF({{ $row->id }})">
                                        SK
                                    </span>
                                    <span wire:loading wire:target="downloadSKPDF({{ $row->id }})"
                                        class="flex items-center gap-2">
                                        <flux:icon.arrow-path class="size-4 animate-spin" />
                                        Converting...
                                    </span>
                                </div>
                            </flux:button>
                        </flux:tooltip>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                            <flux:menu>

                                <flux:modal.trigger name="siswa-modal">
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $row->id }})">
                                        Edit
                                    </flux:menu.item>
                                </flux:modal.trigger>
                                <flux:modal.trigger name="delete-siswa">
                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $row->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center">
                        Tidak ada data siswa.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal add and edit --}}
    <flux:modal name="modal-tambah-siswa" class="min-w-[22rem]"
        x-on:close-modal.window="$flux.modal('modal-tambah-siswa').close()">
        <div class="space-y-6">

            {{-- Heading modal --}}
            <div>
                <flux:heading size="lg">
                    {{ $editId ? 'Edit Siswa' : 'Tambah Siswa' }}
                </flux:heading>
                <flux:text class="mt-2">
                    Manage siswa details.
                </flux:text>
            </div>

            {{-- Form input --}}
            <flux:input label="NIS" placeholder="Masukkan NIS" size="sm" wire:model="nis" autofocus />
            <flux:input label="NISN" placeholder="Masukkan NISN" size="sm" wire:model="nisn" />
            <flux:input label="Nama" placeholder="Masukkan Nama" size="sm" wire:model="nama" />
            <flux:input label="No Ujian" placeholder="Masukkan No Ujian" size="sm" wire:model="no_ujian" />
            <flux:select label="Kompetensi Keahlian" size="sm" wire:model="kompetensi_keahlian">
                <flux:select.option value="bisnis retail / bisnis digital">Bisnis Retail / Bisnis Digital
                </flux:select.option>
                <flux:select.option value="akuntansi">Akuntansi</flux:select.option>
                <flux:select.option value="manajemen perkantoran">Manajemen Perkantoran</flux:select.option>
                <flux:select.option value="teknik komputer dan jaringan">Teknik Komputer dan Jaringan
                </flux:select.option>
                <flux:select.option value="teknik kendaraan ringan">Teknik Kendaraan Ringan</flux:select.option>
            </flux:select>
            <flux:select label="Status" size="sm" wire:model="status">
                <flux:select.option value="lulus">Lulus</flux:select.option>
                <flux:select.option value="tidak lulus">Tidak Lulus</flux:select.option>
            </flux:select>

            {{-- Control input --}}
            <div class="flex items-center gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">
                        Cancel
                    </flux:button>
                </flux:modal.close>
                <flux:button wire:click="save" wire:loading.attr="disabled" variant="primary" color="blue"
                    size="sm" class="w-full">
                    <span wire:loading.remove wire:target="save">
                        {{ $editId ? 'Update' : 'Save' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </flux:button>
            </div>

        </div>
    </flux:modal>

    {{-- Modal delete --}}
    <flux:modal name="delete-siswa" class="min-w-[22rem]"
        x-on:close-modal.window="$flux.modal('delete-siswa').close()">
        <div class="space-y-6">

            {{-- Modal heading --}}
            <div>
                <flux:heading size="lg">
                    Delete siswa?
                </flux:heading>
                <flux:text class="mt-2">
                    Proses ini akan menghapus data siswa secara permanen.
                    <br>
                    Tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>

            {{-- Modal control --}}
            <div class="flex gap-2">
                <flux:spacer />

                {{-- Cancel --}}
                <flux:modal.close>
                    <flux:button variant="ghost">
                        Batal
                    </flux:button>
                </flux:modal.close>

                {{-- Delete --}}
                <flux:button wire:click="delete" wire:loading.attr="disabled" variant="danger">
                    <span wire:loading.remove wire:target="delete">
                        Hapus!
                    </span>
                    <span wire:loading wire:target="delete">
                        Proses hapus...
                    </span>
                </flux:button>

            </div>
        </div>
    </flux:modal>

    {{-- Modal upload data siswa --}}
    <flux:modal name="modal-upload-data-siswa" class="min-w-[22rem]"
        x-on:close-modal.window="$flux.modal('modal-upload-data-siswa').close()">
        <div class="space-y-6">

            {{-- Modal heading --}}
            <div>
                <flux:heading size="lg">
                    Upload data siswa
                </flux:heading>
                <flux:text class="mt-2">
                    Pilih file Excel (.xlsx) yang berisi data siswa untuk diunggah.
                    <br>
                    Pastikan format file sesuai dengan template yang disediakan.
                </flux:text>
                {{-- download template --}}
                <flux:button variant="primary" color="emerald" size="sm" class="mt-3 w-full"
                    wire:click="downloadTemplateSiswa">
                    Download template
                </flux:button>
            </div>

            {{-- Form input --}}
            <flux:input type="file" label="Pilih file Excel" size="sm" wire:model="file" accept=".xlsx" />

            {{-- Control input --}}
            <div class="flex items-center gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">
                        Cancel
                    </flux:button>
                </flux:modal.close>
                <flux:button wire:click="import" wire:loading.attr="disabled" variant="primary" color="emerald"
                    size="sm" class="w-full">
                    <span wire:loading.remove wire:target="import">
                        Upload
                    </span>
                    <span wire:loading wire:target="import">
                        Uploading...
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal upload template SK --}}
    <flux:modal name="modal-upload-sk-template" class="min-w-[22rem]"
        x-on:close-modal.window="$flux.modal('modal-upload-sk-template').close()">
        <div class="space-y-6">

            {{-- Modal heading --}}
            <div>
                <flux:heading size="lg">
                    Upload template SK
                </flux:heading>
                <flux:text class="mt-2">
                    Pilih file Word (.docx) yang akan dijadikan template untuk generate SK siswa.
                    <br>
                    Pastikan file memiliki placeholder yang sesuai dengan format yang dibutuhkan.
                </flux:text>
            </div>

            {{-- Form input --}}
            <flux:input type="file" label="Pilih file Word" size="sm" wire:model="template_sk"
                accept=".docx" />

            {{-- Control input --}}
            <div class="flex items-center gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">
                        Cancel
                    </flux:button>
                </flux:modal.close>
                <flux:button wire:click="uploadTemplateSK" wire:loading.attr="disabled"
                    wire:target="template_sk,uploadTemplateSK" variant="primary" color="emerald" size="sm"
                    class="w-full">
                    <span wire:loading.remove wire:target="template_sk,uploadTemplateSK">
                        Upload
                    </span>
                    <span wire:loading wire:target="template_sk,uploadTemplateSK">
                        Uploading...
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
