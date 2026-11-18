<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class UiHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('ui');
    }

    public function testFormatTanggalIndonesia(): void
    {
        $this->assertSame('12 Nov 2026', cc_date_id('2026-11-12 09:15:00'));
        $this->assertSame('12 Nov 2026, 09:15', cc_datetime_id('2026-11-12 09:15:00'));
        $this->assertSame('-', cc_date_id('bukan tanggal'));
    }

    public function testDaftarStatusLengkap(): void
    {
        $this->assertSame(['menunggu', 'diproses', 'selesai', 'ditolak'], array_keys(cc_status_list()));
    }

    public function testBadgeStatusMemuatIkonDanTeks(): void
    {
        $html = cc_status_badge('selesai');

        $this->assertStringContainsString('cc-badge--done', $html);
        $this->assertStringContainsString('Selesai', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html); // ikon dekoratif
    }

    public function testBadgeStatusTidakDikenalAman(): void
    {
        $html = cc_status_badge('<script>alert(1)</script>');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}