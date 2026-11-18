<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Test akses halaman oleh tamu (belum login).
 *
 * @internal
 */
final class AksesTamuTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    /** Landing page harus bisa dibuka dan menampilkan konten Assignment Hub. */
    public function testBerandaDapatDibuka(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        $result->assertSee('Assignment Hub');
    }

    /** Halaman login dan register harus bisa dibuka oleh tamu. */
    public function testHalamanLoginDanRegisterDapatDibuka(): void
    {
        $this->get('login')->assertOK();
        $this->get('register')->assertOK();
    }

    /** Semua halaman mahasiswa harus mengalihkan tamu ke login. */
    public function testAreaMahasiswaMengalihkanTamuKeLogin(): void
    {
        $paths = [
            'mahasiswa/dashboard',
            'mahasiswa/profil',
            'mahasiswa/kelas',
            'mahasiswa/tugas',
            'mahasiswa/notifikasi',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertRedirectTo(site_url('login'));
        }
    }

    /** Semua halaman admin harus mengalihkan tamu ke login. */
    public function testAreaAdminMengalihkanTamuKeLogin(): void
    {
        $paths = [
            'admin/dashboard',
            'admin/mata-kuliah',
            'admin/kelas',
            'admin/mahasiswa',
            'admin/tugas',
            'admin/pengaturan',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertRedirectTo(site_url('login'));
        }
    }
}
