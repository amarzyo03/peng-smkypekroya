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

    public function save()
    {
        $this->validate();
        try {
            $destination = public_path('templates');

            // Buat folder templates jika belum ada
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $filePath = $destination . DIRECTORY_SEPARATOR . 'SK_Kelulusan.docx';

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
