<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfficersTable extends Migration
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
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
            ],
            'kontak' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nama', 'uq_officers_nama');

        $this->forge->createTable('officers', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('officers', true);
    }
}