<?php

namespace App\Livewire;

use App\Models\Siswa;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
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
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                Flux::toast(
                    heading: 'Gagal',
                    variant: 'danger',
                    text: 'File Excel kosong.'
                );

                return;
            }

            $imported = 0;

            DB::transaction(function () use ($rows, &$imported) {
                foreach ($rows as $index => $row) {
                    if ($index === 0) {
                        continue;
                    }

                    $nis = isset($row[0]) ? trim((string) $row[0]) : '';
                    if ($nis === '') {
                        continue;
                    }

                    $noUjian = isset($row[3]) ? trim((string) $row[3]) : '';
                    if ($noUjian === '') {
                        $noUjian = 'NO-UJIAN-' . $nis;
                    }

                    $data = [
                        'nis' => $nis,
                        'nisn' => isset($row[1]) ? trim((string) $row[1]) : '',
                        'nama' => isset($row[2]) ? trim((string) $row[2]) : '',
                        'no_ujian' => $noUjian,
                        'kompetensi_keahlian' => isset($row[4]) ? trim((string) $row[4]) : '',
                        'status' => isset($row[5]) ? trim((string) $row[5]) : '',
                    ];

                    $existing = Siswa::where('nis', $data['nis'])->first();
                    if ($existing) {
                        $existing->fill($data);
                        $existing->save();
                    } else {
                        Siswa::create($data);
                    }

                    $imported++;
                }
            });

            $this->reset('file');

            Flux::toast(
                heading: 'Berhasil',
                variant: 'success',
                text: $imported > 0
                    ? "Data siswa berhasil diimpor ({$imported} data)."
                    : 'Tidak ada data siswa yang diimpor.'
            );
        } catch (\Throwable $e) {
            Flux::toast(
                heading: 'Gagal',
                variant: 'danger',
                text: 'Import gagal: ' . $e->getMessage()
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
