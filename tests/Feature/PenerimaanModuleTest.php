<?php

namespace Tests\Feature;

use Tests\TestCase;

class PenerimaanModuleTest extends TestCase
{
    public function test_dashboard_shows_penerimaan_layout_cards_and_table(): void
    {
        $this->get('/penerimaan/dashboard')
            ->assertOk()
            ->assertSee('Modul Penerimaan')
            ->assertSee(route('penerimaan.notifikasi.index'))
            ->assertSee(route('penerimaan.profil.show'))
            ->assertDontSee('Modul Keuangan')
            ->assertSeeInOrder(['Menunggu', 'Penerimaan', 'SPK', 'Aktif', 'Penerimaan', 'selesai'])
            ->assertSeeInOrder(['No Surat', 'Nomor SPK', 'Judul Pengadaan', 'Status', 'Tanggal', 'Aksi'])
            ->assertSee('DIJALAN')
            ->assertSee('SELESAI')
            ->assertSee(route('penerimaan.dokumentasi.show', 801));
    }

    public function test_sidebar_only_has_three_links(): void
    {
        $html = $this->get('/penerimaan/dashboard')->getContent();

        $this->assertSame(1, preg_match_all('#<nav class="relative mt-\[17px\].*?</nav>#s', $html, $nav));
        $this->assertSame(3, substr_count($nav[0][0], '<a '));
    }

    public function test_detail_in_progress_shows_editable_form_modal_and_photo_actions(): void
    {
        $this->get('/penerimaan/dokumentasi/801')
            ->assertOk()
            ->assertSee('Dokumentasi Penerimaan')
            ->assertSee('Informasi Dokumen Kontrak &amp; SPK', false)
            ->assertSee('name="nama_penerima"', false)
            ->assertSee('name="unit_bagian"', false)
            ->assertSee('name="tanggal_penerimaan"', false)
            ->assertSee('name="catatan"', false)
            ->assertSee('name="dokumen_serah_terima"', false)
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('Kirim Bukti Penerimaan')
            ->assertSee('Unggah Semua')
            ->assertSee('Tambah Foto')
            ->assertSee('Hapus Foto')
            ->assertSee('Menunggu Penerimaan')
            ->assertSee('id="modal-penerimaan"', false)
            ->assertSee('Konfirmasi Bukti Penerimaan')
            ->assertSee('data-ringkasan="nama"', false)
            ->assertSee('SPK/PBJ-PLK/2026/02/001')
            ->assertDontSee('berhasil dicatat');
    }

    public function test_detail_completed_is_read_only_with_banner_and_no_actions(): void
    {
        $this->get('/penerimaan/dokumentasi/803')
            ->assertOk()
            ->assertSee('Penerimaan barang berhasil dicatat')
            ->assertSee('Hendra Wijaya')
            ->assertSee('Perlengkapan')
            ->assertSee('3 foto tersimpan')
            ->assertSee('Surat Jalan (Foto Barang)')
            ->assertDontSee('Kirim Bukti Penerimaan')
            ->assertDontSee('Unggah Semua')
            ->assertDontSee('Hapus Foto')
            ->assertDontSee('name="nama_penerima"', false)
            ->assertDontSee('type="file"', false)
            ->assertDontSee('id="modal-penerimaan"', false)
            ->assertDontSee('<form id="form-penerimaan"', false);
    }

    public function test_notification_and_profile_pages_use_penerimaan_layout(): void
    {
        foreach (['/penerimaan/notifikasi', '/penerimaan/profil'] as $url) {
            $this->get($url)->assertOk()->assertSee('Modul Penerimaan');
        }
    }

    public function test_unknown_record_returns_404(): void
    {
        $this->get('/penerimaan/dokumentasi/999')->assertNotFound();
    }
}
