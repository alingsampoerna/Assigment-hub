<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ReportModel;

class Statistik extends BaseController
{
    private const NAMA_BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(): string
    {
        $model      = new ReportModel();
        $kategori   = (new CategoryModel())->daftar();
        $statusList = cc_status_list();

        // Bulan yang tersedia (diambil dari data), terbaru di atas
        $perBulan   = $model->perBulan();
        $kunciBulan = array_keys($perBulan);
        rsort($kunciBulan);

        $opsiPeriode = ['semua' => 'Semua waktu'];
        foreach ($kunciBulan as $ym) {
            $opsiPeriode[$ym] = $this->labelBulan((string) $ym);
        }

        $periode = $this->getString('periode');
        if (! isset($opsiPeriode[$periode])) {
            $periode = 'semua';
        }

        // Rentang waktu: [awal (termasuk), akhir (tidak termasuk)]
        $awal  = null;
        $akhir = null;
        if ($periode !== 'semua') {
            $awal  = $periode . '-01 00:00:00';
            $akhir = (new \DateTime($periode . '-01'))->modify('first day of next month')->format('Y-m-d') . ' 00:00:00';
        }

        $stats = $model->statistik($awal, $akhir);
        $total = $stats['total'];

        // Per status
        $fillStatus = ['menunggu' => 'warning', 'diproses' => '', 'selesai' => 'success', 'ditolak' => 'danger'];
        $itemStatus = [];
        foreach ($statusList as $nilai => $label) {
            $itemStatus[] = ['label' => $label, 'jumlah' => $stats[$nilai], 'fill' => $fillStatus[$nilai] ?? ''];
        }

        // Per kategori
        $itemKategori = array_map(
            static fn (array $item): array => ['label' => $item['nama'], 'jumlah' => $item['jumlah']],
            $model->perKategori($kategori, $awal, $akhir)
        );

        // Tren per bulan (seluruh data, urut dari bulan terlama)
        $itemTren = [];
        foreach ($perBulan as $ym => $jumlah) {
            $itemTren[] = ['label' => $this->labelBulan((string) $ym), 'jumlah' => $jumlah];
        }

        return view('pages/admin/statistik', [
            'title'          => 'Statistik - CampusCare Admin',
            'activeNav'      => 'statistik',
            'periode'        => $periode,
            'opsiPeriode'    => $opsiPeriode,
            'labelPeriode'   => $opsiPeriode[$periode],
            'total'          => $total,
            'perStatus'      => $stats,
            'tingkatSelesai' => $total > 0 ? (int) round($stats['selesai'] / $total * 100) : 0,
            'itemKategori'   => $itemKategori,
            'itemStatus'     => $itemStatus,
            'itemTren'       => $itemTren,
            'totalSemua'     => array_sum($perBulan),
        ]);
    }

    private function labelBulan(string $ym): string
    {
        [$tahun, $bulan] = explode('-', $ym);

        return (self::NAMA_BULAN[(int) $bulan] ?? $bulan) . ' ' . $tahun;
    }

    private function getString(string $key): string
    {
        $value = $this->request->getGet($key);

        return is_string($value) ? trim($value) : '';
    }
}