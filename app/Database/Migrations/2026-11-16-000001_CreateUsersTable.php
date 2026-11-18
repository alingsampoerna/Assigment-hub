<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
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
            'nim' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,   // admin tidak punya NIM
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,    // hasil password_hash(), jangan diperpendek
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['mahasiswa', 'admin'],
                'default'    => 'mahasiswa',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nim', 'uq_users_nim');     // beberapa NULL tetap diperbolehkan
        $this->forge->addUniqueKey('email', 'uq_users_email');

        $this->forge->createTable('users', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('users', true);
    }
}