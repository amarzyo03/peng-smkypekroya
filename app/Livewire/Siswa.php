<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa as SiswaModel;

class Siswa extends Component
{
    // Pagination
    use WithPagination;

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
    | Download Template
    |--------------------------------------------------------------------------
    */
    public function downloadTemplate()
    {
        return response()->download(
            public_path('templates/template-siswa.xlsx')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */
    public function import()
    {
        // Implementasi logika import data siswa dari file Excel
        // Anda bisa menggunakan package seperti Maatwebsite Excel untuk mempermudah proses ini

        Flux::toast(
            variant: 'success',
            text: "Data siswa berhasil diimpor."
        );

        $this->dispatch('close-modal');
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
