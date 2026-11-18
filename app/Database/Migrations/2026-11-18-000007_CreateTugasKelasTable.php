<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTugasKelasTable extends Migration
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
            'tugas_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'kelas_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // Satu tugas tidak boleh ditugaskan ke kelas yang sama lebih dari sekali
        $this->forge->addUniqueKey(['tugas_id', 'kelas_id'], 'uq_tugas_kelas');

        $this->forge->addKey('kelas_id', false, false, 'idx_tk_kelas');

        // Hapus baris ini bila tugas atau kelas dihapus
        $this->forge->addForeignKey(
            'tugas_id', 'tugas', 'id',
            'RESTRICT', 'CASCADE',
            'fk_tk_tugas'
        );
        $this->forge->addForeignKey(
            'kelas_id', 'kelas', 'id',
            'RESTRICT', 'CASCADE',
            'fk_tk_kelas'
        );

        $this->forge->createTable('tugas_kelas', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('tugas_kelas', true);
    }
}
