<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePenilaianTable extends Migration
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
            'pengumpulan_id' => [   // Satu pengumpulan hanya boleh punya satu penilaian (UNIQUE)
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'admin_id' => [   // Admin yang memberikan nilai
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'nilai' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',   // Contoh: 85.50 (maks 999.99)
            ],
            'komentar' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_penilaian' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // Satu pengumpulan hanya boleh dinilai sekali
        $this->forge->addUniqueKey('pengumpulan_id', 'uq_penilaian_pengumpulan');

        $this->forge->addForeignKey(
            'pengumpulan_id', 'pengumpulan', 'id',
            'RESTRICT', 'CASCADE',
            'fk_penilaian_pengumpulan'
        );
        $this->forge->addForeignKey(
            'admin_id', 'users', 'id',
            'RESTRICT', 'RESTRICT',
            'fk_penilaian_admin'
        );

        $this->forge->createTable('penilaian', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('penilaian', true);
    }
}
