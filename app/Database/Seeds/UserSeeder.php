<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@kampus.ac.id';

        // Aman dijalankan berulang: akun yang sudah ada tidak diubah
        $sudahAda = $this->db->table('users')->where('email', $email)->countAllResults();
        if ($sudahAda > 0) {
            CLI::write('Akun admin sudah ada, dilewati.', 'yellow');

            return;
        }

        // Password dari .env (admin.password) atau dibuat acak
        $dariEnv    = (string) env('admin.password', '');
        $dibuatAcak = $dariEnv === '';
        $password   = $dibuatAcak ? $this->buatPasswordAcak() : $dariEnv;

        if (strlen($password) < 8) {
            CLI::error('admin.password di .env minimal 8 karakter. Akun admin tidak dibuat.');

            return;
        }

        $this->db->table('users')->insert([
            'nama'       => 'Admin Assignment Hub',
            'nim'        => null,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'role'       => 'admin',
            'status'     => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        CLI::write('Akun admin dibuat: ' . $email, 'green');

        if ($dibuatAcak) {
            CLI::write('Password admin (catat sekarang, tidak akan ditampilkan lagi): ' . $password, 'green');
        }
    }

    /**
     * Password acak berisi huruf dan angka.
     */
    private function buatPasswordAcak(int $panjang = 14): string
    {
        $huruf = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $angka = '23456789';
        $semua = $huruf . $angka;

        // Pastikan minimal ada satu huruf dan satu angka
        $hasil = $huruf[random_int(0, strlen($huruf) - 1)]
               . $angka[random_int(0, strlen($angka) - 1)];

        for ($i = strlen($hasil); $i < $panjang; $i++) {
            $hasil .= $semua[random_int(0, strlen($semua) - 1)];
        }

        return str_shuffle($hasil);
    }
}
