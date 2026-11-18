<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mata_kuliah_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'nama_kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'tahun_akademik' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,     // Contoh: 2026/2027
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['ganjil', 'genap'],
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('mata_kuliah_id', false, false, 'idx_kelas_mk');
        $this->forge->addKey(['tahun_akademik', 'semester'], false, false, 'idx_kelas_tahun_sem');

        // FK: kelas bergantung pada mata_kuliah
        $this->forge->addForeignKey(
            'mata_kuliah_id', 'mata_kuliah', 'id',
            'RESTRICT', 'CASCADE',
            'fk_kelas_mk'
        );

        $this->forge->createTable('kelas', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('kelas', true);
    }
}
