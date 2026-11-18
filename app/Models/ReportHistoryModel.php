<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportHistoryModel extends Model
{
    protected $table         = 'report_histories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;   // riwayat adalah catatan tetap; waktunya ada di kolom "waktu"
    protected $allowedFields = ['report_id', 'status', 'catatan', 'changed_by', 'waktu'];

    /**
     * Riwayat satu laporan, urut dari yang paling awal.
     */
    public function forReport(int $reportId): array
    {
        return $this->select('status, catatan, waktu')
            ->where('report_id', $reportId)
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}