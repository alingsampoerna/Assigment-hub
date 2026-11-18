<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasMahasiswaTable extends Migration
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
            'kelas_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'mahasiswa_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // Satu mahasiswa hanya bisa terdaftar sekali per kelas
        $this->forge->addUniqueKey(['kelas_id', 'mahasiswa_id'], 'uq_kelas_mahasiswa');

        $this->forge->addKey('mahasiswa_id', false, false, 'idx_km_mahasiswa');

        // FK: hapus baris kelas_mahasiswa jika kelas dihapus (cascade)
        // FK: tolak penghapusan mahasiswa jika masih terdaftar di kelas (restrict)
        $this->forge->addForeignKey(
            'kelas_id', 'kelas', 'id',
            'RESTRICT', 'CASCADE',
            'fk_km_kelas'
        );
        $this->forge->addForeignKey(
            'mahasiswa_id', 'users', 'id',
            'RESTRICT', 'RESTRICT',
            'fk_km_mahasiswa'
        );

        $this->forge->createTable('kelas_mahasiswa', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('kelas_mahasiswa', true);
    }
}
