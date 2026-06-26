<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa as SiswaModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Importsiswa extends Component
{
    use WithFileUploads;

    public $file;

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        try {

            $spreadsheet = IOFactory::load($this->file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            if (count($rows) <= 1) {
                Flux::toast(
                    heading: 'Gagal',
                    variant: 'danger',
                    text: 'File Excel kosong.'
                );
                return;
            }

            DB::transaction(function () use ($rows) {
                foreach ($rows as $index => $row) {

                    // Skip header
                    if ($index == 0) {
                        continue;
                    }

                    // Skip baris kosong
                    if (empty($row[0])) {
                        continue;
                    }

                    SiswaModel::updateOrCreate(
                        [
                            'nis' => trim($row[0]),
                        ],
                        [
                            'nisn'                  => trim($row[1] ?? ''),
                            'nama'                  => trim($row[2] ?? ''),
                            'no_ujian'              => trim($row[3] ?? ''),
                            'kompetensi_keahlian'   => trim($row[4] ?? ''),
                            'status'                => trim($row[5] ?? ''),
                        ]
                    );
                }
            });

            $this->reset('file');

            Flux::toast(
                heading: 'Berhasil',
                variant: 'success',
                text: 'Data siswa berhasil diimpor.'
            );
        } catch (\Throwable $e) {
            Flux::toast(
                heading: 'Gagal',
                variant: 'danger',
                text: $e->getMessage()
            );
        }
    }

    public function downloadTemplateSiswa()
    {
        $path = public_path('templates/template-siswa.xlsx');

        // Cek file template
        if (!file_exists($path)) {

            Flux::toast(
                heading: 'Peringatan',
                text: 'Template siswa belum tersedia.',
                variant: 'warning',
            );

            return;
        }

        return response()->download(
            $path,
            'template-siswa.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.importsiswa');
    }
}
