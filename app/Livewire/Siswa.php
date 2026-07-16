<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Siswa as SiswaModel;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\TemplateProcessor;

class Siswa extends Component
{
    // Traits for pagination and file uploads
    use WithPagination;
    use WithFileUploads;

    // Public properties for managing state
    public $editId = null;
    public $deleteId = null;
    public $search = '';
    public $sortBy = 'nis';
    public $sortDirection = 'asc';

    // Public properties to hold the student data
    public $nis;
    public $nisn;
    public $nama;
    public $no_ujian;
    public $kompetensi_keahlian;
    public $status;
    public $file;
    public $template_sk;
    public $downloadUrl = null;
    public $downloadFileName = null;

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->reset([
            'nis',
            'nisn',
            'nama',
            'no_ujian',
            'kompetensi_keahlian',
            'status',
            'editId'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */
    public function save()
    {
        $this->validate([
            'nis' => 'required',
            'nisn' => 'required',
            'nama' => 'required',
            'no_ujian' => 'required',
            'kompetensi_keahlian' => 'required',
            'status' => 'required'
        ]);

        SiswaModel::updateOrCreate(
            ['id' => $this->editId],
            [
                'nis' => $this->nis,
                'nisn' => $this->nisn,
                'nama' => $this->nama,
                'no_ujian' => $this->no_ujian,
                'kompetensi_keahlian' => $this->kompetensi_keahlian,
                'status' => $this->status
            ]
        );

        Flux::toast(
            variant: 'success',
            text: $this->editId ? "Data siswa berhasil diperbarui." : "Data siswa berhasil ditambahkan."
        );

        $this->create();
        $this->dispatch('close-modal');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $siswa = SiswaModel::findOrFail($id);

        $this->nis = $siswa->nis;
        $this->nisn = $siswa->nisn;
        $this->nama = $siswa->nama;
        $this->no_ujian = $siswa->no_ujian;
        $this->kompetensi_keahlian = $siswa->kompetensi_keahlian;
        $this->status = $siswa->status;
        $this->editId = $id;
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm Delete
    |--------------------------------------------------------------------------
    */
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
    public function delete()
    {
        SiswaModel::findOrFail($this->deleteId)->delete();
        Flux::toast(
            variant: 'success',
            text: "Data siswa berhasil dihapus."
        );

        $this->reset('deleteId');
        $this->dispatch('close-modal');
    }

    /*
    |--------------------------------------------------------------------------
    | Download SK Word per siswa
    |--------------------------------------------------------------------------
    */
    public function downloadSK($id)
    {
        if (!file_exists(public_path('temp'))) {
            mkdir(public_path('temp'), 0777, true);
        }

        if (!file_exists(public_path('templates/template-sk.docx'))) {
            Flux::toast(
                heading: 'Peringatan',
                text: 'Template SK belum diupload, silakan upload template terlebih dahulu.',
                variant: 'warning',
            );
            return;
        }

        $siswa = SiswaModel::findOrFail($id);
        $template = new TemplateProcessor(
            public_path('templates/template-sk.docx')
        );

        $template->setValue('nis', $siswa->nis);
        $template->setValue('nisn', $siswa->nisn);
        $template->setValue('nama', strtoupper($siswa->nama));
        $template->setValue('no_peserta', $siswa->no_ujian);
        $template->setValue('kompetensi_keahlian', strtoupper($siswa->kompetensi_keahlian));
        $template->setValue('status', strtoupper($siswa->status));

        $filename = 'SK-' . $siswa->nama . '.docx';
        $path = public_path('temp/' . $filename);

        $template->saveAs($path);

        $this->downloadUrl = asset('temp/' . $filename);
        $this->downloadFileName = $filename;
        $this->dispatch('download-file', url: $this->downloadUrl, filename: $this->downloadFileName);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Download SK PDF (per siswa atau semua dalam ZIP)
    |--------------------------------------------------------------------------
    */
    public function downloadSKPDF($id = null)
    {
        if (!file_exists(public_path('templates/template-sk.docx'))) {
            Flux::toast(
                heading: 'Peringatan',
                text: 'Template SK belum diupload.',
                variant: 'warning',
            );
            return;
        }

        if (!file_exists(public_path('temp'))) {
            mkdir(public_path('temp'), 0777, true);
        }

        $siswa = SiswaModel::findOrFail($id);
        $template = new TemplateProcessor(
            public_path('templates/template-sk.docx')
        );

        $template->setValue('nis', $siswa->nis);
        $template->setValue('nisn', $siswa->nisn);
        $template->setValue('nama', strtoupper($siswa->nama));
        $template->setValue('no_peserta', $siswa->no_ujian);
        $template->setValue('kompetensi_keahlian', strtoupper($siswa->kompetensi_keahlian));
        $template->setValue('status', strtoupper($siswa->status));

        $namaFile = 'SK-' . preg_replace('/[^A-Za-z0-9._-]/', '-', strtolower($siswa->nama));
        $docxPath = public_path("temp/{$namaFile}.docx");
        $pdfPath  = public_path("temp/{$namaFile}.pdf");

        $template->saveAs($docxPath);

        $sofficePath = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';
        if (!file_exists($sofficePath)) {
            Flux::toast(
                heading: 'Peringatan',
                text: 'LibreOffice belum terinstal atau path tidak ditemukan.',
                variant: 'warning',
            );
            return;
        }

        $command = '"' . $sofficePath . '" --headless --convert-to pdf "' . $docxPath . '" --outdir "' . public_path('temp') . '"';
        exec($command, $output, $exitCode);

        $attempt = 0;
        while (!file_exists($pdfPath) && $attempt < 10) {
            usleep(500000);
            $attempt++;
        }

        if (!file_exists($pdfPath) || $exitCode !== 0) {
            Flux::toast(
                heading: 'Gagal',
                text: 'File PDF gagal dibuat. Periksa instalasi LibreOffice.',
                variant: 'danger',
            );
            return;
        }

        $this->downloadUrl = asset('temp/' . basename($pdfPath));
        $this->downloadFileName = 'SK_' . preg_replace('/[^A-Za-z0-9._-]/', '-', strtolower($siswa->nama)) . '.pdf';
        $this->dispatch('download-file', url: $this->downloadUrl, filename: $this->downloadFileName);

        Flux::toast(
            heading: 'Berhasil',
            text: 'SK ' . $siswa->nama . ' berhasil dipersiapkan untuk diunduh.',
            variant: 'success',
        );

        if (file_exists($docxPath)) {
            unlink($docxPath);
        }

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Export Excel
    |--------------------------------------------------------------------------
    */
    public function exportExcel()
    {
        $siswas = SiswaModel::all();

        $exportData = $siswas->map(function ($siswa) {
            return [
                'NIS' => $siswa->nis,
                'NISN' => $siswa->nisn,
                'Nama' => $siswa->nama,
                'No Ujian' => $siswa->no_ujian,
                'Kompetensi Keahlian' => $siswa->kompetensi_keahlian,
                'Status' => $siswa->status,
            ];
        });

        return Excel::download(
            new class($exportData) implements FromCollection, WithHeadings {

                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function collection()
                {
                    return collect($this->data);
                }

                public function headings(): array
                {
                    return [
                        [
                            'DATA SISWA'
                        ],
                        [],
                        [
                            'NIS',
                            'NISN',
                            'Nama',
                            'No Ujian',
                            'Kompetensi Keahlian',
                            'Status',
                        ]
                    ];
                }
            },

            'data-siswa.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Export PDF
    |--------------------------------------------------------------------------
    */
    public function exportPDF()
    {
        $siswas = SiswaModel::all();
        $html   = '<h2 style="text-align:center;">DATA SISWA</h2>
                    <table width="100%" border="1" cellspacing="0" cellpadding="5">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>No Ujian</th>
                                <th>Kompetensi Keahlian</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
        foreach ($siswas as $index => $siswa) {
            $html .= '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . $siswa->nis . '</td>
                            <td>' . $siswa->nisn . '</td>
                            <td>' . $siswa->nama . '</td>
                            <td>' . $siswa->no_ujian . '</td>
                            <td>' . $siswa->kompetensi_keahlian . '</td>
                            <td>' . $siswa->status . '</td>
                        </tr>';
        }

        $html .= '</tbody>
                </table>';

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'data-siswa.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset pagination saat search
    |--------------------------------------------------------------------------
    */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Siswas Data
    |--------------------------------------------------------------------------
    */
    public function getSiswasProperty()
    {
        return SiswaModel::where('nama', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    /*
    |--------------------------------------------------------------------------
    | Sort
    |--------------------------------------------------------------------------
    */
    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('livewire.siswa');
    }
}
