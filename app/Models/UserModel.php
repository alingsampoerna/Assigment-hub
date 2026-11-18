<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['nama', 'nim', 'email', 'password', 'role', 'status'];

    // Password selalu di-hash otomatis sebelum disimpan.
    // Penting: jangan pernah mengirim password yang SUDAH di-hash ke insert()/update().
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    /**
     * Cari user berdasarkan email. Hanya mengembalikan akun yang berstatus aktif.
     */
    public function cariByEmail(string $email): ?array
    {
        return $this->where('email', $email)
                    ->where('status', 'aktif')
                    ->first();
    }

    /**
     * Daftar mahasiswa aktif, diurutkan berdasarkan nama.
     */
    public function daftarMahasiswa(string $q = ''): array
    {
        $builder = $this->where('role', 'mahasiswa')
                        ->where('status', 'aktif')
                        ->orderBy('nama', 'ASC');

        if ($q !== '') {
            $builder->groupStart()
                    ->like('nama', $q)
                    ->orLike('nim', $q)
                    ->orLike('email', $q)
                    ->groupEnd();
        }

        return $builder->findAll();
    }

    /**
     * Hitung total mahasiswa (opsional filter status).
     */
    public function hitungMahasiswa(string $status = ''): int
    {
        $builder = $this->where('role', 'mahasiswa');

        if ($status !== '') {
            $builder->where('status', $status);
        }

        return (int) $builder->countAllResults();
    }
}
