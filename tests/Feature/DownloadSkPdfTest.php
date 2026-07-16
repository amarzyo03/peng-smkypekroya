<?php

use App\Livewire\Siswa;

describe('download SK PDF', function () {
    it('resolves libreoffice from the environment path', function () {
        $tempDir = sys_get_temp_dir() . '/libreoffice-test-' . uniqid('', true);
        mkdir($tempDir, 0777, true);

        $fakeSoffice = $tempDir . DIRECTORY_SEPARATOR . 'soffice.exe';
        file_put_contents($fakeSoffice, '#!/bin/sh\n');
        chmod($fakeSoffice, 0755);

        putenv('LIBREOFFICE_PATH=');
        putenv('PATH=' . $tempDir);

        $component = new Siswa();
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('resolveLibreOfficePath');
        $method->setAccessible(true);

        $result = $method->invoke($component);

        expect($result)->toBe($fakeSoffice);

        unlink($fakeSoffice);
        rmdir($tempDir);
        putenv('PATH');
        putenv('LIBREOFFICE_PATH');
    });
});
