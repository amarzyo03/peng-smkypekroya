<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;

class Uploadsk extends Component
{
    use WithFileUploads;

    public $file_sk;

    protected $rules    = ['file_sk' => 'required|file|mimes:docx|max:10240'];
    protected $messages = [
        'file_sk.required' => 'Silakan pilih file template SK.',
        'file_sk.mimes'    => 'File harus berformat DOCX.',
        'file_sk.max'      => 'Ukuran file maksimal 10 MB.',
    ];

    public function getTemplateSkStatusProperty()
    {
        $path = public_path('templates/template-sk.docx');

        if (file_exists($path)) {
            return [
                'uploaded' => true,
                'message' => 'Template SK sudah diupload: ' . basename($path),
            ];
        }

        return [
            'uploaded' => false,
            'message' => 'Template SK belum diupload.',
        ];
    }

    public function deleteTemplateSk()
    {
        $path = public_path('templates/template-sk.docx');

        if (!file_exists($path)) {
            Flux::toast(
                heading: 'Info',
                text: 'Template SK belum diupload.',
                variant: 'info',
            );

            return;
        }

        File::delete($path);

        Flux::toast(
            heading: 'Berhasil',
            text: 'Template SK berhasil dihapus.',
            variant: 'success',
        );
    }

    public function cleanupTempSkFiles()
    {
        $tempDir = public_path('temp');

        if (!is_dir($tempDir)) {
            Flux::toast(
                heading: 'Info',
                text: 'Folder temp belum ada.',
                variant: 'info',
            );

            return;
        }

        $deleted = 0;
        $files = File::files($tempDir);

        foreach ($files as $file) {
            $filename = basename($file);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (preg_match('/^sk-/i', $filename) && in_array($extension, ['pdf', 'docx', 'doc'], true)) {
                File::delete($file);
                $deleted++;
            }
        }

        Flux::toast(
            heading: 'Berhasil',
            text: $deleted > 0
                ? "Berhasil membersihkan {$deleted} file SK temp."
                : 'Tidak ada file SK temp yang perlu dibersihkan.',
            variant: 'success',
        );
    }

    public function save()
    {
        $this->validate();
        try {
            $destination = public_path('templates');

            // Buat folder templates jika belum ada
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $filePath = $destination . DIRECTORY_SEPARATOR . 'template-sk.docx';

            // Hapus file lama jika ada
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            // Simpan file baru
            File::copy(
                $this->file_sk->getRealPath(),
                $filePath
            );

            $this->reset('file_sk');
            Flux::toast(
                heading: 'Berhasil',
                text: 'Template SK berhasil diupload.',
                variant: 'success'
            );
        } catch (\Throwable $e) {
            report($e);
            Flux::toast(
                heading: 'Gagal',
                text: 'Gagal mengupload template SK.',
                variant: 'danger'
            );
        }
    }

    public function render()
    {
        return view('livewire.uploadsk');
    }
}
