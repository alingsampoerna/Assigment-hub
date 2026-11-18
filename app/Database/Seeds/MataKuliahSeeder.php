<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

/**
 * Contoh data mata kuliah untuk development.
 * Jalankan: php spark db:seed MataKuliahSeeder
 */
class MataKuliahSeeder extends Seeder
{
    private array $data = [
        ['kode' => 'IF101', 'nama' => 'Algoritma dan Pemrograman',       'deskripsi' => 'Dasar algoritma dan logika pemrograman.'],
        ['kode' => 'IF102', 'nama' => 'Basis Data',                       'deskripsi' => 'Perancangan dan implementasi basis data relasional.'],
        ['kode' => 'IF103', 'nama' => 'Pemrograman Web',                  'deskripsi' => 'Pengembangan aplikasi berbasis web front-end dan back-end.'],
        ['kode' => 'IF201', 'nama' => 'Struktur Data',                    'deskripsi' => 'Array, linked list, tree, graph, dan kompleksitas algoritma.'],
        ['kode' => 'IF202', 'nama' => 'Rekayasa Perangkat Lunak',         'deskripsi' => 'Proses pengembangan perangkat lunak terstruktur.'],
        ['kode' => 'IF301', 'nama' => 'Kecerdasan Buatan',                'deskripsi' => 'Dasar-dasar AI, machine learning, dan penerapannya.'],
        ['kode' => 'IF302', 'nama' => 'Jaringan Komputer',                'deskripsi' => 'Protokol jaringan, TCP/IP, dan keamanan jaringan.'],
        ['kode' => 'MK101', 'nama' => 'Matematika Diskrit',               'deskripsi' => 'Logika, himpunan, kombinatorik, dan teori graf.'],
        ['kode' => 'MK102', 'nama' => 'Kalkulus',                         'deskripsi' => 'Limit, turunan, integral, dan aplikasinya.'],
        ['kode' => 'MK201', 'nama' => 'Statistika dan Probabilitas',      'deskripsi' => 'Distribusi probabilitas, uji hipotesis, dan regresi.'],
    ];

    public function run(): void
    {
        $now    = date('Y-m-d H:i:s');
        $dibuat = 0;

        foreach ($this->data as $mk) {
            $sudahAda = $this->db->table('mata_kuliah')
                ->where('kode_mata_kuliah', $mk['kode'])
                ->countAllResults();

            if ($sudahAda > 0) {
                CLI::write('Sudah ada, dilewati: ' . $mk['kode'], 'yellow');
                continue;
            }

            $this->db->table('mata_kuliah')->insert([
                'kode_mata_kuliah' => $mk['kode'],
                'nama_mata_kuliah' => $mk['nama'],
                'deskripsi'        => $mk['deskripsi'],
                'status'           => 'aktif',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            CLI::write('Dibuat: ' . $mk['kode'] . ' — ' . $mk['nama'], 'green');
            $dibuat++;
        }

        CLI::write('Selesai. ' . $dibuat . ' mata kuliah ditambahkan.', 'green');
    }
}
