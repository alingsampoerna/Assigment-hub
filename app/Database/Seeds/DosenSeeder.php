<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

/**
 * Buat akun dosen contoh.
 * Jalankan: php spark db:seed DosenSeeder
 *
 * Password diambil dari .env (dosen.password) atau dibuat acak.
 * Akun yang sudah ada (berdasarkan email) tidak diubah.
 */
class DosenSeeder extends Seeder
{
    private array $contohDosen = [
        ['nama' => 'Dr. Hendra Wijaya, M.Kom',   'email' => 'hendra@kampus.ac.id'],
        ['nama' => 'Dra. Siti Rahayu, M.Pd',      'email' => 'siti.rahayu@kampus.ac.id'],
        ['nama' => 'Prof. Budi Santoso, Ph.D',     'email' => 'budi.santoso@kampus.ac.id'],
    ];

    public function run(): void
    {
        $defaultPass = (string) env('dosen.password', '');
        $pakai_acak  = $defaultPass === '';

        $dibuat = 0;
        foreach ($this->contohDosen as $d) {
            $sudahAda = $this->db->table('users')
                ->where('email', $d['email'])
                ->countAllResults();

            if ($sudahAda > 0) {
                CLI::write('Dosen sudah ada, dilewati: ' . $d['email'], 'yellow');
                continue;
            }

            $password = $pakai_acak ? $this->buatPassword() : $defaultPass;

            if (strlen($password) < 8) {
                CLI::error('dosen.password di .env minimal 8 karakter. Dosen ' . $d['email'] . ' tidak dibuat.');
                continue;
            }

            $this->db->table('users')->insert([
                'nama'       => $d['nama'],
                'nim'        => null,
                'email'      => $d['email'],
                'password'   => password_hash($password, PASSWORD_DEFAULT), // hash manual karena insert langsung ke DB
                'role'       => 'dosen',
                'status'     => 'aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            CLI::write('Dosen dibuat: ' . $d['email'] . ($pakai_acak ? ' | Password: ' . $password : ''), 'green');
            $dibuat++;
        }

        if ($dibuat === 0) {
            CLI::write('Tidak ada dosen baru yang dibuat.', 'yellow');
        }
    }

    private function buatPassword(int $panjang = 12): string
    {
        $huruf = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $angka = '23456789';
        $semua = $huruf . $angka;

        $hasil = $huruf[random_int(0, strlen($huruf) - 1)]
               . $angka[random_int(0, strlen($angka) - 1)];

        for ($i = strlen($hasil); $i < $panjang; $i++) {
            $hasil .= $semua[random_int(0, strlen($semua) - 1)];
        }

        return str_shuffle($hasil);
    }
}
