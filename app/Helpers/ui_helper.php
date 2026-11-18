<?php

/**
 * Helper komponen UI CampusCare.
 * Setiap fungsi merender partial dengan saveData = false
 * supaya variabel antarpemanggilan tidak saling bocor.
 */

if (! function_exists('cc_alert')) {
    /**
     * @param string      $type    success | error | warning | info
     * @param string      $message Isi pesan
     * @param string|null $title   Judul opsional
     */
    function cc_alert(string $type, string $message, ?string $title = null): string
    {
        return view('partials/alert', [
            'type'    => $type,
            'message' => $message,
            'title'   => $title,
        ], ['saveData' => false]);
    }
}

if (! function_exists('cc_error_summary')) {
    /**
     * Ringkasan error di atas form: [nama_field => pesan].
     */
    function cc_error_summary(array $errors): string
    {
        if ($errors === []) {
            return '';
        }

        return view('partials/error_summary', ['errors' => $errors], ['saveData' => false]);
    }
}

if (! function_exists('cc_input')) {
    /**
     * Komponen input: label + kolom + hint + pesan error.
     * Parameter wajib: id, name, label.
     * Opsional: type, value, error, hint, autocomplete, inputmode, placeholder, required.
     */
    function cc_input(array $params): string
    {
        return view('partials/form_input', $params, ['saveData' => false]);
    }
}
if (! function_exists('cc_status_badge')) {
    /**
     * Badge status laporan: ikon + teks + warna (tidak hanya mengandalkan warna).
     * Nilai status: menunggu | diproses | selesai | ditolak
     */
    function cc_status_badge(string $status): string
    {
        $map = [
            'menunggu' => ['cc-badge--waiting',    'bi-clock',             'Menunggu'],
            'diproses' => ['cc-badge--processing', 'bi-gear-fill',         'Diproses'],
            'selesai'  => ['cc-badge--done',       'bi-check-circle-fill', 'Selesai'],
            'ditolak'  => ['cc-badge--rejected',   'bi-x-circle-fill',     'Ditolak'],
        ];

        if (! isset($map[$status])) {
            // Status tak dikenal: tampilkan apa adanya dengan gaya netral
            return '<span class="cc-badge cc-badge--neutral">' . esc($status) . '</span>';
        }

        [$class, $icon, $label] = $map[$status];

        return '<span class="cc-badge ' . $class . '">'
            . '<i class="bi ' . $icon . '" aria-hidden="true"></i> ' . esc($label)
            . '</span>';
    }
}

if (! function_exists('cc_date_id')) {
    /**
     * Format tanggal Indonesia singkat, contoh: "12 Nov 2026".
     */
    function cc_date_id(string $datetime): string
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $time   = strtotime($datetime);

        if ($time === false) {
            return '-';
        }

        return date('j', $time) . ' ' . $months[(int) date('n', $time) - 1] . ' ' . date('Y', $time);
    }
}
if (! function_exists('cc_textarea')) {
    /**
     * Komponen textarea: label + kolom + hint + penghitung karakter + pesan error.
     * Wajib: id, name, label.
     * Opsional: value, error, hint, rows, required, max (batas karakter; jika diisi, penghitung tampil).
     */
    function cc_textarea(array $params): string
    {
        return view('partials/form_textarea', $params, ['saveData' => false]);
    }
}

if (! function_exists('cc_select')) {
    /**
     * Komponen select: label + pilihan + hint + pesan error.
     * Wajib: id, name, label, options (array nilai => teks).
     * Opsional: selected, error, hint, placeholder, required.
     */
    function cc_select(array $params): string
    {
        return view('partials/form_select', $params, ['saveData' => false]);
    }
}

if (! function_exists('cc_datetime_id')) {
    /**
     * Format tanggal dan jam, contoh: "12 Nov 2026, 09:15".
     */
    function cc_datetime_id(string $datetime): string
    {
        $time = strtotime($datetime);

        if ($time === false) {
            return '-';
        }

        return cc_date_id($datetime) . ', ' . date('H:i', $time);
    }
}
if (! function_exists('cc_status_list')) {
    /**
     * Daftar status laporan: [nilai => label]. Satu sumber untuk seluruh aplikasi.
     */
    function cc_status_list(): array
    {
        return [
            'menunggu' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai'  => 'Selesai',
            'ditolak'  => 'Ditolak',
        ];
    }
}

if (! function_exists('cc_setting')) {
    /**
     * Membaca satu pengaturan aplikasi dari tabel settings.
     * Dibaca sekali per request; jika database tidak tersedia, dipakai nilai bawaan.
     */
    function cc_setting(string $kunci, string $bawaan = ''): string
    {
        static $semua = null;

        if ($semua === null) {
            try {
                $semua = (new \App\Models\SettingModel())->semua();
            } catch (\Throwable $e) {
                $semua = [];
            }
        }

        return (string) ($semua[$kunci] ?? $bawaan);
    }
}

if (! function_exists('cc_app_name')) {
    function cc_app_name(): string
    {
        return cc_setting('nama_aplikasi', 'Assignment Hub');
    }
}

if (! function_exists('cc_page_title')) {
    /**
     * Judul tab browser. Kata "CampusCare" pada judul dari controller diganti dengan
     * nama aplikasi dari pengaturan; judul tanpa kata itu diberi akhiran nama aplikasi.
     */
    function cc_page_title(string $title = ''): string
    {
        $nama = cc_app_name();

        if ($title === '') {
            return $nama;
        }

        // Ganti referensi nama lama jika masih ada di controller lama
        if (str_contains($title, 'CampusCare')) {
            return str_replace('CampusCare', $nama, $title);
        }

        return $title . ' - ' . $nama;
    }
}

if (! function_exists('cc_user_role')) {
    /**
     * Peran pengguna yang sedang login ('admin' atau 'mahasiswa'), atau null untuk tamu.
     */
    function cc_user_role(): ?string
    {
        $role = session()->get('role');

        return is_string($role) && $role !== '' ? $role : null;
    }
}

if (! function_exists('cc_dashboard_url')) {
    /**
     * Alamat dashboard sesuai peran, atau null untuk tamu.
     */
    function cc_dashboard_url(): ?string
    {
        return match (cc_user_role()) {
            'admin'     => site_url('admin/dashboard'),
            'mahasiswa' => site_url('mahasiswa/dashboard'),
            default     => null,
        };
    }
}