<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambahkan kolom status ke tabel users yang sudah ada.
 * Kolom diletakkan setelah kolom role.
 */
class AddStatusToUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
                'null'       => false,
                'after'      => 'role',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'status');
    }
}
