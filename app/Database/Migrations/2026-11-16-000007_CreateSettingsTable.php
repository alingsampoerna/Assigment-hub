<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'kunci' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nilai' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('kunci', true);

        $this->forge->createTable('settings', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('settings', true);
    }
}