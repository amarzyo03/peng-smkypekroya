<?php

use App\Livewire\Importsiswa;

describe('Importsiswa component', function () {
    it('exposes the import method for the form action', function () {
        $component = new Importsiswa();

        expect(method_exists($component, 'import'))->toBeTrue();
    });
});
